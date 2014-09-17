__author__ = 'bmasquelier'
#
# Python script for serial to UDP communication
#

import serial
import os
import subprocess
import time
import logging
import threading

logger = logging.getLogger('arduidom')

GRIS = '\033[95m'
BLUE = '\033[94m'
GREEN = '\033[92m'
YELLOW = '\033[93m'
RED = '\033[91m'
data = ""
pin1 = ""
oldpin1 = "x"
pinvalue = ["x", "x", "x", "x", "x", "x", "x", "x", "x", "x", "x", "x", "x", "x", "x", "x", "x", "x", "x", "x"]
oldpinvalue = ["y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y"]

print()
print()
print()
print()
print()
print()
print()
print "----------------------------------------------"

ArduinoPort = serial.Serial('/dev/ttyUSB0', 115200, timeout=0.1)
# ArduinoPort = serial.Serial('/dev/cu.usbserial-A500SYZT', 115200, timeout=0.1)

import SocketServer

SocketServer.TCPServer.allow_reuse_address = True
from SocketServer import (TCPServer, StreamRequestHandler)

messageQueue = ""


def read_socket():
    """
    Check socket for messages

    Credit: Olivier Djian
    """

    #global messageQueue

    if (messageQueue != ""):
        print("Message received in socket messageQueue")
        message = stripped(messageQueue)

        print("Message from WEEWX [v2]")

    timestamp = time.strftime('%Y-%m-%d %H:%M:%S')

    print "------------------------------------------------"
    print "Incoming message from socket"
    print "Send= " + message
    print "Date/Time= " + timestamp

    try:
        print("Decode message")
    except KeyError:
        logger.error("Unrecognizable packet. Line: " + _line())
    except KeyboardInterrupt:
            print("Received keyboard interrupt")
            print("Close server socket")
            serversocket.netAdapter.shutdown()


class NetRequestHandler(StreamRequestHandler):
    def handle(self):
        print("Client connected to [%s:%d]" % self.client_address)
        #lg = StreamRequestHandler.recv(1024)
        lg = self.rfile.readline()
        messageQueue = lg
        #print("Message read from socket : " + lg)
        print("Send " + "$$" + lg + " to Arduino")
        ArduinoPort.write("$$" + lg)
        print("Send " + "OK to jeeDom")
        self.request.sendall("Ook\n")
        self.wfile.write("OoK" + '\n')
        self.netAdapterClientConnected = False
        print("Client disconnected from [%s:%d]" % self.client_address)

class ArduiDomSocketAdapter(object, StreamRequestHandler):
    def __init__(self, address='localhost', port=58174):
        self.Address = address
        self.Port = port

        self.netAdapter = TCPServer((self.Address, self.Port), NetRequestHandler)
        if self.netAdapter:
            self.netAdapterRegistered = True
            threading.Thread(target=self.loopNetServer, args=()).start()

    def loopNetServer(self):
        print("loopNetServer Thread started")
        print("Listening on : [%s:%d]" % (self.Address, self.Port))
        self.netAdapter.serve_forever()
        print("loopNetServer Thread stopped")


# logger = logging.getLogger('rfxcmd')UdpServer = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
#UdpServer.bind(("127.0.0.1", 58174))


#def queryArduino(datats, rep):
#    print YELLOW + ">" + datats
#    ArduinoPort.write(datats)
#    while True:
#        datarcv = ArduinoPort.readline()
#        if datarcv.startswith(rep) == 1:
#            print GREEN + "<" + datarcv
#            return datarcv


def ArduinoRead():
    data = ''
    while True:
        data = ArduinoPort.readline()
        if data != '':
            # print GREEN + "<" + data
            return data
        return ''


ArduiDomSocketAdapter()
#read_socket()

print GRIS
print "######################################"
print "# ArduiDom - Arduino Link for jeeDom #"
print "######################################"

ArduinoPort.write("$$TS" + '\r\n')

while True:  # This is the main loop of program...................................................................

    #    print "Check upd port in"
    #    line, addr = UdpServer.recvfrom(2048)
    #    if (line != ''):
    #        print "There is data on udp"
    #        line = str(line)
    #        print "Received = ", line

    line = ArduinoRead()
    line = ""

    if line != '':
        #    for letter in line:
        #        print letter + " = " + letter.encode("hex")

        line = line.replace('\n', '')
        line = line.replace('\r', '')
        print BLUE + "Arduino > [", line, "]"

        if line.find("PIN") > -1:
            if line.find("SET PIN") != -1:
                pinNumber = line[3]
                pinValue = line[5]
                print(YELLOW + "Pin number " + pinNumber)
                print("Value " + pinValue)
                cmd = 'nice -n 19 /usr/bin/php /usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php '
                cmd += pinNumber
                cmd += "="
                cmd += pinValue
                print(RED + cmd)
                #subprocess.Popen(cmd, shell=True)

    if (messageQueue != ""):
        print(messageQueue)


    #for pinnb in range(1, 13 + 1):
    #    pinfile = open(
    #        '/usr/share/nginx/www/jeedom/plugins/arduidom/ressources/arduidom/pins_conf/pin' + str(pinnb) + "_d" + str(
    #            pinnb - 1) + "/value", 'r')
    #    pinvalue[pinnb] = pinfile.readline()
    #    pinfile.close()
    #    if oldpinvalue[pinnb] != pinvalue[pinnb]:
    #        print(
    #        "Value of pin " + str(pinnb) + " Changed from " + str(oldpinvalue[pinnb]) + " to " + str(pinvalue[pinnb]))
    #        cmd = "$$SP"
    #        cmd += str(pinnb).zfill(2)
    #        cmd += pinvalue[pinnb]
    #        ArduinoPort.write(cmd)
    #        print("Send " + cmd + " to Arduino")
    #        oldpinvalue[pinnb] = pinvalue[pinnb]

ArduinoPort.close()
