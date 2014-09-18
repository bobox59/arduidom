__author__ = 'bmasquelier'
#
# Python script for Arduidom communication
#

import serial
import thread
from socket import *
import time
import subprocess

VIOLET = '\033[95m'
BLUE = '\033[94m'
GREEN = '\033[92m'
YELLOW = '\033[93m'
RED = '\033[91m'
data = ""
pin1 = ""
oldpin1 = "x"
pinvalue = ["x", "x", "x", "x", "x", "x", "x", "x", "x", "x", "x", "x", "x", "x", ".", "xxx", "xxx", "xxx", "xxx", "xxx"]
pinmode = ["z", "z", "z", "z", "z", "z", "z", "z", "z", "z", "z", "z", "z", "z", ".", "z", "z", "z", "z", "z", "z"]
oldpinvalue = ["y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y", "y"]
count = 1
arduino_rx = "aaa"
currentline = 99
radiorxpin = 0

def print_time(threadName, delay):
    global pinvalue
    global pinmode
    global count
    global arduino_rx
    while True:
        time.sleep(delay)
        count += 1
        if (count > 5):
            count = 0
            print("Send $$RF to Arduino")
            arduino_rx = ""
            ArduinoPort.write("$$RF")
            while (arduino_rx == ""):
                time.sleep(0.01)

            print("arduino_rx=" + arduino_rx)
            pinvalue = arduino_rx.rsplit(',')

        print VIOLET + "%s: %s" % ( threadName, time.ctime(time.time()) )
        print "          D00  D01  D02  D03  D04  D05  D06  D07  D08  D09  D10  D11  D12  D13   x    A0     A1     A2     A3     A4     A5"
        print "pinvalue=" + str(pinvalue)
        print " pinmode=" + str(pinmode)
        print "arduino_rx=" + arduino_rx

def handler(clientsocket, clientaddr):
    global pinvalue
    global pinmode
    global arduino_rx
    print GREEN + "Count=" + str(count)
    print GREEN + "Accepted connection from: " + str(clientaddr)

    while 1:
        data = clientsocket.recv(1024)
        data = data.replace('\n', '')
        data = data.replace('\r', '')
        if not data:
            break
        else:
            print ("Data From TCP : " + data)
            if data[0:2] == 'HI':
                print("HI Received !")
                print("Send " + "$$" + data + " to Arduino")
                arduino_rx = ""
                ArduinoPort.write("$$" + data)
                print("Wait Arduino Response...")
                while (arduino_rx == ""):
                    time.sleep(0.1)
                print ("Arduino says " + arduino_rx)
                arduino_rx = ""
                msg = data + "_OK"
                print("Send " + msg + " to jeeDom")
                clientsocket.send(msg)

            if data[0:2] == 'CP':
                print("CP Received !")
                print("Send " + "$$" + data + " to Arduino")
                arduino_rx = ""
                ArduinoPort.write("$$" + data)
                print("Wait Arduino Response...")
                while (arduino_rx == ""):
                    time.sleep(0.1)
                print ("Arduino says " + arduino_rx)
                arduino_rx = ""
                msg = data + "_OK"
                print("Send " + msg + " to jeeDom")
                clientsocket.send(msg)

            if data[0:2] == 'SP':
                print("SP Received !")
                print("Send " + "$$" + data + " to Arduino")
                ArduinoPort.write("$$" + data)
                msg = data + "_OK"
                print("Send " + msg + " to jeeDom")
                clientsocket.send(msg)

            if data[0:2] == 'SR':
                print("SR Received !")
                print("Send " + "$$" + data + " to Arduino")
                ArduinoPort.write("$$" + data)
                msg = data + "_OK"
                print("Send " + msg + " to jeeDom")
                clientsocket.send(msg)

            if data[0:2] == 'GP':
                pintoread = ((10 * int(data[2])) + int(data[3]))
                print("GP " + str(pintoread) + "Received !")
                print("Send $$RF to Arduino")
                arduino_rx = ""
                ArduinoPort.write("$$RF")
                while (arduino_rx == ""):
                    time.sleep(0.01)

                print("arduino_rx=" + arduino_rx)
                pinvalue = arduino_rx.rsplit(',')
                msg = "GP" + str(pintoread).zfill(2) + "=" + pinvalue[pintoread] + "_OK"
                print("Send " + msg + " to jeeDom")
                clientsocket.send(msg)


            if data[0:2] == 'RF': # *** Refresh Datas
                print("RF Received !")
                print("Send $$RF to Arduino")
                arduino_rx = ""
                ArduinoPort.write("$$RF")
                while (arduino_rx == ""):
                    time.sleep(0.01)

                print("arduino_rx=" + arduino_rx)
                pinvalue = arduino_rx.rsplit(',')


            break
    print("Close Socket")
    clientsocket.close()

def tcpServerThread(threadName, delay):
    global pinvalue
    global pinmode
    global arduino_rx
    print GREEN + "Thread " + threadName + " Started."
    host = "127.0.0.1"
    port = 58174
    addr = (host, port)
    serversocket = socket(AF_INET, SOCK_STREAM)
    serversocket.bind(addr)
    serversocket.listen(0)
    if not serversocket:
        exit()

    while 1:
        print GREEN + "Server is waiting for connection..."
        clientsocket, clientaddr = serversocket.accept()
        thread.start_new_thread(handler, (clientsocket, clientaddr))

    print "Server Stop to listening !"
    serversocket.close()


def COMServer(threadName, delay):
    global pinvalue
    global pinmode
    global arduino_rx
    global currentline
    global radiorxpin
    print YELLOW + "Thread " + threadName + " Started."


    while True:  # This is the main loop of program...................................................................

        line = ''
        while True:
            line = ArduinoPort.readline()
            if line != '':
                break

        if line != '':
            line = line.replace('\n', '')
            line = line.replace('\r', '')
            #if (line != arduino_rx): #Anti repetition
            arduino_rx = line
            print BLUE + "Arduino > [", line, "]"

            if line.find("Pin") > -1:  # Definition des pin modes via CP ou RESET
                if line.find(" is ") > -1:
                    offset = 0
                    if line.find("APin") > -1:
                        offset = 1
                    print("Pin Mode configuration detected.")
                    currentline = int(line[4+offset])
                    if line[5+offset] != ' ':
                        currentline = 10 + int(line[5+offset])
                    if offset == 1:
                        currentline += 15
                    print("Current line = " + str(currentline))
                    if line.find("OUTPUT") > -1:
                        pinmode[currentline] = "o"
                    if line.find("INPUT") > -1:
                        pinmode[currentline] = "i"
                    if line.find("A-INPUT") > -1:
                        pinmode[currentline] = "a"
                    if line.find("DISABLED") > -1:
                        pinmode[currentline] = "z"
                    if line.find("Radio RX") > -1:
                        pinmode[currentline] = "r"
                        radiorxpin = currentline
                    if line.find("Radio TX") > -1:
                        pinmode[currentline] = "t"
                    if line.find("PWM") > -1:
                        pinmode[currentline] = "p"

            if line.find("PIN") > -1:
                #print "CRKKKK:" + str(line.find("SET"))
                if line.find("SET") < 0:
                    pinnumber = int(line[3])
                    pinvalue[pinnumber] = int(line[5])
                    print(BLUE + "Pin number " + str(pinnumber))
                    print("Value " + str(pinvalue[pinnumber]))
                    cmd = 'nice -n 19 /usr/bin/php /usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php '
                    cmd += pinnumber
                    cmd += "="
                    cmd += pinvalue[pinnumber]
                    print(RED + cmd)

            if line.find("RFD:") > -1:
                print "Radio Code Received:"

                if line.find("SET") < 0:
                    pinnumber = radiorxpin
                    print(BLUE + "Pin number " + str(pinnumber))
                    print("Value " + str(pinvalue[pinnumber]))
                    cmd = 'nice -n 19 /usr/bin/php /usr/share/nginx/www/jeedom/plugins/arduidom/core/php/jeeArduidom.php '
                    cmd += str(pinnumber)
                    cmd += "="
                    cmd += '"' + line + '"'
                    print(RED + cmd)
                    subprocess.Popen(cmd, shell=True)




print RED
print
print
print
print
print
print
print "######################################"
print "# ArduiDom - Arduino Link for jeeDom #"
print "#                        by Bobox 59 #"
print "######################################"
print
print "Opening Arduino USB Port..."
ArduinoPort = serial.Serial('/dev/ttyUSB0', 115200, timeout=0.1)
# ArduinoPort = serial.Serial('/dev/cu.usbserial-A500SYZT', 115200, timeout=0.1)


print YELLOW + "Launch USB Thread..."

try:
    thread.start_new_thread( COMServer, ("TH-COMServer", 1))
except:
    print "Error with Thread TH-COMServer"

time.sleep(1)
print RED + "En attente de l'arduino (HELLO)"
ArduinoPort.write("$$HI")

while (arduino_rx !="HELLO"):
    time.sleep(0.1)
arduino_rx = ""

print ("Launch TCP Thread...")
try:
    thread.start_new_thread( tcpServerThread, ("TH-TcpServer", 1))
except:
    print "Error with Thread TH-TcpServer"

try:
    thread.start_new_thread( print_time, ("TH-time", 2))
except:
    print "Error with Thread TH-Time"

while 1:
    pass
