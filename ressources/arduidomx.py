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
from Queue import Queue
from threading import Thread
__author__ = 'bmasquelier cedric02'
#
# Python script for Arduidom communication
#
to_arduino = Queue()
from_arduino = Queue()


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
        self.prefix += 'api=' + apikey + self._Separateur
        # TODO Else tcp

    def send(self, cmds):
        cmd_line = self.prefix
        for cmd in cmds:
            cmd_line += self._Separateur + cmd
        logger.debug("CLASS_JEEDOM->send [" + cmd_line + "]")
        subprocess.Popen(cmd_line, shell=True)


def cli_parser(argv=None):
    parser = optparse.OptionParser("usage: %prog -h   pour l'aide")
    parser.add_option("-d", "--device", dest="deviceport", default="none", type="string",
                      help="device port Arduino (ex:/dev/ttyACM0)")
    parser.add_option("-l", "--loglevel", dest="loglevel", default="error", type="string", help="Log Level")
    parser.add_option("-a", "--apikey", dest="apikey", default="none", type="string", help="JeeDom Api Key")
    parser.add_option("-e", "--extip", dest="externalip", default='', type="string", help="MASTER JeeDom IP")
    parser.add_option("-n", dest="nodaemon", default="no", help="Mettre -nd pour le lancer en DEBUG")
    parser.add_option("-i", dest="arduino_id", default="1", type="int", help="ARDUINO_ID")
    parser.add_option("-p", dest="port", default="58201", type="int", help="tcp port")
    return parser.parse_args(argv)


def handler(options, clientsocket, clientaddr):
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
                rf_request = from_jeedom(jeedata, "DBG_Data to do:RF")
                to_arduino.put(rf_request)
                while not rf_request.finished():
                    time.sleep(0.1)
                answer = rf_request.answer()
                logger.debug("[" + str(answer) + "] >> JeeDom")
                clientsocket.send(answer)
            break
    logger.debug("Close Jeedom Socket")
    clientsocket.close()


def tcpServerThread(options, threadName):
    logger.debug("Thread " + threadName + " Started.")
    host = "0.0.0.0"
    addr = (host, options.port)
    serversocket = socket(AF_INET, SOCK_STREAM)
    serversocket.setsockopt(SOL_SOCKET, SO_REUSEADDR, 1)
    serversocket.bind(addr)
    serversocket.listen(0)
    if not serversocket:
        exit()

    while 1:
        clientsocket, clientaddr = serversocket.accept()
        worker_handler = Thread(target=handler, args=(options, clientsocket, clientaddr))
        worker_handler.setDaemon(True)
        worker_handler.start()
    logger.debug("Server Stop to listening !")
    serversocket.close()


def parse_adrduino_answer(options, line):
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
            cmd.append('arduid=' + str(options.arduino_id))
            cmdlog = 'PHP(DATA)=> '
            for pinnumber in range(0, len(pinvalue)):
                cmd.append(str(pinnumber) + "=" + pinvalue[pinnumber].replace("DATA:", ""))
                cmdlog += ":" + str(pinnumber) + "=" + pinvalue[pinnumber].replace("DATA:", "")
            logger.debug(cmdlog)
            options.jeedom.send(cmd)

        elif line.find("DHT:") > -1:
            logger.debug("DHT values => FOUND")
            dhtvalue = line.rsplit(';')
            cmd = []
            cmd.append('arduid=' + str(options.arduino_id))
            cmdlog = "PHP(DHT)->"
            for pinnumber in range(0, len(dhtvalue)):
                if dhtvalue[pinnumber].find("nan") == -1:
                    cmd.append(str(pinnumber + 501) + "=" + dhtvalue[pinnumber].replace("DHT:", ""))
                    cmdlog += ":" + str(pinnumber + 501) + "=" + dhtvalue[pinnumber].replace("DHT:", "")
            logger.debug(cmdlog)
            options.jeedom.send(cmd)

        elif line.find(">>") > -1 and line.find("<<") > -1:
            psplit = line.rsplit('>>')
            pinnumber = int(psplit[0])
            psplit2 = psplit[1].rsplit('<<')
            # pinvalue[pinnumber] = psplit2[0]
            cmdlog = 'PHP=> '
            cmd = []
            cmd.append('arduid=' + str(options.arduino_id))
            cmd.append(str(pinnumber) + "=" + psplit2[0])
            cmdlog += ":" + str(pinnumber) + "=" + psplit2[0]
            logger.debug(cmdlog)
            options.jeedom.send(cmd)
        else:
            logger.info("NON READABLE Arduino >> [" + line + "]")


def COMServer(options, threadName):
    global to_arduino
    logger.debug("Thread " + threadName + " Started.")
    logger.info("Opening Arduino USB Port...")
    options.ArduinoPort = serial.Serial(options.deviceport, 115200, timeout=0.1)
    time.sleep(1)
    logger.debug("En attente de l'arduino (HELLO)")
    logger.debug("[" + "PING" + "] >> Arduino")
    options.ArduinoPort.write("PING\n")
    time.sleep(0.5)
    while re.search("^PING_OK", options.ArduinoPort.readline()):
        logger.debug("[" + "PING" + "] >> Arduino")
        options.ArduinoPort.write("PING\n")
        time.sleep(0.1)
    options.ArduinoPort.flush()
    logger.debug("Arduino est pret")
    while True:
        line = options.ArduinoPort.readline()
        if line != '':
            parse_adrduino_answer(options, line)
            next # TODO: a quoi sert le next ?
        if not to_arduino.empty():
            logger.debug("process Queue")
            command = to_arduino.get()
            request = command.start_processing()
            logger.debug("IN check_queue doing [" + request + "]")
            if len(request) >= 64:
                options.ArduinoPort.write(request[0:64])
                time.sleep(0.1)  # TODO WHY : Laisse le temps a l'arduino de traiter la 1e part des données, 0.5 avant modif pour tests
                options.ArduinoPort.write(request[64:127] + '\n')
            else:
                options.ArduinoPort.write(request + '\n')
            line = options.ArduinoPort.readline()
            while not re.search(command.confirm, line):
                parse_adrduino_answer(options, line)
                if command.status() == "TIMEOUT":
                    logger.error("TIMEOUT : " + request)
                    to_arduino.task_done()
                    break
                line = options.ArduinoPort.readline()
            else:
                # bonne reponse (on sort du while correctment)
                line = line.replace('\n', '')
                line = line.replace('\r', '')
                logger.debug("IN check_queue answer = [" + line + "]")
                command.result(line)
                to_arduino.task_done()
    logger.error("Thread END.")


def main(argv=None):
    global logger
    pinvalue = []
    pyfolder = os.path.dirname(os.path.realpath(__file__)) + "/"
    nbprocesses = 0
    for x in range(0, 102):
        pinvalue.append("z")

    (options, args) = cli_parser(argv)
    options.jeedom = Jeedom("PHP", "", "", options.apikey)
    LOG_FILENAME = pyfolder + '../../../log/arduidom_daemon_' + str(options.arduino_id)
    formatter = logging.Formatter('%(asctime)s | %(levelname)s | %(threadName)s - %(module)s:%(lineno)d - %(message)s')
    if options.loglevel == "1":
        loglevel = "DEBUG"
    else:
        loglevel = "INFO"
    if options.nodaemon != "no":
        loglevel = "DEBUG"
        handler = logging.StreamHandler()
    else:
        handler = logging.FileHandler(LOG_FILENAME)
    handler.setFormatter(formatter)
    logger = logging.getLogger("arduidom" + str(options.arduino_id))
    logger.setLevel(logging.getLevelName(loglevel))
    logger.addHandler(handler)

    logger.info("######################################")
    logger.info("# ArduiDom - Arduino Link for jeeDom #")
    logger.info("# v2           by Bobox59 & Cedric02 #")
    logger.info("######################################")
    logger.info("LogLevel = " + loglevel + " option.logvevel = " + str(options.loglevel))
    logger.debug("Python version: %s.%s.%s" % sys.version_info[:3])

    sys.stderr = open(LOG_FILENAME + "_stderr", 'a', 1)

    KILL_FILENAME = pyfolder + 'arduidom' + str(options.arduino_id) + '.kill'
    PID_FILENAME = pyfolder + 'arduidom' + str(options.arduino_id) + '.pid'
    options.ArduinoPortCfg = options.deviceport
    options.externalip = ""  # Sinon, cela ne fonctionne pas !!!!
    if options.deviceport == "none":
        logger.error("incorrect number of options, Exiting...")
        # parser.error("incorrect number of options, Exiting...") # Ligne qui pose peut-etre probleme
        quit()
    mypid = os.getpid()
    logger.debug("Mon PID = " + str(mypid))
    ps = subprocess.Popen(['ps', 'aux'], stdout=subprocess.PIPE).communicate()[0]
    processes = ps.split('\n')
    nfields = len(processes[0].split()) - 1
    for row in processes[1:]:
        procs = str(row.split(None, nfields))
        if procs.find("arduidomx.py -i " + str(options.arduino_id)) > -1:
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
        if procs.find("arduidomx.py -i " + str(options.arduino_id)) > -1:
            nbprocesses += 1

    logger.debug("Nombre de processus arduidom" + str(options.arduino_id) + ".py = " + str(nbprocesses))
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

    logger.info("Launch USB Thread...")
    # noinspection PyBroadException
    try:
        worker_usb = Thread(target=COMServer, args=(options, "TH-COMServer",))
        worker_usb.setDaemon(True)
        worker_usb.start()
        # thread.start_new_thread(COMServer, (options,"TH-COMServer",))
    except ImportError, e:
        logger.error("Error with Thread TH-COMServer :" + str(e))
        quit()

    logger.info("Launch TCP Thread on port " + str(options.port) + "...")
    # noinspection PyBroadException
    try:
        worker_usb = Thread(target=tcpServerThread, args=(options, "TH-TcpServer",))
        worker_usb.setDaemon(True)
        worker_usb.start()
        # thread.start_new_thread(tcpServerThread, (options,"TH-TcpServer",))
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
