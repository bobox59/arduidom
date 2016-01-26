#!/usr/bin/env python
# -*- coding: UTF-8 -*-
import serial
from socket import *
import time
import subprocess
import os
import optparse
import sys
import signal
import logging
import re
import xml.dom.minidom as minidom
from Queue import Queue
from threading import Thread
__author__ = 'bmasquelier cedric02'
#
# Python script for Arduidom communication
#
to_arduino = Queue()
from_arduino = Queue()

class cmdarg_data:
	def __init__(
		self,
		configFile = "",
		action = "",
		rawcmd = "",
		device = "",
		createpid = False,
		pidfile = "",
		printout_complete = True,
		printout_csv = False
		):

		self.configFile = configFile
		self.action = action
		self.rawcmd = rawcmd
		self.device = device
		self.createpid = createpid
		self.pidfile = pidfile


class from_jeedom:
	def __init__(self, command, confirm):
		self.request = command
		self._answer = ""
		self._status = "WAITING"  # START|IN_PROGRESS|OK|TIMEOUT
		self.timeout = 10
		self.confirm = confirm
		self.start_time = int(time.time())

	def start_processing(self):
		self._status = "IN_PROGRESS"
		return self.request

	def status(self):
		# logger.debug("IN CLASS " + str(self.start_time) + " " + str(self.timeout) + " " + str(time.time()))
		if (int(time.time()) - self.start_time) >= self.timeout:
			self._status = "TIMEOUT"
		return self._status

	def finished(self):
		if self.status() == "OK" or self.status() == "TIMEOUT":
			return True
		else:
			return False

	def answer(self):
		if self.status() == "OK":
			return self._answer
		else:
			return self.request + "_BAD"

	def result(self, _answer):
		self._status = "OK"
		self._answer = _answer


class Jeedom:
	# Mode = (php|tcp)
	def __init__(self, mode, url, ip, apikey):
		self.ip = ip
		self.apikey = apikey
		# if mode == "php":
		self._Separateur = " "
		self.prefix = "nice -n 19 /usr/bin/php "
		self.prefix += "/usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php "
		self.prefix += 'api=' + apikey
		# TODO Else tcp

	def send(self, cmds):
		cmd_line = self.prefix
		for cmd in cmds:
			cmd_line += self._Separateur + cmd
		logger.debug("CLASS_JEEDOM->send [" + cmd_line + "]")
		subprocess.Popen(cmd_line, shell=True)


def cli_parser(argv=None):
	parser = optparse.OptionParser("usage: %prog -h   pour l'aide")
	parser.add_option("-l", "--loglevel", dest="loglevel", default="INFO", type="string", help="Log Level (INFO, DEBUG, ERROR")
	parser.add_option("-n", "--nodaemon", dest="nodaemon", default="no", help="Mettre -nd pour lancer en DEBUG VERBOSE")
	return parser.parse_args(argv)


def handler(options, clientsocket, clientaddr, arduID):
	global to_arduino
	logger.debug("Accepted jeedom connection from: " + str(clientaddr))
	while 1:
		jeedata = clientsocket.recv(1024)
		jeedata = jeedata.replace('\n', '')
		jeedata = jeedata.replace('\r', '')
		if not jeedata:
			break
		else:
			logger.debug("JeeDom  >> [" + jeedata + "]")
			if jeedata[0:4] == 'PING':
				logger.debug("Jeedom PING Received !")
				ping_request = from_jeedom(jeedata, "PING_OK")
				to_arduino.put(ping_request)

				while not ping_request.finished():
					time.sleep(0.1)
				answer = ping_request.answer()
				logger.debug("[" + str(answer) + "] >> JeeDom")
				clientsocket.send(answer)

			elif jeedata[0:2] == 'CP':
				cp_request = from_jeedom(jeedata, "CP_OK")
				to_arduino.put(cp_request)
				while not cp_request.finished():
					time.sleep(0.1)
				answer = cp_request.answer()
				logger.debug("[" + str(answer) + "] >> JeeDom")
				clientsocket.send(answer)

			elif jeedata[0:2] == 'SP':
				sp_request = from_jeedom(jeedata, "SP_OK")
				to_arduino.put(sp_request)
				while not sp_request.finished():
					time.sleep(0.1)
				answer = sp_request.answer()
				logger.debug("[" + str(answer) + "] >> JeeDom")
				clientsocket.send(answer)

			elif jeedata[0:2] == 'RF':  # *** Refresh Datas
				rf_request = from_jeedom(jeedata, "DATA:")
				to_arduino.put(rf_request)
				while not rf_request.finished():
					time.sleep(0.1)
				answer = rf_request.answer()
				logger.debug("[" + str(answer) + "] >> JeeDom")
				clientsocket.send(answer)
			break
	logger.debug("Close Jeedom Socket")
	clientsocket.close()


def tcpServerThread(options, threadName, arduID):
	logger.debug("Thread " + threadName + " Started.")
	addr = (options.sockethost, int(options.socketport) + arduID)
	serversocket = socket(AF_INET, SOCK_STREAM)
	serversocket.setsockopt(SOL_SOCKET, SO_REUSEADDR, 1)
	serversocket.bind(addr)
	serversocket.listen(0)
	if not serversocket:
		exit()

	while 1:
		clientsocket, clientaddr = serversocket.accept()
		worker_handler = Thread(target=handler, args=(options, clientsocket, clientaddr, arduID))
		worker_handler.setDaemon(True)
		worker_handler.start()
	logger.debug("Server Stop to listening !")
	serversocket.close()


def parse_adrduino_answer(options, line, arduID):
	line = line.replace('\n', '')
	line = line.replace('\r', '')
	if line != '':
		logger.debug("Arduino >> [" + line + "]")
		if re.search("^DBG", line) or re.search("^SP", line) or re.search("^Pin ", line):
			logger.debug("Arduino DBG >> [" + line + "]")
			test = 1

		elif line.find("DATA:") > -1:
			logger.debug("RF values => FOUND")
			pinvalue = line.rsplit(',')
			cmd = []
			cmd.append('arduid=' + str(arduID))
			cmdlog = 'PHP(DATA)=> '
			for pinnumber in range(0, len(pinvalue)):
				cmd.append(str(pinnumber) + "=" + pinvalue[pinnumber].replace("DATA:", ""))
				cmdlog += str(arduID) + ":" + str(pinnumber) + "=" + pinvalue[pinnumber].replace("DATA:", "")
			logger.debug(cmdlog)
			options.jeedom.send(cmd)

		elif line.find("DHT:") > -1:
			logger.debug("DHT values => FOUND")
			dhtvalue = line.rsplit(';')
			cmd = []
			cmd.append('arduid=' + str(arduID))
			cmdlog = "PHP(DHT)->"
			for pinnumber in range(0, len(dhtvalue)):
				if dhtvalue[pinnumber].find("nan") == -1:
					cmd.append(str(pinnumber + 501) + "=" + dhtvalue[pinnumber].replace("DHT:", ""))
					cmdlog += str(arduID) + ":" + str(pinnumber + 501) + "=" + dhtvalue[pinnumber].replace("DHT:", "")
			logger.debug(cmdlog)
			options.jeedom.send(cmd)

		elif line.find(">>") > -1 and line.find("<<") > -1:
			psplit = line.rsplit('>>')
			pinnumber = int(psplit[0])
			psplit2 = psplit[1].rsplit('<<')
			# pinvalue[pinnumber] = psplit2[0]
			cmdlog = 'PHP=> '
			cmd = []
			cmd.append('arduid=' + str(arduID))
			cmd.append(str(pinnumber) + "=" + psplit2[0])
			cmdlog += str(arduID) + ":" + str(pinnumber) + "=" + psplit2[0]
			logger.debug(cmdlog)
			options.jeedom.send(cmd)
		else:
			logger.info("NON READABLE Arduino >> [" + line + "]")


def COMServer(options, threadName, arduID):
	global to_arduino
	logger.debug("Thread " + threadName + " Started.")
	logger.info("Opening Arduino USB Port...")
	SerialPort = ""
	if arduID == 1 : SerialPort = serial.Serial(options.A1_port, 115200, timeout=0.1)
	if arduID == 2 : SerialPort = serial.Serial(options.A2_port, 115200, timeout=0.1)
	if arduID == 3 : SerialPort = serial.Serial(options.A3_port, 115200, timeout=0.1)
	if arduID == 4 : SerialPort = serial.Serial(options.A4_port, 115200, timeout=0.1)
	if arduID == 5 : SerialPort = serial.Serial(options.A5_port, 115200, timeout=0.1)
	if arduID == 6 : SerialPort = serial.Serial(options.A6_port, 115200, timeout=0.1)
	if arduID == 7 : SerialPort = serial.Serial(options.A7_port, 115200, timeout=0.1)
	if arduID == 8 : SerialPort = serial.Serial(options.A8_port, 115200, timeout=0.1)
	time.sleep(1)
	logger.debug("En attente de l'arduino (HELLO)")
	logger.debug("[" + "PING" + "] >> Arduino")
	SerialPort.write("PING\n")
	time.sleep(0.5)
	while re.search("^PING_OK", SerialPort.readline()):
		logger.debug("[" + "PING" + "] >> Arduino")
		SerialPort.write("PING\n")
		time.sleep(0.1)
	SerialPort.flush()
	logger.debug("Arduino est pret")
	while True:
		line = SerialPort.readline()
		if line != '':
			parse_adrduino_answer(options, line, arduID)
			next # TODO: a quoi sert le next ?
		if not to_arduino.empty():
			logger.debug("process Queue")
			command = to_arduino.get()
			request = command.start_processing()
			logger.debug("IN check_queue doing [" + request + "]")
			if len(request) >= 64:
				SerialPort.write(request[0:64])
				time.sleep(0.1)  # TODO WHY : Laisse le temps a l'arduino de traiter la 1e part des données, 0.5 avant modif pour tests
				SerialPort.write(request[64:127] + '\n')
			else:
				SerialPort.write(request + '\n')
			line = SerialPort.readline()
			while not re.search(command.confirm, line):
				parse_adrduino_answer(options, line, arduID)
				if command.status() == "TIMEOUT":
					logger.error("TIMEOUT : " + request)
					to_arduino.task_done()
					break
				line = SerialPort.readline()
			else:
				# bonne reponse (on sort du while correctment)
				line = line.replace('\n', '')
				line = line.replace('\r', '')
				logger.debug("IN check_queue answer = [" + line + "]")
				command.result(line)
				to_arduino.task_done()
	logger.error("Thread END.")


def read_configFile( options, configFile):
	"""
	Read items from the configuration file
	"""
	if os.path.exists( configFile ):

		# ----------------------
		# Serial device
		options.ArduinoQty = read_config( configFile, "ArduinoQty")
		logger.debug("ArduinoQty: " + str(options.ArduinoQty))
		#options.serial_device = read_config( configFile, "serial_device")
		#logger.debug("Serial device: " + str(options.serial_device))

		# ----------------------
		# SOCKET SERVER
		options.sockethost = read_config( configFile, "sockethost")
		options.socketport = read_config( configFile, "socketport")
		logger.debug("Socket Host: " + str(options.sockethost))
		logger.debug("Socket Port: " + str(options.socketport))

		# ----------------------
		# SERIALS
		options.A1_port = read_config( configFile, "A1_serial_port")
		options.A2_port = read_config( configFile, "A2_serial_port")
		options.A3_port = read_config( configFile, "A3_serial_port")
		options.A4_port = read_config( configFile, "A4_serial_port")
		options.A5_port = read_config( configFile, "A5_serial_port")
		options.A6_port = read_config( configFile, "A6_serial_port")
		options.A7_port = read_config( configFile, "A7_serial_port")
		options.A8_port = read_config( configFile, "A8_serial_port")
		logger.debug("Arduino 1 Port: " + str(options.A1_port))
		logger.debug("Arduino 2 Port: " + str(options.A2_port))
		logger.debug("Arduino 3 Port: " + str(options.A3_port))
		logger.debug("Arduino 4 Port: " + str(options.A4_port))
		logger.debug("Arduino 5 Port: " + str(options.A5_port))
		logger.debug("Arduino 6 Port: " + str(options.A6_port))
		logger.debug("Arduino 7 Port: " + str(options.A7_port))
		logger.debug("Arduino 8 Port: " + str(options.A8_port))

		# -----------------------
		# DAEMON
		options.daemon_pidfile = read_config( configFile, "daemon_pidfile")
		logger.debug("Daemon_pidfile: " + str(options.daemon_pidfile))

		# TRIGGER
		options.trigger_url = read_config( configFile, "trigger_url")
		options.apikey = read_config( configFile, "apikey")
		options.trigger_timeout = read_config( configFile, "trigger_timeout")
		logger.debug("trigger_url: " + str(options.trigger_url))
		logger.debug("apikey: " + str(options.apikey))

	else:
		# config file not found, set default values
		print "Error: Configuration file not found (" + configFile + ")"
		logger.error("Error: Configuration file not found (" + configFile + ") Line: ")

# ----------------------------------------------------------------------------
def read_config( configFile, configItem):

	xmlData = ""
	if os.path.exists( configFile ):
		#open the xml file for reading:
		f = open( configFile,'r')
		data = f.read()
		f.close()

		try:
			dom = minidom.parseString(data)
		except:
			print "Error: problem in the config_arduidom.xml file, cannot process it"
			logger.debug('Error in config_arduidom.xml file')

		# Get config item
		logger.debug('Get the configuration item: ' + configItem)

		try:
			xmlTag = dom.getElementsByTagName( configItem )[0].toxml()
			logger.debug('Found: ' + xmlTag)
			xmlData = xmlTag.replace('<' + configItem + '>','').replace('</' + configItem + '>','')
			logger.debug('--> ' + xmlData)
		except:
			logger.debug('The item tag not found in the config file')
			xmlData = ""

	else:
		logger.error("Error: Config file does not exists.")

	return xmlData


def main(argv=None):

	global logger
	pinvalue = []
	for x in range(0, 102):
		pinvalue.append("z")
	nbprocesses = 0
	pyfolder = os.path.dirname(os.path.realpath(__file__)) + "/"

	(options, args) = cli_parser(argv)

	LOG_FILENAME = pyfolder + '../../../log/arduidom_daemon'
	formatter = logging.Formatter('%(asctime)s | %(levelname)s | %(threadName)s - %(module)s:%(lineno)d - %(message)s')

	if options.loglevel != "INFO":
		loglevel = "DEBUG"
	else:
		loglevel = "INFO"

	if options.nodaemon != "no":
		loglevel = "DEBUG"
		handler = logging.StreamHandler()
	else:
		handler = logging.FileHandler(LOG_FILENAME)
		sys.stderr = open(LOG_FILENAME + "_stderr", 'a', 1)

	handler.setFormatter(formatter)
	logger = logging.getLogger("arduidom")
	logger.setLevel(logging.getLevelName(loglevel))
	logger.addHandler(handler)

	KILL_FILENAME = pyfolder + 'arduidomx.kill'
	PID_FILENAME = pyfolder + 'arduidomx.pid'

	logger.info(".")
	logger.info(".")
	logger.info(".")
	logger.info(".")
	logger.info("######################################")
	logger.info("# ArduiDom - Arduino Link for jeeDom #")
	logger.info("# v2           by Bobox59 & Cedric02 #")
	logger.info("######################################")
	logger.info("LogLevel = " + loglevel + " option.logvevel = " + str(options.loglevel))
	logger.debug("Python version: %s.%s.%s" % sys.version_info[:3])

	# ----------------------------------------------------------
	# PROCESS CONFIG.XML
	configFile = os.path.join(pyfolder, "config_arduidom.xml")
	logger.debug("Configfile: " + configFile)
	logger.debug("Read configuration file")
	read_configFile(options, configFile)
	logger.debug("End of Read configuration file")
	logger.debug("1")
	options.jeedom = Jeedom("PHP", "", "", options.apikey)
	logger.debug("2")
	options.externalip = ""  # Sinon, cela ne fonctionne pas !!!!
	logger.debug("4")
	logger.debug("5")
	mypid = os.getpid()
	logger.debug("Mon PID = " + str(mypid))
	ps = subprocess.Popen(['ps', 'aux'], stdout=subprocess.PIPE).communicate()[0]
	processes = ps.split('\n')
	nfields = len(processes[0].split()) - 1
	for row in processes[1:]:
		procs = str(row.split(None, nfields))
		if procs.find("arduidomx.py") > -1:
			line = procs.split("', '")
			pid = int(line[1])
			if pid != mypid:
				logger.debug(row.split(None, nfields))
				logger.info("Tentative de terminer le démon : " + str(pid))
				logger.debug(os.kill(pid, signal.SIGKILL))
	ps = subprocess.Popen(['ps', 'aux'], stdout=subprocess.PIPE).communicate()[0]
	processes = ps.split('\n')
	nfields = len(processes[0].split()) - 1
	for row in processes[1:]:
		procs = str(row.split(None, nfields))
		if procs.find("arduidomx.py") > -1:
			nbprocesses += 1

	logger.debug("Nombre de processus arduidomx.py = " + str(nbprocesses))
	if nbprocesses > 1:
		logger.error("ERREUR FATALE, IL RESTE UN DEMON QUI TOURNE ENCORE !")
		exit()
	while os.path.isfile(KILL_FILENAME):
		logger.debug("fichier KILL trouvé au démarrage, suppression...")
		os.remove(KILL_FILENAME)
		time.sleep(0.5)

	if os.path.isfile(PID_FILENAME):
		logger.debug("fichier PID trouvé au démarrage, suppression...")
		os.remove(PID_FILENAME)
	file(PID_FILENAME, "w").write(str(os.getpid()))

	#-------------------------- THREADS USB -------------------------------------------------------------------
	logger.info("Launch USB Thread...")
	# noinspection PyBroadException
	try:
		for nb in range(1,int(options.ArduinoQty)+1) :
			logger.info("Launch USB Thread n°" + str(nb))
			worker_usb = Thread(target=COMServer, args=(options, "TH-COMServer", nb,))
			worker_usb.setDaemon(True)
			worker_usb.start()
			## thread.start_new_thread(COMServer, (options,"TH-COMServer",))

	except ImportError, e:
		logger.error("Error with Thread TH-COMServer :" + str(e))
		quit()

	#-------------------------- THREADS TCP -------------------------------------------------------------------
	logger.info("Launch TCP Thread on base port " + str(options.socketport) + "...")
	# noinspection PyBroadException
	try:
		for nb in range(1,int(options.ArduinoQty)+1) :
			worker_tcp = Thread(target=tcpServerThread, args=(options, "TH-TcpServer", nb,))
			worker_tcp.setDaemon(True)
			worker_tcp.start()
			## thread.start_new_thread(tcpServerThread, (options,"TH-TcpServer",))
	except ImportError, e:
		logger.error("Error with Thread TH-TcpServer " + str(e))
		quit()

	logger.info("Surveille le .kill ...")
	while 1:
		time.sleep(0.5)
		if os.path.isfile(KILL_FILENAME):
			logger.warning("KILL FILE " + str(KILL_FILENAME) + " FOUND, EXITING...")
			quit()
		pass
	logger.info("after kill...")


if __name__ == '__main__':
	main()
