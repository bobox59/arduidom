#!/usr/bin/env python
# -*- coding: UTF-8 -*-
__author__ = 'bmasquelier'
#
# Python script for Arduidom communication
#

import serial
import thread
from socket import *
import time
from time import localtime, strftime
from datetime import datetime
import subprocess
import os
import optparse
import sys
import signal
import logging

import pprint

pinvalue = []

lastradiosend = 0
ARDUINO_CPOK = 0
ARDUINO_SPOK = 0
ARDUINO_PINGOK = 0
cnt_timeout = 0


def cli_parser(argv=None):
   parser = optparse.OptionParser("usage: %prog -h   pour l'aide")
   parser.add_option("-d", "--device", dest="deviceport", default="none", type="string", help="device port Arduino (ex:/dev/ttyACM0)")
   parser.add_option("-l", "--loglevel", dest="loglevel", default="error", type="string", help="Log Level")
   parser.add_option("-a", "--apikey", dest="apikey", default="none", type="string", help="JeeDom Api Key")
   parser.add_option("-e", "--extip", dest="externalip", default='', type="string", help="MASTER JeeDom IP")
   parser.add_option("-n", dest="nodaemon", default="no", help="Mettre -nd pour le lancer en DEBUG")
   parser.add_option("-i", dest="arduino_id", default="1", type="int",  help="ARDUINO_ID")
   parser.add_option("-p", dest="port", default="58201",  type="int", help="tcp port")
   return parser.parse_args(argv)


def handler(options,clientsocket, clientaddr):
    global pinvalue
    global arduino_rx
    global ARDUINO_CPOK
    global ARDUINO_SPOK
    global ARDUINO_PINGOK
    global cnt_timeout
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
                logger.debug( "PING Received !")
                ARDUINO_PINGOK = 0
                arduino_rx = ""
                logger.debug("[" + jeedata + "] >> Arduino")
                options.ArduinoPort.write(jeedata + '\n')
                logger.debug("Wait Arduino Response...")
                while ARDUINO_PINGOK != 1:
                    time.sleep(0.1)
                logger.debug("Arduino >> [" + arduino_rx + "]")
                arduino_rx = ""
                msg = jeedata + "_OK"
                clientsocket.send(msg)
                logger.debug("[" + msg + "] >> JeeDom")

            if jeedata[0:2] == 'CP':
                logger.debug("CP Received !")
                ARDUINO_CPOK = 0
                cnt_cp_timeout = 0
                arduino_rx = ""
                logger.info("[" + jeedata[0:64] + "] >> Arduino" )  # ENVOI EN 2 PARTIES POUR SUPPORT LIMITE 64 Bytes Arduino Serial
                options.ArduinoPort.write(jeedata[0:64])
                time.sleep(0.5)
                logger.info("[" + jeedata[64:127] + "] >> Arduino")
                options.ArduinoPort.write(jeedata[64:127] + '\n')
                logger.debug("Wait Arduino CP_OK Response...")
                while ARDUINO_CPOK != 1:
                    time.sleep(0.1)
                    cnt_cp_timeout += 1
                    if cnt_cp_timeout > 60:
                        msg = jeedata + "_BAD"
                        logger.debug( "[" + msg + "] >> JeeDom")
                        clientsocket.send(msg)
                        ARDUINO_CPOK = 1

                if cnt_cp_timeout <= 60:
                    logger.debug("Arduino >> [" + arduino_rx + "]")
                    arduino_rx = ""
                    msg = jeedata + "_OK"
                    logger.debug( "[" + msg + "] >> JeeDom")
                    clientsocket.send(msg)

            if jeedata[0:2] == 'SP':
                logger.debug( "SP Received !")
                ARDUINO_SPOK = 0
                cnt_timeout = 0
                logger.info("[" + jeedata + "] >> Arduino" )
                options.ArduinoPort.write(jeedata + '\n')
                logger.debug( "Wait Arduino SP_OK Response...")
                while ARDUINO_SPOK != 1:
                    time.sleep(0.1)
                    cnt_timeout += 1
                    if cnt_timeout > 30:
                        msg = jeedata + "_BAD"
                        logger.info("[" + msg + "] >> JeeDom")
                        clientsocket.send(msg)
                        ARDUINO_SPOK = 1

                if cnt_timeout <= 30:
                    msg = jeedata + "_OK"
                    logger.info("[" + msg + "] >> JeeDom")
                    clientsocket.send(msg)

            if jeedata[0:2] == 'RF':  # *** Refresh Datas
                logger.debug( "RF Received !")
                arduino_rx = ""
                logger.info("[" + "RF" + "] >> Arduino" )
                options.ArduinoPort.write("RF\n")
                while arduino_rx == "":
                    time.sleep(0.01)

                logger.debug( "arduino_rx=" + arduino_rx)
                pinvalue = arduino_rx.rsplit(',')

            break
    logger.debug("Close Jeedom Socket")
    clientsocket.close()


def tcpServerThread(options,threadName):
    logger.debug( "Thread " + threadName + " Started.")
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
        thread.start_new_thread(handler, (options,clientsocket, clientaddr))

    logger.debug("Server Stop to listening !")
    serversocket.close()


def COMServer(options,threadName):
    global pinvalue
    global arduino_rx
    global lastradiosend
    global ARDUINO_CPOK
    global ARDUINO_SPOK
    global ARDUINO_PINGOK
    logger.debug( "Thread " + threadName + " Started.")

    while True:  # This is the main loop of program...................................................................
        line = ''
        while True:
            line = options.ArduinoPort.readline()
            if line != '':
                break

        if line.find("CP_OK") > -1:
            ARDUINO_CPOK = 1

        if line.find("SP_OK") > -1:
            ARDUINO_SPOK = 1

        if line.find("PING_OK") > -1:
            ARDUINO_PINGOK = 1

        if line != '':
            line = line.replace('\n', '')
            line = line.replace('\r', '')
            arduino_rx = line
            if line.find("Raw data:") == -1:
                logger.debug(1,  "Arduino >> [" + line + "]" )
            if line.find("Raw data:") != -1:
                logger.debug(1,  "Arduino >> [" + line + "]" )

            if line.find("DATA:") > -1:
                if ARDUINO_CPOK > 0:
                    logger.debug( "RF values => FOUND")
                    pinvalue = line.rsplit(',')

                    if options.externalip != "":
                        cmd = 'http://' + options.externalip + '/plugins/arduidom/core/php/jeeArduidom.php?'
                        _Separateur = "&"
                    else:
                        cmd = 'nice -n 19 /usr/bin/php '
                        cmd += '/usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php '
                        _Separateur = " "
                    #cmd = 'nice -n 19 /usr/bin/php '
                    #cmd += '/usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php '
                    cmd += 'api=' + options.apikey + _Separateur
                    cmd += 'arduid=' + str(options.arduino_id) + _Separateur
                    cmdlog = 'PHP=> '
                    for pinnumber in range(0, len(pinvalue)):
                        cmd += str(pinnumber)
                        cmdlog += str(pinnumber)
                        cmd += "="
                        cmdlog += "="
                        cmd += pinvalue[pinnumber].replace("DATA:", "")
                        cmdlog += pinvalue[pinnumber].replace("DATA:", "")
                        cmd += _Separateur
                        cmdlog += " "
                    if len(cmd) > 120:
                        logger.info( cmdlog )
                        subprocess.Popen(cmd, shell=True)


            if line.find("DHT:") > -1:
                if ARDUINO_CPOK > 0:
                    logger.debug("DHT values => FOUND")
                    dhtvalue = line.rsplit(';')

                    if options.externalip != "":
                        cmd = 'http://' + options.externalip + '/plugins/arduidom/core/php/jeeArduidom.php?'
                        _Separateur = "&"
                    else:
                        cmd = 'nice -n 19 /usr/bin/php '
                        cmd += '/usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php '
                        _Separateur = " "
                    #cmd = 'nice -n 19 /usr/bin/php '
                    #cmd += '/usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php '
                    cmd += 'api=' + options.apikey + _Separateur
                    cmd += 'arduid=' + str(options.arduino_id) + _Separateur
                    cmdlog = cmd
                    ####cmdlog = 'PHP=> '
                    for pinnumber in range(0, len(dhtvalue)):
                        if dhtvalue[pinnumber].find("nan") == -1:
                            cmd += str(pinnumber + 501)
                            cmdlog += str(pinnumber + 501)
                            cmd += "="
                            cmdlog += "="
                            cmd += dhtvalue[pinnumber].replace("DHT:", "")
                            cmdlog += dhtvalue[pinnumber].replace("DHT:", "")
                            cmd += _Separateur
                            cmdlog += " "
                    if len(cmd) > 120:
                        logger.info( cmdlog )
                        subprocess.Popen(cmd, shell=True)

            if line.find(">>") > -1 and line.find("<<") > -1:
                    if ARDUINO_CPOK > 0:
                        psplit = line.rsplit('>>')
                        pinnumber = int(psplit[0])
                        psplit2 = psplit[1].rsplit('<<')
                        pinvalue[pinnumber] = psplit2[0]

                        if options.externalip != "":
                            cmd = 'http://' + options.externalip + '/plugins/arduidom/core/php/jeeArduidom.php?'
                            _Separateur = "&"
                        else:
                            cmd = 'nice -n 19 /usr/bin/php '
                            cmd += '/usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php '
                            _Separateur = " "
                        cmdlog = 'PHP=> '
                        #cmd = 'nice -n 19 /usr/bin/php '
                        #cmd += '/usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php '
                        cmd += 'api=' + options.apikey + _Separateur
                        cmd += 'arduid=' + str(options.arduino_id) + _Separateur
                        cmd += str(pinnumber)
                        cmdlog += str(pinnumber)
                        cmd += "="
                        cmdlog += "="
                        cmd += pinvalue[pinnumber]
                        cmdlog += pinvalue[pinnumber]
                        cmd += _Separateur
                        cmdlog += " "
                        if len(cmd) > 120:
                            logger.info( cmdlog )
                            subprocess.Popen(cmd, shell=True)



def main(argv=None):
    global logger
    pyfolder = os.path.dirname(os.path.realpath(__file__)) + "/"
    nbprocesses = 0
    lastradiosend = datetime.now()
    for x in range(0,102):
        pinvalue.append("z")

    (options, args) = cli_parser(argv)
    LOG_FILENAME = pyfolder + '../../../log/arduidom_daemon_' + str(options.arduino_id)
    formatter = logging.Formatter('%(asctime)s - %(threadName)s - %(module)s:%(lineno)d - %(levelname)s - %(message)s')
    if (options.loglevel == 1):
        loglevel = "DEBUG" 
    else: 
        loglevel = "INFO"
    if options.nodaemon != "no":
        loglevel = "DEBUG"
        handler = logging.StreamHandler()
    else:
        handler = logging.FileHandler(LOG_FILENAME+"_logger")
    handler.setFormatter(formatter)
    logger = logging.getLogger("arduidom"+str(options.arduino_id))
    logger.setLevel(logging.getLevelName(loglevel))
    logger.addHandler(handler)

    logger.info("######################################")
    logger.info("# ArduiDom - Arduino Link for jeeDom #")
    logger.info("# v1.03                  by Bobox 59 #")
    logger.info("######################################")
    logger.debug("Python version: %s.%s.%s" % sys.version_info[:3])

#    sys.stdout = open(LOG_FILENAME, 'a', 1)
    sys.stderr = open(LOG_FILENAME+"_stderr", 'a', 1)


    KILL_FILENAME = pyfolder + 'arduidom' + str(options.arduino_id) + '.kill'
    PID_FILENAME = pyfolder + 'arduidom' + str(options.arduino_id) + '.pid'
    options.ArduinoPortCfg = options.deviceport
    options.externalip = ""     #Sinon, cela ne fonctionne pas !!!!
    if options.deviceport == "none":
        logger.error("incorrect number of options, Exiting...")
        parser.error("incorrect number of options, Exiting...")
        quit()
    mypid = os.getpid()
    logger.debug("Mon PID = " + str(mypid))
    ps = subprocess.Popen(['ps', 'aux'], stdout=subprocess.PIPE).communicate()[0]
    processes = ps.split('\n')
    nfields = len(processes[0].split()) - 1
    for row in processes[1:]:
        procs = str(row.split(None, nfields))
        if procs.find("arduidomx.py -i "+str(options.arduino_id)) > -1:
            line = procs.split("', '")
            pid = int(line[1])
            if pid != mypid:
                logger.debug(row.split(None, nfields))
                logger.info("Tentative de terminer le démon : " + str(pid) )
                logger.debug(os.kill(pid, signal.SIGKILL))
    ps = subprocess.Popen(['ps', 'aux'], stdout=subprocess.PIPE).communicate()[0]
    processes = ps.split('\n')
    nfields = len(processes[0].split()) - 1
    for row in processes[1:]:
        procs = str(row.split(None, nfields))
        if procs.find("arduidomx.py -i "+ str(options.arduino_id)) > -1:
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
        #os.kill(PID_FILENAME, signal.SIGKILL)
        os.remove(PID_FILENAME)

    file(PID_FILENAME, 'w').write(str(os.getpid()))
    logger.info("Opening Arduino USB Port...")
    options.ArduinoPort = serial.Serial(options.deviceport, 115200, timeout=0.1)
    logger.debug(options.ArduinoPort)
    time.sleep(1)
    logger.debug("En attente de l'arduino (HELLO)")
    logger.debug("[" + "PING" + "] >> Arduino" )
    options.ArduinoPort.write("PING\n")

    time.sleep(0.5)
    arduino_rx = options.ArduinoPort.readline()
    while arduino_rx.find("PING_OK") == -1:
        logger.debug("[" + "PING" + "] >> Arduino" )
        options.ArduinoPort.write("PING\n")
        arduino_rx = options.ArduinoPort.readline()
        logger.debug(arduino_rx)

    arduino_rx = ""
    time.sleep(0.1)
    options.ArduinoPort.flush()

    logger.info("Launch USB Thread...")
    # noinspection PyBroadException
    try:
        thread.start_new_thread(COMServer, (options,"TH-COMServer",))
    except ImportError, e:
        logger.error("Error with Thread TH-COMServer :"+str(e))
        quit()


    logger.info("Launch TCP Thread on port " + str(options.port) + "...")
    # noinspection PyBroadException
    try:
        thread.start_new_thread(tcpServerThread, (options,"TH-TcpServer",))
    except ImportError, e:
        logger.error("Error with Thread TH-TcpServer "+str(e))
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

