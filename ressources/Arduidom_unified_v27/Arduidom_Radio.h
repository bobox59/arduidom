//
// Created by Benoit Masquelier on 07/02/2016.
//

#ifndef ARDUIDOM_NET_ARDUIDOM_RADIO_H
#define ARDUIDOM_NET_ARDUIDOM_RADIO_H

/*
  Arduidom_Radio, FORK of RCSwitch - Arduino libary for remote control outlet switches
  Copyright (c) 2011 Suat �zg�r.  All right reserved.

  Contributors:
  - Andre Koehler / info(at)tomate-online(dot)de
  - Gordeev Andrey Vladimirovich / gordeev(at)openpyro(dot)com
  - Skineffect / http://forum.ardumote.com/viewtopic.php?f=2&t=46
  - Dominik Fischer / dom_fischer(at)web(dot)de
  - Frank Oltmanns / <first name>.<last name>(at)gmail(dot)com

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

#if defined(ARDUINO) && ARDUINO >= 100
    #include "Arduino.h"
#elif defined(ENERGIA) // LaunchPad, FraunchPad and StellarPad specific
    #include "Energia.h"
#else
    #include "WProgram.h"
#endif


// At least for the ATTiny X4/X5, receiving has to be disabled due to
// missing libm depencies (udivmodhi4)
#if defined( __AVR_ATtinyX5__ ) or defined ( __AVR_ATtinyX4__ )
    #define RCSwitchDisableReceiving
#endif

// Number of maximum High/Low changes per packet.
// We can handle up to (unsigned long) => 32 bit * 2 H/L changes per bit + 2 for sync
#define RCSWITCH_MAX_CHANGES 134

#define PROTOCOL3_SYNC_FACTOR   10
#define PROTOCOL3_0_HIGH_CYCLES  1
#define PROTOCOL3_0_LOW_CYCLES  2
#define PROTOCOL3_1_HIGH_CYCLES  2
#define PROTOCOL3_1_LOW_CYCLES   1

class RCSwitch {

public:
    RCSwitch();

    void sendTriState(char* Code);
    void send(unsigned long Code, unsigned int length);
    void send(unsigned long remote, unsigned long button, boolean onoff);
    void send(char* Code);

    static char* dec2binWzerofill(unsigned long dec, unsigned int length);
    static char* dec2binWzerofill2(unsigned long dec, unsigned int length);
    static char* dec2binWcharfill(unsigned long dec, unsigned int length, char fill);
    static char* dec2binWcharfill2(unsigned long dec, unsigned int length, char fill);

#ifndef RCSwitchDisableReceiving
    void enableReceive(int interrupt);
    void enableReceive();
    void disableReceive();
    bool available();
    void resetAvailable();


    unsigned long getReceivedValue();
    unsigned long getReceivedAddr();
    unsigned int getReceivedBitlength();
    unsigned int getReceivedDelay();
    byte getReceivedProtocol();
    unsigned int* getReceivedRawdata();
#endif

    void enableTransmit(int nTransmitterPin);
    void disableTransmit();
    void setPulseLength(int nPulseLength);
    void setRepeatTransmit(int nRepeatTransmit);
#ifndef RCSwitchDisableReceiving
    void setReceiveTolerance(int nPercent);
#endif
    void setProtocol(int nProtocol);
    void setProtocol(int nProtocol, int nPulseLength);

private:
    void sendT0();
    void sendT1();
    void sendTF();
    void send0();
    void send1();
    void sendSync();
    void sendPair(boolean b);
    void transmit(int nHighPulses, int nLowPulses);



#ifndef RCSwitchDisableReceiving
    static void handleInterrupt();
    static bool receiveProtocol1(unsigned int changeCount);
    static bool receiveProtocol2(unsigned int changeCount);
    static bool receiveProtocol3(unsigned int changeCount);
    static bool receiveProtocol4(unsigned int changeCount);
    int nReceiverInterrupt;
#endif
    int nTransmitterPin;
    int nPulseLength;
    int nRepeatTransmit;
    char nProtocol;

#ifndef RCSwitchDisableReceiving
    static int nReceiveTolerance;
    static unsigned long nReceivedValue;
    static unsigned long nReceivedAddr;
    static unsigned int nReceivedBitlength;
    static unsigned int nReceivedDelay;
    static byte nReceivedProtocol;
#endif
    /*
     * timings[0] contains sync timing, followed by a number of bits
     */
    static unsigned int timings[RCSWITCH_MAX_CHANGES];


};

#endif //ARDUIDOM_NET_ARDUIDOM_RADIO_H

