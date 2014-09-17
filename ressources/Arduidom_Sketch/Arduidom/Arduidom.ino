// Arduino Sketch par Benoit Masquelier   masquelierb@gmail.com
// Utilisable pour le plugin Arduidom sur Jeedom
//
#include <EEPROM.h>
#include <RCSwitch.h>
RCSwitch mySwitch = RCSwitch();

// Definitions et VERSIONS
#define pinradioD1 7 // Pin du Radio D1 SC2662
#define ArduiDomVersion 'd' // Sers a verfifier le contenu de l'EEPROM

// Variables utilis√©es pour RCSwitch
unsigned long RFData = 0; // Valeur du Data Recu par 433
unsigned long RFAddr = 0; // Valeur de l'addresse de l'emmeteur 433
unsigned int RFProtocol = 0; // Valeur du Protocole du message 433
// Variables pour SERIE
char DataSerie[60]; // a string to hold incoming data
byte LenSerial;
char Decimale[16]; // Pour fonction ITOA
char TempLong[11]; // pour fonction LTOA
char pinmode[21]; // Pin Modes
char* tris; // For TriState Sending
byte radioevent;
byte detect;
byte bytetosend[6];
byte actualpin;
byte CheckEEPROM; // Variable pour verifier la version de l eeprom
byte RadioRXpin;
byte pinToSet;
byte lastprint; // Utilis√© pour eviter les repetitions sur port s√©rie

//
//   _____      _                 _____             _   _
//  / ____|    | |               |  __ \           | | (_)
// | (___   ___| |_ _   _ _ __   | |__) |___  _   _| |_ _ _ __   ___
//  \___ \ / _ \ __| | | | '_ \  |  _  // _ \| | | | __| | '_ \ / _ \
//  ____) |  __/ |_| |_| | |_) | | | \ \ (_) | |_| | |_| | | | |  __/
// |_____/ \___|\__|\__,_| .__/  |_|  \_\___/ \__,_|\__|_|_| |_|\___|
//                       | |
//                       |_|
//

void setup() {

  Serial.begin(115200); // Init du Port serie/USB
  Serial.setTimeout(5); // Timeout 5ms
  jeesend("INIT...\n");

  CheckEEPROM = 1; // Verifie la version des donnees sur l EEPROM
  if (EEPROM.read(1) != ArduiDomVersion) CheckEEPROM = 0;
  if (CheckEEPROM != 1) {
    jeesend("Update EEPROM\n");
    InitEEPROM();
  }
  ReloadEEPROM();

  jeesend("HELLO\n");

}
//   __      __   _     _    _
//   \ \    / /  (_)   | |  | |
//    \ \  / /__  _  __| |  | |     ___   ___  _ __
//     \ \/ / _ \| |/ _` |  | |    / _ \ / _ \| '_ \ 
//      \  / (_) | | (_| |  | |___| (_) | (_) | |_) |
//       \/ \___/|_|\__,_|  |______\___/ \___/| .__/
//                                            | |
//                                            |_|
//


void loop() {

  //   __   ___  __                  __   ___  __   ___  __  ___    __
  //  /__` |__  |__) |  /\  |       |__) |__  /  ` |__  |__)  |  | /  \ |\ |
  //  .__/ |___ |  \ | /~~\ |___    |  \ |___ \__, |___ |     |  | \__/ | \| $$$C0

  //
  if (LenSerial > 0) { // Donnees dans le buffer Serie
    if (DataSerie[0] == '$' && DataSerie[1] == '$') { // $$ - Ordre de jeeDom

      if (DataSerie[2] == 'S' && DataSerie[3] == 'P') {               // SP = Set Pin
        jeesend("SP_");
        pinToSet = (int((DataSerie[4]) * 10) + int(DataSerie[5])) - 16;
        //if (pinmode[pinToSet] == 'o') { // verif que la pin est en mode OUT
        ltoa(pinToSet, TempLong, 10);
        jeesend(TempLong);
        if (pinmode[pinToSet] == 'o' || pinmode[pinToSet] == 'i') { // also on mode i for pull up of inputs
          jeesend("=");
          if (DataSerie[6] == '0') {
            digitalWrite(pinToSet, LOW);
            jeesend("0");
          }
          if (DataSerie[6] == '1') {
            digitalWrite(pinToSet, HIGH);
            jeesend("1");
          }
        }
        if (pinmode[pinToSet] == 'p') {
          jeesend("=");
          ltoa((DataSerie[6] * 100 + DataSerie[7] * 10 + DataSerie[8]),TempLong, 10);
          analogWrite(pinToSet, DataSerie[6] * 100 + DataSerie[7] * 10 + DataSerie[8]);
          jeesend(TempLong);
        }
        jeesend("\n");
      }

      if (DataSerie[2] == 'C' && DataSerie[3] == 'P') {              // CP = Configure Pin mode
        if (LenSerial < (4 + 20)) {
          jeesend("CP ERROR\n");
          ReloadEEPROM();
        } else {
          mySwitch.disableReceive(); // Reception sur INT1 (Pin 3)

          actualpin = 0;
          for (int i = 0; i < 14; i++) {
            EEPROM.write(10 + i, DataSerie[4 + i]);  // $$CPzzrzzzzzzzztoo......
          }
          for (int i = 0; i < 6; i++) {
            EEPROM.write(50 + i, DataSerie[18 + i]); // $$CP..............aaaaaa
          }
          if (RadioRXpin != 0) {
            mySwitch.enableReceive(RadioRXpin - 2);
          }
          jeesend("CP OK\n");
          ReloadEEPROM();
        }
      }


      if (DataSerie[2] == 'S' && DataSerie[3] == 'R') { /////////////// SR = Send Radio code
        if (DataSerie[4] == '1') { //// Radio Mode 1
          envoi(1, 6095360, 1); // KP HOME Example
          jeesend("SR1_OK\n");
        }
        if (DataSerie[4] == '2') { //// Radio Mode 2
          mySwitch.sendTriState(("001100001000"));
          jeesend("SR2_OK\n");
        }
        if (DataSerie[4] == '3') { //// Radio Mode 2
          mySwitch.sendTriState(("001100000000"));
          jeesend("SR3_OK\n");
        }
        if (DataSerie[4] == 'T') { //// Radio Mode TriState
          itoa(DataSerie[5], TempLong, 10);
          tris += DataSerie[5];
          tris += DataSerie[6];
          tris += DataSerie[7];
          tris += DataSerie[8];
          tris += DataSerie[9];
          tris += DataSerie[10];
          tris += DataSerie[11];
          tris += DataSerie[12];
          tris += DataSerie[13];
          tris += DataSerie[14];
          tris += DataSerie[15];
          tris += DataSerie[16];
          mySwitch.sendTriState(tris);
          jeesend("SRT");
          jeesend(tris);
          jeesend("_OK\n");
        }
      }

      if (DataSerie[2] == 'R' && DataSerie[3] == 'F') { /////////////// RF = ReFresh datas

        for (int i = 0; i < 14; i++) {
          ltoa(i, TempLong, 10);
            //jeesend(TempLong);
            //jeesend("=");
            ltoa(digitalRead(i), TempLong, 10);
            jeesend(TempLong);
            jeesend(",");
        }
        jeesend("x,");
        for (int i = 0; i < 6; i++) {
          ltoa(i, TempLong, 10);
            //jeesend("a");
            //jeesend(TempLong);
            //jeesend("=");
            ltoa(analogRead(i), TempLong, 10);
            jeesend(TempLong);
            jeesend(",");
        }
        jeesend("\n");
      }

      if (DataSerie[2] == 'H' && DataSerie[3] == 'I') { /////////////// HI = HELLO
        jeesend("HELLO\n");
      }

    } // END OF If $$

    // Effacement du buffer Serie
    for (int i = 0; i < 59; i++) {
      DataSerie[i] = char(0);
    }
    Serial.flush();
    LenSerial = 0;
  } // END OF IF LenSerial




  //   __        __     __      __   ___  __   ___  __  ___    __
  //  |__)  /\  |  \ | /  \    |__) |__  /  ` |__  |__)  |  | /  \ |\ |
  //  |  \ /~~\ |__/ | \__/    |  \ |___ \__, |___ |     |  | \__/ | \|
  //
  if (mySwitch.available()) {

    RFData = mySwitch.getReceivedValue();
    RFAddr = mySwitch.getReceivedAddr();
    RFProtocol = mySwitch.getReceivedProtocol();
    //RF = mySwitch.
    jeesend("RFD:");
    ltoa(RFData, TempLong, 10);
    jeesend(TempLong);
    jeesend(":A:");
    ltoa(RFAddr, TempLong, 10);
    jeesend(TempLong);
    jeesend(":P:");
    ltoa(RFProtocol, TempLong, 10);
    jeesend(TempLong);
    jeesend("\n");

    mySwitch.resetAvailable();
  }// End of mySwitch Reception

  //   __        __     __      ___       ___      ___  __
  //  |__)  /\  |  \ | /  \    |__  \  / |__  |\ |  |  /__`
  //  |  \ /~~\ |__/ | \__/    |___  \/  |___ | \|  |  .__/
  //
  bitWrite(radioevent, 0, digitalRead(pinradioD1));

  if (bitRead(radioevent, 1) == 1 && bitRead(radioevent, 0) == 0) bitWrite(radioevent, 1, 0);

  if (bitRead(radioevent, 1) == 0 && bitRead(radioevent, 0) == 1) {
    if (analogRead(0) < 500) detect = 0;
    if (analogRead(0) >= 500) detect = 1;
    if (analogRead(1) >= 500) detect = detect + 2;
    if (analogRead(2) >= 500) detect = detect + 4;
    if (analogRead(3) >= 500) detect = detect + 8;

  }
  // Efface detect apres 2 secondes
  ///////if (Compteur[CNT_CLEAR_DETECT] == 0) detect = 0;

  if (lastprint != detect && detect > 0) {
    jeesend("INPUT:");
    itoa(detect, Decimale, 10);
    jeesend(Decimale);
    lastprint = detect;
    jeesend("\n");
  }


} // EOF void loop()
//
//    ______ ____  _   _  _____ _______ _____ ____  _   _  _____
//   |  ____/ __ \| \ | |/ ____|__   __|_   _/ __ \| \ | |/ ____|
//   | |__ | |  | |  \| | |       | |    | || |  | |  \| | (___
//   |  __|| |  | | . ` | |       | |    | || |  | | . ` |\___ \ 
//   | |   | |__| | |\  | |____   | |   _| || |__| | |\  |____) |
//   |_|    \____/|_| \_|\_____|  |_|  |_____\____/|_| \_|_____/
//



//                         _
//                        (_)
//     ___ _ ____   _____  _
//    / _ \ '_ \ \ / / _ \| |
//   |  __/ | | \ V / (_) | |
//    \___|_| |_|\_/ \___/|_|
//
//
void envoi(byte send433b1, byte send433b2, byte send433b3) {
#define debugviewenvoi 1

  mySwitch.disableReceive(); // Reception sur INT1 (Pin 3)

  // CheckSum pour keypad radio !
  bitWrite(send433b2, 7, bitRead(send433b1, 7) ^ bitRead(send433b1, 3));
  bitWrite(send433b2, 6, 1 - (bitRead(send433b1, 6) ^ bitRead(send433b1, 2)));
  bitWrite(send433b2, 5, bitRead(send433b1, 5) ^ bitRead(send433b1, 1));
  bitWrite(send433b2, 4, 1 - (bitRead(send433b1, 4) ^ bitRead(send433b1, 0)));

  bytetosend[0] = 152;
  bytetosend[1] = 118;
  bytetosend[2] = 175;
  bytetosend[3] = send433b1;
  bytetosend[4] = send433b2;
  bytetosend[5] = send433b3;

  digitalWrite(12, HIGH);
  delay(50);
  digitalWrite(12, LOW);
  delay(50);

  for (int repeats = 1; repeats < 4; repeats++) {

    digitalWrite(12, HIGH);
    delayMicroseconds(500);
    for (byte b2 = 0; b2 <= 5; b2++) {
      for (byte b3 = 0; b3 <= 7; b3++) {

        switch (bitRead(bytetosend[b2], b3)) {
          case 0:
            digitalWrite(12, LOW);
            delayMicroseconds(400);
            digitalWrite(12, HIGH);
            delayMicroseconds(400);
            digitalWrite(12, HIGH);
            delayMicroseconds(390);
            break;
          case 1:
            digitalWrite(12, LOW);
            delayMicroseconds(400);
            digitalWrite(12, LOW);
            delayMicroseconds(400);
            digitalWrite(12, HIGH);
            delayMicroseconds(390);
            break;
        }
      }
    }
    digitalWrite(12, LOW);
    delay(4);
  }

  if (RadioRXpin != 0) {
    mySwitch.enableReceive(RadioRXpin - 2);
  }

} // EOF envoi()




// jeesend
void jeesend(char ordre[40]) {
  for (int m = 0; m < 40; m++) {
    if (ordre[m] == char('\0')) m = 40;
    if (m < 40) {
      Serial.print(char(ordre[m]));
    }
  }
}



//                  _       _ ______               _
//                 (_)     | |  ____|             | |
//    ___  ___ _ __ _  __ _| | |____   _____ _ __ | |_
//   / __|/ _ \ '__| |/ _` | |  __\ \ / / _ \ '_ \| __|
//   \__ \  __/ |  | | (_| | | |___\ V /  __/ | | | |_
//   |___/\___|_|  |_|\__,_|_|______\_/ \___|_| |_|\__|
//
//
void serialEvent() {
  for (int i = 0; i < 59; i++) {
    DataSerie[i] = char(0);
  }
  while (Serial.available() > 0) {
    LenSerial = Serial.readBytes(DataSerie, 59);
  }
} // EOF serialEvent()


void ReloadEEPROM() {
  pinmode[0] = EEPROM.read(10); // Pin Modes
  pinmode[1] = EEPROM.read(11); //
  pinmode[2] = EEPROM.read(12); // z = Disabled
  pinmode[3] = EEPROM.read(13); // i = Input
  pinmode[4] = EEPROM.read(14); // o = Output
  pinmode[5] = EEPROM.read(15); // r = Radio In
  pinmode[6] = EEPROM.read(16); // t = Radio Out
  pinmode[7] = EEPROM.read(17); // a = Analog IN
  pinmode[8] = EEPROM.read(18); // p = PWN Output
  pinmode[9] = EEPROM.read(19); //
  pinmode[10] = EEPROM.read(20); //
  pinmode[11] = EEPROM.read(21); //
  pinmode[12] = EEPROM.read(22); //
  pinmode[13] = EEPROM.read(23); //

  pinmode[15] = EEPROM.read(50); //
  pinmode[16] = EEPROM.read(51); //
  pinmode[17] = EEPROM.read(52); //
  pinmode[18] = EEPROM.read(53); //
  pinmode[19] = EEPROM.read(54); //
  pinmode[20] = EEPROM.read(55); //
 
  mySwitch.disableReceive();
  RadioRXpin = 0;
  for (int i = 2; i < 14; i++) {
    jeesend("Pin ");
    ltoa(i, TempLong, 10);
    jeesend(TempLong);
    jeesend(" is ");
    if (pinmode[i] == 'z') {
      pinMode(i, INPUT);
      jeesend("DISABLED");
    }
    if (pinmode[i] == 'i') {
      pinMode(i, INPUT);
      jeesend("INPUT");
    }
    if (pinmode[i] == 'o') {
      pinMode(i, OUTPUT);
      jeesend("OUTPUT");
    }
    if (pinmode[i] == 'p') {
      pinMode(i, OUTPUT);
      jeesend("PWM-OUTPUT");
    }
    if (pinmode[i] == 'r') {
      pinMode(i, INPUT);
      mySwitch.enableReceive(i - 2); // Reception sur INT i (1 = Pin 3)
      RadioRXpin = i;
      jeesend("Radio RX");
    }
    if (pinmode[i] == 't') {
      pinMode(i, OUTPUT);
      mySwitch.enableTransmit(i); // Transmission sur Pin
      mySwitch.setRepeatTransmit(15); // Repete x fois le message
      jeesend("Radio TX");
    }
    jeesend("\n");
  }
  for (int i = 0; i < 6; i++) {
    jeesend("APin ");
    ltoa(i, TempLong, 10);
    jeesend(TempLong);
    jeesend(" is ");
    if (pinmode[15+i] == 'z') {
      jeesend("DISABLED");
    }
    if (pinmode[15+i] == 'a') {
      jeesend("A-INPUT");
    }
    jeesend("\n");
  }

}

void InitEEPROM() {
  EEPROM.write(1, ArduiDomVersion); // Pin Mode
  EEPROM.write(10, 'i'); // Pin 0 Mode // RESERVED FOR USB
  EEPROM.write(11, 'i'); // Pin 1 Mode // RESERVED FOR USB
  EEPROM.write(12, 'z'); // Pin 2 Mode
  EEPROM.write(13, 'z'); // Pin 3 Mode
  EEPROM.write(14, 'z'); // Pin 4 Mode
  EEPROM.write(15, 'z'); // Pin 5 Mode
  EEPROM.write(16, 'z'); // Pin 6 Mode
  EEPROM.write(17, 'z'); // Pin 7 Mode
  EEPROM.write(18, 'z'); // Pin 8 Mode
  EEPROM.write(19, 'z'); // Pin 9 Mode
  EEPROM.write(20, 'z'); // Pin 10 Mode
  EEPROM.write(21, 'z'); // Pin 11 Mode
  EEPROM.write(22, 'z'); // Pin 12 Mode
  EEPROM.write(23, 'z'); // Pin 13 Mode
  
  EEPROM.write(50, 'z'); // Pin A0 Mode
  EEPROM.write(51, 'z'); // Pin A1 Mode
  EEPROM.write(52, 'z'); // Pin A2 Mode
  EEPROM.write(53, 'z'); // Pin A3 Mode
  EEPROM.write(54, 'z'); // Pin A4 Mode
  EEPROM.write(55, 'z'); // Pin A5 Mode

}



