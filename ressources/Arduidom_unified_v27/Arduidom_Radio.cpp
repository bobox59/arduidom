/*
  Arduidom_Radio FORK of RCSwitch - Arduino libary for remote control outlet switches
  Copyright (c) 2011 Suat �zg�r.  All right reserved.

  Contributors:
  - Andre Koehler / info(at)tomate-online(dot)de
  - Gordeev Andrey Vladimirovich / gordeev(at)openpyro(dot)com
  - Skineffect / http://forum.ardumote.com/viewtopic.php?f=2&t=46
  - Dominik Fischer / dom_fischer(at)web(dot)de
  - Frank Oltmanns / <first name>.<last name>(at)gmail(dot)com
  - Andreas Steinel / A.<lastname>(at)gmail(dot)com

  Project home: http://code.google.com/p/rc-switch/

  This library is free software; you can redistribute it and/or
  modify it under the terms of the GNU Lesser General Public
  License as published by the Free Software Foundation; either
  version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public
  License along with this library; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

#include "Arduidom_Radio.h"

#if not defined( RCSwitchDisableReceiving )
unsigned long RCSwitch::nReceivedValue = 0; // NULL
unsigned long RCSwitch::nReceivedAddr = 0; // NULL
unsigned int RCSwitch::nReceivedBitlength = 0;
unsigned int RCSwitch::nReceivedDelay = 0;
byte RCSwitch::nReceivedProtocol = 0;
int RCSwitch::nReceiveTolerance = 60;
#endif
unsigned int RCSwitch::timings[RCSWITCH_MAX_CHANGES];

RCSwitch::RCSwitch() {
    this->nTransmitterPin = -1;
    this->setPulseLength(350);
    this->setRepeatTransmit(10);
    this->setProtocol(1);
#if not defined( RCSwitchDisableReceiving )
    this->nReceiverInterrupt = -1;
  this->setReceiveTolerance(60);
  RCSwitch::nReceivedValue = 0; // NULL
  RCSwitch::nReceivedAddr = 0; // NULL
#endif
}

/**
  * Sets the protocol to send.
  */
void RCSwitch::setProtocol(int nProtocol) {
    this->nProtocol = nProtocol;
    if (nProtocol == 1){
        this->setPulseLength(350);
    }
    else if (nProtocol == 2) {
        this->setPulseLength(650);
    }
    else if (nProtocol == 3) {
        this->setPulseLength(100);
    }
        //HOMEEASY
    else if (nProtocol == 4){
        this->setPulseLength(275);
    }
}

/**
  * Sets the protocol to send with pulse length in microseconds.
  */
void RCSwitch::setProtocol(int nProtocol, int nPulseLength) {
    this->nProtocol = nProtocol;
    this->setPulseLength(nPulseLength);
}


/**
  * Sets pulse length in microseconds
  */
void RCSwitch::setPulseLength(int nPulseLength) {
    this->nPulseLength = nPulseLength;
}

/**
 * Sets Repeat Transmits
 */
void RCSwitch::setRepeatTransmit(int nRepeatTransmit) {
    this->nRepeatTransmit = nRepeatTransmit;
}

/**
 * Set Receiving Tolerance
 */
#if not defined( RCSwitchDisableReceiving )
void RCSwitch::setReceiveTolerance(int nPercent) {
  RCSwitch::nReceiveTolerance = nPercent;
}
#endif


/**
 * Enable transmissions
 *
 * @param nTransmitterPin    Arduino Pin to which the sender is connected to
 */
void RCSwitch::enableTransmit(int nTransmitterPin) {
    this->nTransmitterPin = nTransmitterPin;
    pinMode(this->nTransmitterPin, OUTPUT);
}

/**
  * Disable transmissions
  */
void RCSwitch::disableTransmit() {
    this->nTransmitterPin = -1;
}


/**
 * @param sCodeWord   /^[10FS]*$/  -> see getCodeWord
 */
void RCSwitch::sendTriState(char* sCodeWord) {
    for (int nRepeat=0; nRepeat<nRepeatTransmit; nRepeat++) {
        int i = 0;
        while (sCodeWord[i] != '\0') {
            switch(sCodeWord[i]) {
                case '0':
                    this->sendT0();
                    break;
                case 'F':
                    this->sendTF();
                    break;
                case '1':
                    this->sendT1();
                    break;
            }
            i++;
        }
        this->sendSync();
    }
}

void RCSwitch::send(unsigned long Code, unsigned int length) {
    this->send( this->dec2binWzerofill(Code, length) );
}

void RCSwitch::send(unsigned long remote, unsigned long button, boolean onoff){
    int lastprotocol = this->nProtocol;
    int lastRepeatTransmit = this->nRepeatTransmit;

    setProtocol(4);
    setRepeatTransmit(1);

    char* remote_bin = this->dec2binWzerofill(remote, 26);
    char* button_bin = this->dec2binWzerofill2(button, 4);
    //Serial.println(this->dec2binWzerofill(remote, 26));
    //char* remote_bin = (this->dec2binWcharfill(remote, 26, '0'));
    //char* button_bin = (this->dec2binWcharfill(button, 4, '0'));
    //Serial.println(remote_bin);
    //Serial.println(button_bin);
    //Set Protocol to Home Easy (Chacon/DIO)
    //Latch1
    this->transmit(1,36);
    //Latch2
    this->transmit(1,10);
    digitalWrite(this->nTransmitterPin, HIGH);

    //Remote (Recipient Code)
    this->send(remote_bin);

    //No Group
    this->sendPair(false);

    //ON/OFF
    this->sendPair(onoff);

    //Button (Sender Code)
    this->send(button_bin);

    //End Message
    this->send0();
    delay(10);

    setProtocol(lastprotocol);
    setRepeatTransmit(lastRepeatTransmit);

}

void RCSwitch::send(char* sCodeWord) {
    for (int nRepeat=0; nRepeat<nRepeatTransmit; nRepeat++) {
        int i = 0;
        while (sCodeWord[i] != '\0') {

            if(this->nProtocol != 4){
                switch(sCodeWord[i]) {
                    case '0':
                        this->send0();
                        break;
                    case '1':
                        this->send1();
                        break;
                }
            }
            else if(this->nProtocol == 4){
                switch(sCodeWord[i]) {
                    case '0':
                        this->sendPair(false);
                        break;
                    case '1':
                        this->sendPair(true);
                        break;
                }
            }

            i++;
        }
        this->sendSync();
    }
}

void RCSwitch::transmit(int nHighPulses, int nLowPulses) {
    #if not defined ( RCSwitchDisableReceiving )
        boolean disabled_Receive = false;
        int nReceiverInterrupt_backup = nReceiverInterrupt;
    #endif
    if (this->nTransmitterPin != -1) {
    #if not defined( RCSwitchDisableReceiving )
        if (this->nReceiverInterrupt != -1) {
            this->disableReceive();
            disabled_Receive = true;
        }
    #endif
    digitalWrite(this->nTransmitterPin, HIGH);
    delayMicroseconds( this->nPulseLength * nHighPulses);
    digitalWrite(this->nTransmitterPin, LOW);
    delayMicroseconds( this->nPulseLength * nLowPulses);

    #if not defined( RCSwitchDisableReceiving )
        if(disabled_Receive){
            this->enableReceive(nReceiverInterrupt_backup);
        }
    #endif
    }
}
/**
 * Sends a "0" Bit
 *                       _
 * Waveform Protocol 1: | |___
 *                       _
 * Waveform Protocol 2: | |__
 */
void RCSwitch::send0() {
    if (this->nProtocol == 1){
        this->transmit(1,3);
    }
    else if (this->nProtocol == 2) {
        this->transmit(1,2);
    }
    else if (this->nProtocol == 3) {
        this->transmit(4,11);
    }
    else if (this->nProtocol == 4){
        this->transmit(1,1);
    }
}

/**
 * Sends a "1" Bit
 *                       ___
 * Waveform Protocol 1: |   |_
 *                       __
 * Waveform Protocol 2: |  |_
 */
void RCSwitch::send1() {
    if (this->nProtocol == 1){
        this->transmit(3,1);
    }
    else if (this->nProtocol == 2) {
        this->transmit(2,1);
    }
    else if (this->nProtocol == 3) {
        this->transmit(9,6);
    }
    else if (this->nProtocol == 4) {
        this->transmit(1,5);
    }
}

void RCSwitch::sendPair(boolean b) {
    if(b)
    {
        this->send1();
        this->send0();
    }
    else
    {
        this->send0();
        this->send1();
    }
}




/**
 * Sends a Tri-State "0" Bit
 *            _     _
 * Waveform: | |___| |___
 */
void RCSwitch::sendT0() {
    this->transmit(1,3);
    this->transmit(1,3);
}

/**
 * Sends a Tri-State "1" Bit
 *            ___   ___
 * Waveform: |   |_|   |_
 */
void RCSwitch::sendT1() {
    this->transmit(3,1);
    this->transmit(3,1);
}

/**
 * Sends a Tri-State "F" Bit
 *            _     ___
 * Waveform: | |___|   |_
 */
void RCSwitch::sendTF() {
    this->transmit(1,3);
    this->transmit(3,1);
}

/**
 * Sends a "Sync" Bit
 *                       _
 * Waveform Protocol 1: | |_______________________________
 *                       _
 * Waveform Protocol 2: | |__________
 */
void RCSwitch::sendSync() {

    if (this->nProtocol == 1){
        this->transmit(1,31);
    }
    else if (this->nProtocol == 2) {
        this->transmit(1,10);
    }
    else if (this->nProtocol == 3) {
        this->transmit(1,71);
    }
}

#if not defined( RCSwitchDisableReceiving )
/**
 * Enable receiving data
 */
void RCSwitch::enableReceive(int interrupt) {
    this->nReceiverInterrupt = interrupt;
    this->enableReceive();
}

void RCSwitch::enableReceive() {
    if (this->nReceiverInterrupt != -1) {
        RCSwitch::nReceivedValue = 0; // NULL
        RCSwitch::nReceivedAddr = 0; // NULL
        RCSwitch::nReceivedBitlength = 0; // NULL
        attachInterrupt(this->nReceiverInterrupt, handleInterrupt, CHANGE);
    }
}

/**
 * Disable receiving data
 */
void RCSwitch::disableReceive() {
    detachInterrupt(this->nReceiverInterrupt);
    this->nReceiverInterrupt = -1;
}

bool RCSwitch::available() {
    return RCSwitch::nReceivedValue != 0; // NULL
}

void RCSwitch::resetAvailable() {
    RCSwitch::nReceivedValue = 0; // NULL
    RCSwitch::nReceivedAddr = 0; // NULL
}

unsigned long RCSwitch::getReceivedAddr() {
    return RCSwitch::nReceivedAddr;
}

unsigned long RCSwitch::getReceivedValue() {
    return RCSwitch::nReceivedValue;
}

unsigned int RCSwitch::getReceivedBitlength() {
    return RCSwitch::nReceivedBitlength;
}

unsigned int RCSwitch::getReceivedDelay() {
    return RCSwitch::nReceivedDelay;
}

byte RCSwitch::getReceivedProtocol() {
    return RCSwitch::nReceivedProtocol;
}

unsigned int* RCSwitch::getReceivedRawdata() {
    return RCSwitch::timings;
}

/**
 *
 */
bool RCSwitch::receiveProtocol1(unsigned int changeCount){

    unsigned long code = 0;
    unsigned long delay = RCSwitch::timings[0] / 31;
    unsigned long delayTolerance = delay * RCSwitch::nReceiveTolerance * 0.01;

    for (int i = 1; i<changeCount ; i=i+2) {

        if (RCSwitch::timings[i] > delay-delayTolerance && RCSwitch::timings[i] < delay+delayTolerance && RCSwitch::timings[i+1] > delay*3-delayTolerance && RCSwitch::timings[i+1] < delay*3+delayTolerance) {
            code = code << 1;
        } else if (RCSwitch::timings[i] > delay*3-delayTolerance && RCSwitch::timings[i] < delay*3+delayTolerance && RCSwitch::timings[i+1] > delay-delayTolerance && RCSwitch::timings[i+1] < delay+delayTolerance) {
            code+=1;
            code = code << 1;
        } else {
            // Failed
            i = changeCount;
            code = 0;
        }
    }
    code = code >> 1;
    if (changeCount > 6) {    // ignore < 4bit values as there are no devices sending 4bit values => noise
        RCSwitch::nReceivedValue = code;
        RCSwitch::nReceivedAddr = 1;
        RCSwitch::nReceivedBitlength = changeCount / 2;
        RCSwitch::nReceivedDelay = delay;
        RCSwitch::nReceivedProtocol = 1;
    }

    if (code == 0){
        return false;
    }else if (code != 0){
        return true;
    }
}

bool RCSwitch::receiveProtocol2(unsigned int changeCount){

    unsigned long code = 0;
    unsigned long delay = RCSwitch::timings[0] / 10;
    unsigned long delayTolerance = delay * RCSwitch::nReceiveTolerance * 0.01;

    for (int i = 1; i<changeCount ; i=i+2) {

        if (RCSwitch::timings[i] > delay-delayTolerance && RCSwitch::timings[i] < delay+delayTolerance && RCSwitch::timings[i+1] > delay*2-delayTolerance && RCSwitch::timings[i+1] < delay*2+delayTolerance) {
            code = code << 1;
        } else if (RCSwitch::timings[i] > delay*2-delayTolerance && RCSwitch::timings[i] < delay*2+delayTolerance && RCSwitch::timings[i+1] > delay-delayTolerance && RCSwitch::timings[i+1] < delay+delayTolerance) {
            code+=1;
            code = code << 1;
        } else {
            // Failed
            i = changeCount;
            code = 0;
        }
    }
    code = code >> 1;
    if (changeCount > 6) {    // ignore < 4bit values as there are no devices sending 4bit values => noise
        RCSwitch::nReceivedValue = code;
        RCSwitch::nReceivedAddr = 1;
        RCSwitch::nReceivedBitlength = changeCount / 2;
        RCSwitch::nReceivedDelay = delay;
        RCSwitch::nReceivedProtocol = 2;
    }

    if (code == 0){
        return false;
    } else if (code != 0){
        return true;
    }


}

/** Protocol 3 is used by BL35P02.
 *
 */
bool RCSwitch::receiveProtocol3(unsigned int changeCount){

    unsigned long codeaddr = 0;
    unsigned long code = 0;
    unsigned long delay = RCSwitch::timings[0] / 10;
    unsigned long delayTolerance = delay * RCSwitch::nReceiveTolerance * 0.01;

    for (int i = 2; i<49 ; i=i+2) {

        if  ( RCSwitch::timings[i+1] > delay*2 - delayTolerance && RCSwitch::timings[i+1] < delay*2 + delayTolerance) {
            codeaddr = codeaddr << 1;
		} else if (RCSwitch::timings[i+1] > delay - delayTolerance && RCSwitch::timings[i+1] < delay + delayTolerance) {
            codeaddr+=1;
            codeaddr = codeaddr << 1;
        } else {
            // Failed
            i = changeCount;
            codeaddr = 0;
            code = 0;
        }
    }
    for (int i = 50; i<97 ; i=i+2) {

        if  ( RCSwitch::timings[i+1] > delay*2 - delayTolerance && RCSwitch::timings[i+1] < delay*2 + delayTolerance) {
            code = code << 1;
		} else if (RCSwitch::timings[i+1] > delay - delayTolerance && RCSwitch::timings[i+1] < delay + delayTolerance) {
            code+=1;
            code = code << 1;
        } else {
            // Failed
            i = changeCount;
            code = 0;
        }
    }

    code = code >> 1;
    codeaddr = codeaddr >> 1;
    if (changeCount > 90) {    // ignore < 24bit values as there are no devices sending 24bit values => noise
        RCSwitch::nReceivedValue = code;
        RCSwitch::nReceivedAddr = codeaddr;
        RCSwitch::nReceivedBitlength = 24; // 48 bits protocol but as 16 addr + 32 data
        RCSwitch::nReceivedDelay = delay;
        RCSwitch::nReceivedProtocol = 3;
    }

    if (code == 0){
        return false;
    } else if (code != 0) {
        return true;
    }
}

/** Protocol 4 is used by HOMEEASY.
 *
 */
bool RCSwitch::receiveProtocol4(unsigned int changeCount){

	unsigned long codeaddr = 0;
	unsigned int codegrp = 0;
	unsigned long code = 0;
	unsigned int codedev = 0;
	unsigned long delay = 275;
	unsigned long delayTolerance = delay * RCSwitch::nReceiveTolerance * 0.01;

	for (int i = 0; i<32*4 ; i=i+4) {
		if ( RCSwitch::timings[i+1] > delay - delayTolerance && RCSwitch::timings[i+1] < delay + delayTolerance
		  && RCSwitch::timings[i+2] > delay - delayTolerance && RCSwitch::timings[i+2] < delay + delayTolerance
	      && RCSwitch::timings[i+3] > delay - delayTolerance && RCSwitch::timings[i+3] < delay + delayTolerance
	      && RCSwitch::timings[i+4] > 1300 - delayTolerance && RCSwitch::timings[i+4] < 1300 + delayTolerance) {
            if (i < 26*4) codeaddr = codeaddr << 1;
            if (i == 26*4) codegrp = codegrp << 1;
            if (i == 27*4) code = code << 1;
            if (i > 27*4 && i < 32*4) codedev = codedev << 1;
        } else if( RCSwitch::timings[i+1] > delay - delayTolerance && RCSwitch::timings[i+1] < delay + delayTolerance
        		&& RCSwitch::timings[i+2] > 1300 - delayTolerance && RCSwitch::timings[i+2] < 1300 + delayTolerance
				&& RCSwitch::timings[i+3] > delay - delayTolerance && RCSwitch::timings[i+3] < delay + delayTolerance
				&& RCSwitch::timings[i+4] > delay - delayTolerance && RCSwitch::timings[i+4] < delay + delayTolerance) {
            if (i < 26*4) {
                codeaddr+=1;
                codeaddr = codeaddr << 1;
            }
            if (i == 26*4) {
                codegrp+=1;
                codegrp = codegrp << 1;
            }
            if (i == 27*4) {
                code+=1;
                code = code << 1;
            }
            if (i > 27*4 && i < 32*4) {
                codedev+=1;
                codedev = codedev << 1;
            }
        } else {
            // Failed
            i = changeCount;
            codeaddr = 0;
            code = 0;
            codegrp = 0;
            codedev = 0;
        }
    }
	codegrp = codegrp >> 1;
	codedev = codedev >> 1;
    code = code >> 1;
	codeaddr = codeaddr >> 1;
	if (changeCount > 128) {    // ignore < 64bit values as there are no devices sending 64bit values => noise
      	RCSwitch::nReceivedValue = codeaddr;
        RCSwitch::nReceivedAddr = 100*code + 1000*codegrp + 1*codedev;
        RCSwitch::nReceivedBitlength = 32; // 64 bits protocol but as 16 addr + 32 data
        RCSwitch::nReceivedDelay = delay;
        RCSwitch::nReceivedProtocol = 4;
    }
    if (code == 0 && codeaddr == 0){
    	return false;
    } else {
    	return true;
    }
}

void RCSwitch::handleInterrupt() {

    static unsigned int duration;
    static unsigned int changeCount;
    static unsigned long lastTime;
    static unsigned int repeatCount;

    long time = micros();
    duration = time - lastTime;
    if (duration > 2200 && duration > RCSwitch::timings[0] - 200 && duration < RCSwitch::timings[0] + 200) {
        repeatCount++;
        changeCount--;
        if (repeatCount > 1) {
            if (changeCount == 49) if (receiveProtocol1(changeCount) == false) repeatCount = 0;
	        if (changeCount == 97) if (receiveProtocol3(changeCount) == false) repeatCount = 0;
            //failed
        }
        changeCount = 0;
    } else if (duration > 9500 && duration > RCSwitch::timings[0] - 200 && duration < RCSwitch::timings[0] + 10200) {
        repeatCount++;
        changeCount--;
        if (repeatCount > 1) {
	        if (changeCount > 128) {
	  	        if (receiveProtocol4(changeCount) == false) repeatCount = 0;
	        }
	        //failed
        }
        changeCount = 0;
    } else if (duration > 2200) {
        changeCount = 0;
    }

    if (changeCount >= RCSWITCH_MAX_CHANGES) {
        changeCount = 0;
        repeatCount = 0;
    }
    RCSwitch::timings[changeCount++] = duration;
    lastTime = time;
}

/**
  * Turns a decimal value to its binary representation
  */
#endif
char* RCSwitch::dec2binWzerofill(unsigned long Dec, unsigned int bitLength){
    return dec2binWcharfill(Dec, bitLength, '0');
}

char* RCSwitch::dec2binWcharfill(unsigned long Dec, unsigned int bitLength, char fill){
    static char bin[64];
    unsigned int i=0;

    while (Dec > 0) {
        bin[32+i++] = ((Dec & 1) > 0) ? '1' : fill;
        Dec = Dec >> 1;
    }

    for (unsigned int j = 0; j< bitLength; j++) {
        if (j >= bitLength - i) {
            bin[j] = bin[ 31 + i - (j - (bitLength - i)) ];
        } else {
            bin[j] = fill;
        }
    }
    bin[bitLength] = '\0';

    return bin;
}

char* RCSwitch::dec2binWzerofill2(unsigned long Dec, unsigned int bitLength){
    return dec2binWcharfill2(Dec, bitLength, '0');
}

char* RCSwitch::dec2binWcharfill2(unsigned long Dec, unsigned int bitLength, char fill){
    static char bin[64];
    unsigned int i=0;

    while (Dec > 0) {
        bin[32+i++] = ((Dec & 1) > 0) ? '1' : fill;
        Dec = Dec >> 1;
    }

    for (unsigned int j = 0; j< bitLength; j++) {
        if (j >= bitLength - i) {
            bin[j] = bin[ 31 + i - (j - (bitLength - i)) ];
        } else {
            bin[j] = fill;
        }
    }
    bin[bitLength] = '\0';
    return bin;
}

