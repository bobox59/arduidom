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
to_arduino_1 = Queue()
to_arduino_2 = Queue()
to_arduino_3 = Queue()
to_arduino_4 = Queue()
to_arduino_5 = Queue()
to_arduino_6 = Queue()
to_arduino_7 = Queue()
to_arduino_8 = Queue()
from_arduino_1 = Queue()
from_arduino_2 = Queue()
from_arduino_3 = Queue()
from_arduino_4 = Queue()
from_arduino_5 = Queue()
from_arduino_6 = Queue()
from_arduino_7 = Queue()
from_arduino_8 = Queue()
trigger_nice = ""

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
    global trigger_nice
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
        self.prefix += trigger_nice
        self.prefix += ' api=' + apikey
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


def tcp_handler(options, clientsocket, clientaddr, arduID):
    global to_arduino_1, to_arduino_2, to_arduino_3, to_arduino_4, to_arduino_5, to_arduino_6, to_arduino_7, to_arduino_8
    if arduID != 0:
        logger.debug("Accepted jeedom connection from: " + str(clientaddr) + " to arduino " + str(arduID))
    while 1:
        jeedata = clientsocket.recv(1024)
        jeedata = jeedata.replace('\n', '')
        jeedata = jeedata.replace('\r', '')
        if not jeedata:
            break
        else:
            if arduID == 0:
                #logger.debug("JeeDom  >> [" + jeedata + "] >> Démon Python")
                if jeedata[0:4] == 'PING':
                    #logger.debug("Jeedom PING Received !")
                    if True: # remplacer par une verif générale du démon
                        logger.debug("JeeDom  >> [" + jeedata + "] >> Démon Python >> [" + "PING_OK" + "] >> JeeDom")
                        clientsocket.send("PING_OK")
                    else:
                        logger.debug("JeeDom  >> [" + jeedata + "] >> Démon Python >> [" + "PING_ERROR" + "] >> JeeDom")
                        clientsocket.send("PING_ERROR")
                    break

            else:
                logger.debug("JeeDom  >> [" + jeedata + "] >> Arduino " + str(arduID))
                if jeedata[0:4] == 'PING':
                    logger.debug("Jeedom PING Received for arduino " + str(arduID) + " !")
                    logger.debug("Make Ping Request for arduino " + str(arduID) + " !")
                    ping_request = from_jeedom(jeedata, "PING_OK_V:" + options.ArduinoVersion)
                    if arduID == 1: to_arduino_1.put(ping_request)
                    if arduID == 2: to_arduino_2.put(ping_request)
                    if arduID == 3: to_arduino_3.put(ping_request)
                    if arduID == 4: to_arduino_4.put(ping_request)
                    if arduID == 5: to_arduino_5.put(ping_request)
                    if arduID == 6: to_arduino_6.put(ping_request)
                    if arduID == 7: to_arduino_7.put(ping_request)
                    if arduID == 8: to_arduino_8.put(ping_request)

                    while not ping_request.finished():
                        time.sleep(0.1)
                    answer = ping_request.answer()
                    logger.debug("[" + str(answer) + "] >> JeeDom")
                    clientsocket.send(answer)

                elif jeedata[0:2] == 'CP':
                    cp_request = from_jeedom(jeedata, "CP_OK")
                    if arduID == 1: to_arduino_1.put(cp_request)
                    if arduID == 2: to_arduino_2.put(cp_request)
                    if arduID == 3: to_arduino_3.put(cp_request)
                    if arduID == 4: to_arduino_4.put(cp_request)
                    if arduID == 5: to_arduino_5.put(cp_request)
                    if arduID == 6: to_arduino_6.put(cp_request)
                    if arduID == 7: to_arduino_7.put(cp_request)
                    if arduID == 8: to_arduino_8.put(cp_request)
                    while not cp_request.finished():
                        time.sleep(0.1)
                    answer = cp_request.answer()
                    logger.debug("[" + str(answer) + "] >> JeeDom")
                    clientsocket.send(answer)

                elif jeedata[0:2] == 'SP':
                    sp_request = from_jeedom(jeedata, jeedata + "_OK")
                    if arduID == 1: to_arduino_1.put(sp_request)
                    if arduID == 2: to_arduino_2.put(sp_request)
                    if arduID == 3: to_arduino_3.put(sp_request)
                    if arduID == 4: to_arduino_4.put(sp_request)
                    if arduID == 5: to_arduino_5.put(sp_request)
                    if arduID == 6: to_arduino_6.put(sp_request)
                    if arduID == 7: to_arduino_7.put(sp_request)
                    if arduID == 8: to_arduino_8.put(sp_request)
                    while not sp_request.finished():
                        time.sleep(0.1)
                    answer = sp_request.answer()
                    logger.debug("[" + str(answer) + "] >> JeeDom")
                    clientsocket.send(answer)

                elif jeedata[0:2] == 'RF':  # *** Refresh Datas
                    rf_request = from_jeedom(jeedata, "DATA:")
                    if arduID == 1: to_arduino_1.put(rf_request)
                    if arduID == 2: to_arduino_2.put(rf_request)
                    if arduID == 3: to_arduino_3.put(rf_request)
                    if arduID == 4: to_arduino_4.put(rf_request)
                    if arduID == 5: to_arduino_5.put(rf_request)
                    if arduID == 6: to_arduino_6.put(rf_request)
                    if arduID == 7: to_arduino_7.put(rf_request)
                    if arduID == 8: to_arduino_8.put(rf_request)
                    while not rf_request.finished():
                        time.sleep(0.1)
                    answer = rf_request.answer()
                    logger.debug("[" + str(answer) + "] >> JeeDom")
                    clientsocket.send(answer)
                break
    if arduID != 0:
        logger.debug("Close Jeedom Socket")
    clientsocket.close()


def tcpServerThread(options, threadName, arduID):
    logger.debug("TCP Thread " + threadName + " for Arduino " + str(arduID) + " Started.")
    addr = (options.sockethost, int(options.socketport) + arduID)
    serversocket = socket(AF_INET, SOCK_STREAM)
    serversocket.setsockopt(SOL_SOCKET, SO_REUSEADDR, 1)
    serversocket.bind(addr)
    serversocket.listen(0)
    if not serversocket:
        exit()

    while 1:
        clientsocket, clientaddr = serversocket.accept()
        worker_handler = Thread(target=tcp_handler, args=(options, clientsocket, clientaddr, arduID))
        worker_handler.setDaemon(True)
        worker_handler.start()
    logger.debug("Server Stop to listening !")
    serversocket.close()


def parse_adrduino_answer(options, line, arduID):
    line = line.replace('\n', '')
    line = line.replace('\r', '')
    if line != '':
        logger.debug("Arduino " + str(arduID) + " >> [" + line + "]")
        if re.search("^DBG", line) or re.search("^SP", line) or re.search("^Pin ", line):
            logger.debug("Arduino " + str(arduID) + " *DBG* >> [" + line + "]")

        elif line.find("DATA:") > -1:
            logger.debug("RF values => FOUND")
            pinvalue = line.rsplit(',')
            cmd = ['arduid=' + str(arduID)]
            cmdlog = 'PHP(DATA)=> '
            for pinnumber in range(0, len(pinvalue)):
                cmd.append(str(pinnumber) + "=" + pinvalue[pinnumber].replace("DATA:", ""))
                cmdlog += str(arduID) + ":" + str(pinnumber) + "=" + pinvalue[pinnumber].replace("DATA:", "")
            logger.debug(cmdlog)
            options.jeedom.send(cmd)

        elif line.find("DHT:") > -1:
            logger.debug("DHT values => FOUND")
            dhtvalue = line.rsplit(';')
            cmd = ['arduid=' + str(arduID)]
            cmdlog = "PHP(DHT)->"
            for pinnumber in range(0, len(dhtvalue)):
                if (dhtvalue[pinnumber].find("nan") == -1) and (dhtvalue[pinnumber].find("na") == -1):
                    cmd.append(str(pinnumber + 501) + "=" + dhtvalue[pinnumber].replace("DHT:", ""))
                    cmdlog += str(arduID) + ":" + str(pinnumber + 501) + "=" + dhtvalue[pinnumber].replace("DHT:", "") + " "
            logger.debug(cmdlog)
            options.jeedom.send(cmd)

        elif line.find(">>") > -1 and line.find("<<") > -1:
            psplit = line.rsplit('>>')
            pinnumber = int(psplit[0])
            psplit2 = psplit[1].rsplit('<<')
            # pinvalue[pinnumber] = psplit2[0]
            cmdlog = 'PHP=> '
            cmd = ['arduid=' + str(arduID), str(pinnumber) + "=" + psplit2[0]]
            cmdlog += str(arduID) + ":" + str(pinnumber) + "=" + psplit2[0]
            logger.debug(cmdlog)
            options.jeedom.send(cmd)

        else:
            logger.info("Arduino " + str(arduID) + " (UNKNOWN ANSWER) >> [" + line + "]")


def COMServer(options, threadName, arduID):
    global to_arduino_1, to_arduino_2, to_arduino_3, to_arduino_4, to_arduino_5, to_arduino_6, to_arduino_7, to_arduino_8

    logger.debug("Thread " + threadName + " for arduino " + str(arduID) + " Started.")
    logger.info("Opening Arduino USB Port...")
    SerialPort = ""
    if arduID == 1 : SerialPort = serial.Serial(options.A1_port, 115200, timeout=0.3, xonxoff=0, rtscts=0)
    if arduID == 2 : SerialPort = serial.Serial(options.A2_port, 115200, timeout=0.3, xonxoff=0, rtscts=0)
    if arduID == 3 : SerialPort = serial.Serial(options.A3_port, 115200, timeout=0.3, xonxoff=0, rtscts=0)
    if arduID == 4 : SerialPort = serial.Serial(options.A4_port, 115200, timeout=0.3, xonxoff=0, rtscts=0)
    if arduID == 5 : SerialPort = serial.Serial(options.A5_port, 115200, timeout=0.3, xonxoff=0, rtscts=0)
    if arduID == 6 : SerialPort = serial.Serial(options.A6_port, 115200, timeout=0.3, xonxoff=0, rtscts=0)
    if arduID == 7 : SerialPort = serial.Serial(options.A7_port, 115200, timeout=0.3, xonxoff=0, rtscts=0)
    if arduID == 8 : SerialPort = serial.Serial(options.A8_port, 115200, timeout=0.3, xonxoff=0, rtscts=0)

    logger.debug("Reset Arduino " + str(arduID) + " via USB") # Reset Arduino
    SerialPort.flush()
    SerialPort.flushInput()
    SerialPort.setDTR(True)
    time.sleep(0.030)    # Read somewhere that 22ms is what the UI does.
    SerialPort.setDTR(False)
    time.sleep(0.200)
    SerialPort.flush()
    SerialPort.flushInput()
    logger.debug("En attente de l'arduino " + str(arduID) + " (HELLO)")
    line = ""
    checktimer = 0
    while not re.search("^HELLO", line):
        time.sleep(1)
        checktimer += 1
        line = SerialPort.readline()
        line = line.replace('\n', '')
        line = line.replace('\r', '')
        logger.debug("Arduino " + str(arduID) + " >> [" + line + "]")
        if checktimer > 15:
            logger.error("TIMEOUT d'attente du HELLO de l'arduino " + str(arduID))
            quit()
    SerialPort.flush()
    SerialPort.flushInput()
    logger.debug("Arduino " + str(arduID) + " est pret.")
    if arduID == 1 : options.A1_hello = 1
    if arduID == 2 : options.A2_hello = 1
    if arduID == 3 : options.A3_hello = 1
    if arduID == 4 : options.A4_hello = 1
    if arduID == 5 : options.A5_hello = 1
    if arduID == 6 : options.A6_hello = 1
    if arduID == 7 : options.A7_hello = 1
    if arduID == 8 : options.A8_hello = 1
    while True:
        while SerialPort.isOpen():
            line = SerialPort.readline()
            if line != '':
                logging.debug("Arduino " + str(arduID) + " >> " + str(line))
                parse_adrduino_answer(options, line, arduID)
                next  # TODO: a quoi sert le next ?

            queueEmpty = True
            if arduID == 1: queueEmpty = to_arduino_1.empty()
            if arduID == 2: queueEmpty = to_arduino_2.empty()
            if arduID == 3: queueEmpty = to_arduino_3.empty()
            if arduID == 4: queueEmpty = to_arduino_4.empty()
            if arduID == 5: queueEmpty = to_arduino_5.empty()
            if arduID == 6: queueEmpty = to_arduino_6.empty()
            if arduID == 7: queueEmpty = to_arduino_7.empty()
            if arduID == 8: queueEmpty = to_arduino_8.empty()
            #if queueEmpty:
                #logger.debug("Queue of arduino " + str(arduID) + " is empty")

            if not queueEmpty:
                logger.debug("process Queue of arduino " + str(arduID))
                if arduID == 1: command = to_arduino_1.get()
                if arduID == 2: command = to_arduino_2.get()
                if arduID == 3: command = to_arduino_3.get()
                if arduID == 4: command = to_arduino_4.get()
                if arduID == 5: command = to_arduino_5.get()
                if arduID == 6: command = to_arduino_6.get()
                if arduID == 7: command = to_arduino_7.get()
                if arduID == 8: command = to_arduino_8.get()
                request = command.start_processing()
                logger.debug("IN check_queue doing [" + request + "]")
                logger.debug("[" + request + "] >> Arduino " + str(arduID))
                if len(request) >= 64:
                    SerialPort.write(request[0:64])
                    time.sleep(0.1)  # TODO WHY : Laisse le temps a l'arduino de traiter la 1e part des données, 0.5 avant modif pour tests
                    SerialPort.write(request[64:127] + '\n')
                else:
                    SerialPort.write(request + '\n')
                line = SerialPort.readline()
                line = line.replace('\n', '')
                line = line.replace('\r', '')
                logger.debug("1_Arduino " + str(arduID) + " >> " + "[" + line + "]")
                while not re.search(command.confirm, line):
                    if re.search("_BAD", line):
                        command.result(line)
                        break
                    parse_adrduino_answer(options, line, arduID)
                    if command.status() == "TIMEOUT":
                        logger.error("TIMEOUT : " + request)
                        if arduID == 1: to_arduino_1.task_done()
                        if arduID == 2: to_arduino_2.task_done()
                        if arduID == 3: to_arduino_3.task_done()
                        if arduID == 4: to_arduino_4.task_done()
                        if arduID == 5: to_arduino_5.task_done()
                        if arduID == 6: to_arduino_6.task_done()
                        if arduID == 7: to_arduino_7.task_done()
                        if arduID == 8: to_arduino_8.task_done()
                        break
                    line = SerialPort.readline()
                    line = line.replace('\n', '')
                    line = line.replace('\r', '')
                    logger.debug("2_Arduino " + str(arduID) + " >> " + "[" + line + "]")
                else:
                    # bonne reponse (on sort du while correctment)
                    line = line.replace('\n', '')
                    line = line.replace('\r', '')
                    logger.debug("IN check_queue answer = [" + line + "]")
                    command.result(line)
                    if arduID == 1: to_arduino_1.task_done()
                    if arduID == 2: to_arduino_2.task_done()
                    if arduID == 3: to_arduino_3.task_done()
                    if arduID == 4: to_arduino_4.task_done()
                    if arduID == 5: to_arduino_5.task_done()
                    if arduID == 6: to_arduino_6.task_done()
                    if arduID == 7: to_arduino_7.task_done()
                    if arduID == 8: to_arduino_8.task_done()
        logger.error("Thread RESTART.")
    logger.error("Thread END")

def read_configFile( options, configFile):
    global trigger_nice
    if os.path.exists( configFile ):

        # ----------------------
        # Serial device
        options.ArduinoVersion = read_config( configFile, "ArduinoVersion")
        options.ArduinoQty = int(read_config( configFile, "ArduinoQty"))
        logger.debug("ArduinoQty: " + str(options.ArduinoQty))

        # ----------------------
        # SOCKET SERVER
        options.sockethost = read_config( configFile, "sockethost")
        options.socketport = read_config( configFile, "socketport")
        logger.debug("Socket Host: " + str(options.sockethost))
        logger.debug("Socket Port: " + str(options.socketport))

        # ----------------------
        # SERIALS
        if options.ArduinoQty >= 1:
            options.A1_port = read_config( configFile, "A1_serial_port")
            logger.debug("Arduino 1 Port: " + str(options.A1_port))
        if options.ArduinoQty >= 2:
            options.A2_port = read_config( configFile, "A2_serial_port")
            logger.debug("Arduino 2 Port: " + str(options.A2_port))
        if options.ArduinoQty >= 3:
            options.A3_port = read_config( configFile, "A3_serial_port")
            logger.debug("Arduino 3 Port: " + str(options.A3_port))
        if options.ArduinoQty >= 4:
            options.A4_port = read_config( configFile, "A4_serial_port")
            logger.debug("Arduino 4 Port: " + str(options.A4_port))
        if options.ArduinoQty >= 5:
            options.A5_port = read_config( configFile, "A5_serial_port")
            logger.debug("Arduino 5 Port: " + str(options.A5_port))
        if options.ArduinoQty >= 6:
            options.A6_port = read_config( configFile, "A6_serial_port")
            logger.debug("Arduino 6 Port: " + str(options.A6_port))
        if options.ArduinoQty >= 7:
            options.A7_port = read_config( configFile, "A7_serial_port")
            logger.debug("Arduino 7 Port: " + str(options.A7_port))
        if options.ArduinoQty >= 8:
            options.A8_port = read_config( configFile, "A8_serial_port")
            logger.debug("Arduino 8 Port: " + str(options.A8_port))

        # -----------------------
        # DAEMON
        options.daemon_pidfile = read_config( configFile, "daemon_pidfile")
        logger.debug("Daemon_pidfile: " + str(options.daemon_pidfile))

        # TRIGGER
        trigger_nice = read_config( configFile, "trigger_nice")
        options.trigger_nice = read_config( configFile, "trigger_nice")
        logger.debug("trigger_nice: " + str(options.trigger_nice))
        options.trigger_url = read_config( configFile, "trigger_url")
        logger.debug("trigger_url: " + str(options.trigger_url))
        options.apikey = read_config( configFile, "apikey")
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
        #logger.debug('Get the configuration item: ' + configItem)

        try:
            xmlTag = dom.getElementsByTagName( configItem )[0].toxml()
            #logger.debug('Found: ' + xmlTag)
            xmlData = xmlTag.replace('<' + configItem + '>','').replace('</' + configItem + '>','')
            #logger.debug('--> ' + xmlData)
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

    LOG_FILENAME = "/tmp/arduidom_daemon"
    #LOG_FILENAME = pyfolder + '../../../log/arduidom_daemon'
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

    options.A1_ready = False
    options.A2_ready = False
    options.A3_ready = False
    options.A4_ready = False
    options.A5_ready = False
    options.A6_ready = False
    options.A7_ready = False
    options.A8_ready = False
    options.A1_hello = 0
    options.A2_hello = 0
    options.A3_hello = 0
    options.A4_hello = 0
    options.A5_hello = 0
    options.A6_hello = 0
    options.A7_hello = 0
    options.A8_hello = 0

    logger.info(".")
    logger.info(".")
    logger.info(".")
    logger.info(".")
    logger.info("######################################")
    logger.info("# ArduiDom - Arduino Link for jeeDom #")
    logger.info("# v2           by Bobox59 & Cedric02 #")
    logger.info("######################################")
    username = os.environ['USER']
    logger.info("Username = " + str(username))
    logger.info("LogLevel = " + loglevel + " option.logvevel = " + str(options.loglevel))
    logger.debug("Python version: %s.%s.%s" % sys.version_info[:3])

    # ----------------------------------------------------------
    # PROCESS CONFIG.XML
    configFile = os.path.join(pyfolder, "config_arduidom.xml")
    logger.debug("Configfile: " + configFile)
    logger.debug("Read configuration file")
    read_configFile(options, configFile)
    logger.debug("End of Read configuration file")
    options.jeedom = Jeedom("PHP", "", "", options.apikey)
    options.externalip = ""  # Sinon, cela ne fonctionne pas !!!!
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

    logger.info("Nombre de processus arduidomx.py = " + str(nbprocesses))
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
    logger.info(".")
    logger.info(".")
    # noinspection PyBroadException
    try:
        for nb1 in range(1,int(options.ArduinoQty)+1) :
            portcheck = ''
            if nb1 == 1 : portcheck = options.A1_port
            if nb1 == 2 : portcheck = options.A2_port
            if nb1 == 3 : portcheck = options.A3_port
            if nb1 == 4 : portcheck = options.A4_port
            if nb1 == 5 : portcheck = options.A5_port
            if nb1 == 6 : portcheck = options.A6_port
            if nb1 == 7 : portcheck = options.A7_port
            if nb1 == 8 : portcheck = options.A8_port
            if portcheck != 'Network' :
                logger.info("Launch USB Thread n°" + str(nb1))
                worker_usb = Thread(target=COMServer, args=(options, "TH-COMServer", nb1,))
                worker_usb.setDaemon(True)
                worker_usb.start()
                ## thread.start_new_thread(COMServer, (options,"TH-COMServer",))

    except ImportError, e:
        logger.error("Error with Thread TH-COMServer :" + str(e))
        quit()

    if options.A1_port == "Network" :
        options.A1_hello = 1


    for nb2 in range(1,int(options.ArduinoQty)+1) :
        logger.info("Verify Arduino Version [" + options.ArduinoVersion + "] >> Arduino " + str(nb2))
        if nb2 != 0: logger.debug("Jeedom PING Received for arduino " + str(nb2) + " !")
        if nb2 != 0: logger.debug("Make Ping Request for arduino " + str(nb2) + " !")
        ping_request = from_jeedom("PING", "^PING_OK")
        if nb2 == 1:
            if options.A1_port != 'Network':
                comm_check = 0
                while options.A1_hello != 1:
                    time.sleep(0.1)
                    comm_check += 0.1
                    if comm_check > 150:
                        quit()
                to_arduino_1.put(ping_request)
                while not ping_request.finished():
                    time.sleep(0.1)
                answer = ping_request.answer()
                logger.info("Arduino " + str(nb2) + " >> [" + str(answer) + "]")
                if answer != "PING_OK_V:" + options.ArduinoVersion:
                    if re.search("PING_OK", answer):
                        logger.error("Version du sketch Arduino " + str(nb2) + " Incorrecte !")
                        cmd = ['daemonready=2']
                        options.jeedom.send(cmd)
                    quit()
                logger.debug("Version Arduino " + str(nb2) + " OK")
            options.A1_ready = True
        if nb2 == 2:
            if options.A2_port != 'Network':
                comm_check = 0
                while options.A2_hello != 1:
                    time.sleep(0.1)
                    comm_check += 0.1
                    if comm_check > 150:
                        quit()
                to_arduino_2.put(ping_request)
                while not ping_request.finished():
                    time.sleep(0.1)
                answer = ping_request.answer()
                logger.info("Arduino " + str(nb2) + " >> [" + str(answer) + "]")
                if answer != "PING_OK_V:" + options.ArduinoVersion:
                    if re.search("PING_OK", answer):
                        logger.error("Version du sketch Arduino " + str(nb2) + " Incorrecte !")
                        cmd = ['daemonready=2']
                        options.jeedom.send(cmd)
                    quit()
                logger.debug("Version Arduino " + str(nb2) + " OK")
            options.A2_ready = True
        if nb2 == 3:
            if options.A3_port != 'Network':
                comm_check = 0
                while options.A3_hello != 1:
                    time.sleep(0.1)
                    comm_check += 0.1
                    if comm_check > 150:
                        quit()
                to_arduino_3.put(ping_request)
                while not ping_request.finished():
                    time.sleep(0.1)
                answer = ping_request.answer()
                logger.info("Arduino " + str(nb2) + " >> [" + str(answer) + "]")
                if answer != "PING_OK_V:" + options.ArduinoVersion:
                    if re.search("PING_OK", answer):
                        logger.error("Version du sketch Arduino " + str(nb2) + " Incorrecte !")
                        cmd = ['daemonready=2']
                        options.jeedom.send(cmd)
                    quit()
                logger.debug("Version Arduino " + str(nb2) + " OK")
            options.A3_ready = True
        if nb2 == 4:
            if options.A4_port != 'Network':
                comm_check = 0
                while options.A4_hello != 1:
                    time.sleep(0.1)
                    comm_check += 0.1
                    if comm_check > 150:
                        quit()
                to_arduino_4.put(ping_request)
                while not ping_request.finished():
                    time.sleep(0.1)
                answer = ping_request.answer()
                logger.info("Arduino " + str(nb2) + " >> [" + str(answer) + "]")
                if answer != "PING_OK_V:" + options.ArduinoVersion:
                    if re.search("PING_OK", answer):
                        logger.error("Version du sketch Arduino " + str(nb2) + " Incorrecte !")
                        cmd = ['daemonready=2']
                        options.jeedom.send(cmd)
                    quit()
                logger.debug("Version Arduino " + str(nb2) + " OK")
            options.A4_ready = True
        if nb2 == 5:
            if options.A5_port != 'Network':
                comm_check = 0
                while options.A5_hello != 1:
                    time.sleep(0.1)
                    comm_check += 0.1
                    if comm_check > 150:
                        quit()
                to_arduino_5.put(ping_request)
                while not ping_request.finished():
                    time.sleep(0.1)
                answer = ping_request.answer()
                logger.info("Arduino " + str(nb2) + " >> [" + str(answer) + "]")
                if answer != "PING_OK_V:" + options.ArduinoVersion:
                    if re.search("PING_OK", answer):
                        logger.error("Version du sketch Arduino " + str(nb2) + " Incorrecte !")
                        cmd = ['daemonready=2']
                        options.jeedom.send(cmd)
                    quit()
                logger.debug("Version Arduino " + str(nb2) + " OK")
            options.A5_ready = True
        if nb2 == 6:
            if options.A6_port != 'Network':
                comm_check = 0
                while options.A6_hello != 1:
                    time.sleep(0.1)
                    comm_check += 0.1
                    if comm_check > 150:
                        quit()
                to_arduino_6.put(ping_request)
                while not ping_request.finished():
                    time.sleep(0.1)
                answer = ping_request.answer()
                logger.info("Arduino " + str(nb2) + " >> [" + str(answer) + "]")
                if answer != "PING_OK_V:" + options.ArduinoVersion:
                    if re.search("PING_OK", answer):
                        logger.error("Version du sketch Arduino " + str(nb2) + " Incorrecte !")
                        cmd = ['daemonready=2']
                        options.jeedom.send(cmd)
                    quit()
                logger.debug("Version Arduino " + str(nb2) + " OK")
            options.A6_ready = True
        if nb2 == 7:
            if options.A7_port != 'Network':
                comm_check = 0
                while options.A7_hello != 1:
                    time.sleep(0.1)
                    comm_check += 0.1
                    if comm_check > 150:
                        quit()
                to_arduino_7.put(ping_request)
                while not ping_request.finished():
                    time.sleep(0.1)
                answer = ping_request.answer()
                logger.info("Arduino " + str(nb2) + " >> [" + str(answer) + "]")
                if answer != "PING_OK_V:" + options.ArduinoVersion:
                    if re.search("PING_OK", answer):
                        logger.error("Version du sketch Arduino " + str(nb2) + " Incorrecte !")
                        cmd = ['daemonready=2']
                        options.jeedom.send(cmd)
                    quit()
                logger.debug("Version Arduino " + str(nb2) + " OK")
            options.A7_ready = True
        if nb2 == 8:
            if options.A8_port != 'Network':
                comm_check = 0
                while options.A8_hello != 1:
                    time.sleep(0.1)
                    comm_check += 0.1
                    if comm_check > 150:
                        quit()
                to_arduino_8.put(ping_request)
                while not ping_request.finished():
                    time.sleep(0.1)
                answer = ping_request.answer()
                logger.info("Arduino " + str(nb2) + " >> [" + str(answer) + "]")
                if answer != "PING_OK_V:" + options.ArduinoVersion:
                    if re.search("PING_OK", answer):
                        logger.error("Version du sketch Arduino " + str(nb2) + " Incorrecte !")
                        cmd = ['daemonready=2']
                        options.jeedom.send(cmd)
                    quit()
                logger.debug("Version Arduino " + str(nb2) + " OK")
            options.A8_ready = True

    #-------------------------- MAIN TCP THREAD -------------------------------------------------------------------
    try:
        logger.info("Launch Main TCP Thread")
        worker_tcp = Thread(target=tcpServerThread, args=(options, "TH-TcpServer", 0,))
        worker_tcp.setDaemon(True)
        worker_tcp.start()
        ## thread.start_new_thread(tcpServerThread, (options,"TH-TcpServer",))
    except ImportError, e:
        logger.error("Error with Thread TH-TcpServer " + str(e))
        quit()

    #-------------------------- THREADS TCP -------------------------------------------------------------------
    logger.info(".")
    logger.info(".")
    logger.info("Prepare to launch TCP Thread(s) on base port " + str(options.socketport) + "...")
    # noinspection PyBroadException
    for nb in range(1,int(options.ArduinoQty)+1) :
        ArduinoReady = False
        while not ArduinoReady:
            if nb == 1: ArduinoReady = options.A1_ready
            if nb == 2: ArduinoReady = options.A2_ready
            if nb == 3: ArduinoReady = options.A3_ready
            if nb == 4: ArduinoReady = options.A4_ready
            if nb == 5: ArduinoReady = options.A5_ready
            if nb == 6: ArduinoReady = options.A6_ready
            if nb == 7: ArduinoReady = options.A7_ready
            if nb == 8: ArduinoReady = options.A8_ready
            time.sleep(0.5)

        try:
            logger.info("Launch TCP Thread n°" + str(nb))
            worker_tcp = Thread(target=tcpServerThread, args=(options, "TH-TcpServer", nb,))
            worker_tcp.setDaemon(True)
            worker_tcp.start()
            ## thread.start_new_thread(tcpServerThread, (options,"TH-TcpServer",))
        except ImportError, e:
            logger.error("Error with Thread TH-TcpServer " + str(e))
            quit()

    logger.info("ALL TCP Threads Launched !")

    logger.info("Tell to jeedom Arduinos are OK")
    cmd = ['daemonready=1']
    options.jeedom.send(cmd)

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
