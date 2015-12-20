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

data = ""
dhtvalue = 0
pinvalue = ["z"]
oldpinvalue = ["z"]
xl = 0
while xl < 101:
    pinvalue.append("z")
    oldpinvalue.append("z")
    xl += 1

VIOLET = '\033[95m'
BLUE = '\033[94m'
GREEN = '\033[92m'
YELLOW = '\033[93m'
RED = '\033[91m'
WHITE = '\033[0m'
radiorxpin = 0
compteur = 0
pyfolder = os.path.dirname(os.path.realpath(__file__)) + "/"
LOG_FILENAME = pyfolder + '../../../log/arduidom_daemon'
KILL_FILENAME = pyfolder + 'arduidom.kill'
PID_FILENAME = pyfolder + 'arduidom.pid'
compteurraz = 0
lastradiosend = datetime.now()
readinputs = 0
nbprocesses = 0
mypid = os.getpid()
ARDUINO_CPOK = 0
ARDUINO_SPOK = 0
ARDUINO_PINGOK = 0
cnt_timeout = 0
_externalip = ''

def log(message):
    print(str(strftime("%Y-%m-%d %H:%M:%S", localtime())) + " | debug | ") + str(message)


def handler(clientsocket, clientaddr):
    global pinvalue
    global arduino_rx
    global ARDUINO_CPOK
    global ARDUINO_SPOK
    global ARDUINO_PINGOK
    global cnt_timeout
    #log("Accepted jeedom connection from: " + str(clientaddr))

    while 1:
        jeedata = clientsocket.recv(1024)
        jeedata = jeedata.replace('\n', '')
        jeedata = jeedata.replace('\r', '')
        if not jeedata:
            break
        else:
            log(" ")
            log((YELLOW + "JeeDom  >> [" + jeedata + "]" + WHITE))
            if jeedata[0:4] == 'PING':
                log("PING Received !")
                ARDUINO_PINGOK = 0
                arduino_rx = ""
                log((BLUE + "[" + jeedata + "] >> Arduino" + WHITE))
                ArduinoPort.write(jeedata + '\n')
                log("Wait Arduino Response...")
                while ARDUINO_PINGOK != 1:
                    time.sleep(0.1)
                log(("Arduino >> [" + arduino_rx + "]"))
                arduino_rx = ""
                msg = jeedata + "_OK"
                clientsocket.send(msg)
                log(("[" + msg + "] >> JeeDom"))

            if jeedata[0:2] == 'CP':
                log("CP Received !")
                ARDUINO_CPOK = 0
                cnt_cp_timeout = 0
                arduino_rx = ""
                log((BLUE + "[" + jeedata[0:64] + "] >> Arduino" + WHITE)) # ENVOI EN 2 PARTIES POUR SUPPORT LIMITE 64 Bytes Arduino Serial
                ArduinoPort.write(jeedata[0:64])
                time.sleep(0.5)
                log((BLUE + "[" + jeedata[64:127] + "] >> Arduino" + WHITE))
                ArduinoPort.write(jeedata[64:127] + '\n')
                log("Wait Arduino CP_OK Response...")
                while ARDUINO_CPOK != 1:
                    time.sleep(0.1)
                    cnt_cp_timeout += 1
                    if cnt_cp_timeout > 60:
                        msg = jeedata + "_BAD"
                        log("[" + msg + "] >> JeeDom")
                        clientsocket.send(msg)
                        ARDUINO_CPOK = 1

                if cnt_cp_timeout <= 60:
                    log(("Arduino >> [" + arduino_rx + "]"))
                    arduino_rx = ""
                    msg = jeedata + "_OK"
                    log("[" + msg + "] >> JeeDom")
                    clientsocket.send(msg)

            if jeedata[0:2] == 'SP':
                log("SP Received !")
                ARDUINO_SPOK = 0
                cnt_timeout = 0
                log((BLUE + "[" + jeedata + "] >> Arduino" + WHITE))
                ArduinoPort.write(jeedata + '\n')
                log("Wait Arduino SP_OK Response...")
                while ARDUINO_SPOK != 1:
                    time.sleep(0.1)
                    cnt_timeout += 1
                    if cnt_timeout > 30 :
                        msg = jeedata + "_BAD"
                        log("[" + msg + "] >> JeeDom")
                        clientsocket.send(msg)
                        ARDUINO_SPOK = 1

                if cnt_timeout <= 30:
                    msg = jeedata + "_OK"
                    log(("[" + msg + "] >> JeeDom"))
                    clientsocket.send(msg)

            if jeedata[0:2] == 'RF':  # *** Refresh Datas
                log("RF Received !")
                arduino_rx = ""
                log((BLUE + "[" + "RF" + "] >> Arduino" + WHITE))
                ArduinoPort.write("RF\n")
                while arduino_rx == "":
                    time.sleep(0.01)

                log("arduino_rx=" + arduino_rx)
                pinvalue = arduino_rx.rsplit(',')



            break
    #log("Close Jeedom Socket")
    clientsocket.close()


def tcpServerThread(threadName):
    global pinvalue
    global arduino_rx
    log("Thread " + threadName + " Started.")
    host = "0.0.0.0"
    port = 58174
    addr = (host, port)
    serversocket = socket(AF_INET, SOCK_STREAM)
    serversocket.setsockopt(SOL_SOCKET, SO_REUSEADDR, 1)
    serversocket.bind(addr)
    serversocket.listen(0)
    if not serversocket:
        exit()

    while 1:
        #log("TCP Server is waiting for jeedom connection...")
        clientsocket, clientaddr = serversocket.accept()
        thread.start_new_thread(handler, (clientsocket, clientaddr))

    log("Server Stop to listening !")
    serversocket.close()


def COMServer(threadName):
    global pinvalue
    global oldpinvalue
    global arduino_rx
    global radiorxpin
    global compteur
    global compteurraz
    global lastradiosend
    global apikey
    global ARDUINO_CPOK
    global ARDUINO_SPOK
    global ARDUINO_PINGOK
    global _externalip
    log("Thread " + threadName + " Started.")

    while True:  # This is the main loop of program...................................................................
        line = ''
        while True:
            line = ArduinoPort.readline()
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
                log(GREEN + "Arduino >> [" + line + "]" + WHITE)
            if line.find("Raw data:") != -1:
                log(VIOLET + "Arduino >> [" + line + "]" + WHITE)

            if line.find("DATA:") > -1:
                if ARDUINO_CPOK > 0:
                    #log("RF values => FOUND")
                    pinvalue = line.rsplit(',')

                    if _externalip != "":
                        cmd = 'http://' + _externalip + '/plugins/arduidom/core/php/jeeArduidom.php?'
                        _Separateur = "&"
                    else:
                        cmd = 'nice -n 19 /usr/bin/php '
                        cmd += '/usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php '
                        _Separateur = " "
                    #cmd = 'nice -n 19 /usr/bin/php '
                    #cmd += '/usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php '
                    cmd += 'api=' + apikey + _Separateur
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
                        log(RED + cmdlog + WHITE)
                        subprocess.Popen(cmd, shell=True)

                    for pinnumber in range(0, len(pinvalue)):
                        oldpinvalue[pinnumber] = pinvalue[pinnumber]

            if line.find("DHT:") > -1:
                if ARDUINO_CPOK > 0:
                    #log("DHT values => FOUND")
                    dhtvalue = line.rsplit(';')


                    if _externalip != "":
                        cmd = 'http://' + _externalip + '/plugins/arduidom/core/php/jeeArduidom.php?'
                        _Separateur = "&"
                    else:
                        cmd = 'nice -n 19 /usr/bin/php '
                        cmd += '/usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php '
                        _Separateur = " "
                    #cmd = 'nice -n 19 /usr/bin/php '
                    #cmd += '/usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php '
                    cmd += 'api=' + apikey + _Separateur
                    cmdlog = 'PHP=> '
                    for pinnumber in range(0, len(dhtvalue)):
                        if dhtvalue[pinnumber].find("nan") == -1:
                            cmd += str(pinnumber + 101)
                            cmdlog += str(pinnumber + 101)
                            cmd += "="
                            cmdlog += "="
                            cmd += dhtvalue[pinnumber].replace("DHT:", "")
                            cmdlog += dhtvalue[pinnumber].replace("DHT:", "")
                            cmd += _Separateur
                            cmdlog += " "
                    if len(cmd) > 120:
                        log(RED + cmdlog + WHITE)
                        subprocess.Popen(cmd, shell=True)


            if line.find(">>") > -1:
                if line.find("<<") > -1:
                    if ARDUINO_CPOK > 0:
                        psplit = line.rsplit('>>')
                        pinnumber = int(psplit[0])
                        psplit2 = psplit[1].rsplit('<<')
                        oldpinvalue[pinnumber] = psplit2[0]
                        pinvalue[pinnumber] = psplit2[0]

                        if _externalip != "":
                            cmd = 'http://' + _externalip + '/plugins/arduidom/core/php/jeeArduidom.php?'
                            _Separateur = "&"
                        else:
                            cmd = 'nice -n 19 /usr/bin/php '
                            cmd += '/usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php '
                            _Separateur = " "
                        cmdlog = 'PHP=> '
                        #cmd = 'nice -n 19 /usr/bin/php '
                        #cmd += '/usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php '
                        cmd += 'api=' + apikey + _Separateur
                        cmd += str(pinnumber)
                        cmdlog += str(pinnumber)
                        cmd += "="
                        cmdlog += "="
                        cmd += pinvalue[pinnumber]
                        cmdlog += pinvalue[pinnumber]
                        cmd += _Separateur
                        cmdlog += " "
                        if len(cmd) > 120:
                            log(RED + cmdlog + WHITE)
                            subprocess.Popen(cmd, shell=True)



parser = optparse.OptionParser("usage: %prog -h   pour l'aide")
parser.add_option("-d", "--device", dest="deviceport", default="none", type="string", help="device port Arduino (ex:/dev/ttyACM0)")
parser.add_option("-a", "--apikey", dest="apikey", default="none", type="string", help="JeeDom Api Key")
parser.add_option("-e", "--extip", dest="_externalip", default='', type="string", help="MASTER JeeDom IP")
parser.add_option("-n", dest="nodaemon", default="no", help="Mettre -nd pour le lancer en DEBUG")

(options, args) = parser.parse_args()
print(options)

apikey = options.apikey
if options.nodaemon != "no":
    print "Lancement en DEBUG MODE"
    time.sleep(2)
else:
    sys.stdout = open(LOG_FILENAME, 'a', 1)
    sys.stderr = open(LOG_FILENAME, 'a', 1)

if options.deviceport == "none":
    parser.error("incorrect number of options, Exiting...")
    quit()


log("Mon PID = " + str(mypid))
ps = subprocess.Popen(['ps', 'aux'], stdout=subprocess.PIPE).communicate()[0]
processes = ps.split('\n')
nfields = len(processes[0].split()) - 1
for row in processes[1:]:
    procs = str(row.split(None, nfields))
    if procs.find("arduidom.py") > -1:
        line = procs.split("', '")
        pid = int(line[1])
        if pid != mypid:
            log(row.split(None, nfields))
            log(RED + "Tentative de terminer le démon : " + str(pid) + WHITE)
            log(os.kill(pid, signal.SIGKILL))

ps = subprocess.Popen(['ps', 'aux'], stdout=subprocess.PIPE).communicate()[0]
processes = ps.split('\n')
nfields = len(processes[0].split()) - 1
for row in processes[1:]:
    procs = str(row.split(None, nfields))
    if procs.find("arduidom.py") > -1:
        nbprocesses += 1

log("Nombre de processus arduidom.py = " + str(nbprocesses))
if nbprocesses > 1:
    log(RED + "ERREUR FATALE, IL RESTE UN DEMON QUI TOURNE ENCORE !" + WHITE)
    exit()

ArduinoPortCfg = options.deviceport

#log("Arduidom.py is in ")
#log(pyfolder)
log("")
log("######################################")
log("# ArduiDom - Arduino Link for jeeDom #")
log("# v1.03                  by Bobox 59 #")
log("######################################")
log("")
while os.path.isfile(KILL_FILENAME):
    log("fichier KILL trouvé au démarrage, suppression...")
    os.remove(KILL_FILENAME)
    time.sleep(0.5)

if os.path.isfile(PID_FILENAME):
    log("fichier PID trouvé au démarrage, suppression...")
    os.kill(PID_FILENAME)
    os.remove(PID_FILENAME)
    file.write(PID_FILENAME, os.getpid())

log("Opening Arduino USB Port...")
#ArduinoPortCfg = file(pyfolder + 'port.cfg').readline()
ArduinoPort = serial.Serial(ArduinoPortCfg, 115200, timeout=0.1)
log(ArduinoPort)
time.sleep(1)
log("En attente de l'arduino (HELLO)")
log((BLUE + "[" + "PING" + "] >> Arduino" + WHITE))
ArduinoPort.write("PING\n")

time.sleep(0.5)
arduino_rx = ArduinoPort.readline()
while arduino_rx.find("PING_OK") == -1:
    log((BLUE + "[" + "PING" + "] >> Arduino" + WHITE))
    ArduinoPort.write("PING\n")
    arduino_rx = ArduinoPort.readline()
    log(arduino_rx)

arduino_rx = ""
log(":)")
log("")
log("")
log("")
time.sleep(0.1)
ArduinoPort.flush()
#log((BLUE + "[" + "CP" + "] >> Arduino" + WHITE))
#ArduinoPort.write("CP\n")

log("Launch USB Thread...")
# noinspection PyBroadException
try:
    thread.start_new_thread(COMServer, ("TH-COMServer",))
except ImportError, e:
    log("Error with Thread TH-COMServer")
    log(e)
    quit()


log("Launch TCP Thread...")
# noinspection PyBroadException
try:
    thread.start_new_thread(tcpServerThread, ("TH-TcpServer",))
except ImportError, e:
    log("Error with Thread TH-TcpServer")
    log(e)
    quit()

#time.sleep(1)

###log("Launch readDataThread...")
#### noinspection PyBroadException
###try:
###    thread.start_new_thread(readDataThread, ("TH-readDataThread",))
###except ImportError, e:
###    log("Error with Thread TH-readDataThread")
###    log(e)
###    quit()

log("Surveille le .kill ...")
while 1:
    time.sleep(0.5)
    if os.path.isfile(KILL_FILENAME):
        log("---------------------------------------")
        log("KILL FILE FOUND, EXITING...")
        quit()

    pass
