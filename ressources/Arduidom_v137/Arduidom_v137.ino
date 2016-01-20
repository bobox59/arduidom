//#include <SPI.h>
//#include <Ethernet.h>


// Arduino Sketch par Benoit Masquelier   masquelierb@gmail.com
// Merci également aux contributions de Alois et Chevalir du forum Jeedom.
// Utilisable pour le plugin Arduidom sur Jeedom
// 
// ATTENTION : Il est IMPERATIF d'utiliser l'IDE 1.6.1 ou + pour que tout soit compatible !!!
//
//
/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
///////////                 Veuillez prendre le temps de lire et configurer les variables ci-dessous. Merci.
/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
//
//
//--------------------------------------------------------------------------------------------------------------------------------------------------
//#define FOR_BOBOX59_ONLY  // LIGNE A RETIRER POUR DESACTIVER DES FONCTIONS QUI ME SONT PERSONELLES $$
//--------------------------------------------------------------------------------------------------------------------------------------------------
//
//
//--------------------------------------------------------------------------------------------------------------------------------------------------
// --------------------------CONFIGURATIONS DES SONDES DHT11/22 ------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------
//                                                                                           
#define CNF_DHT_1_PIN 7 //pin pour la Premiere Sonde DHT (0 si aucune)                         
#define CNF_DHT_2_PIN 0 //pin pour la Sonde DHT 2 (0 si aucune)                         
#define CNF_DHT_3_PIN 0 //pin pour la Sonde DHT 3 (0 si aucune)                         
#define CNF_DHT_4_PIN 0 //pin pour la Sonde DHT 4 (0 si aucune)                         
#define CNF_DHT_5_PIN 0 //pin pour la Sonde DHT 5 (0 si aucune)                         
#define CNF_DHT_6_PIN 0 //pin pour la Sonde DHT 6 (0 si aucune)                         
#define CNF_DHT_7_PIN 0 //pin pour la Sonde DHT 7 (0 si aucune)                         
#define CNF_DHT_8_PIN 0 //pin pour la Sonde DHT 8 (0 si aucune)                                                                                                                   
//--------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------
//
//
//--------------------------------------------------------------------------------------------------------------------------------------------------
// ---------------------- CONFIGURATIONS DES DIFFERENTES VARIABLES DISPONIBLE ----------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------
// EMETEUR RADIO
#define RADIO_REPEATS 10 // Nombre de repetitions des messages Radio (1 a 20, augmenter en cas de soucis de transmission vers prises)
//
// DELTA
#define CNF_APINS_DELTA 10 // Valeur du delta pour envoi changement vers jeedom (si la valeur change d'au moins xxx en une fois, envoi a jeedom
#define CNF_CPINS_DELTA 0.1 // Valeur du delta pour envoi changement vers jeedom (si la valeur change d'au moins xxx en une fois, envoi a jeedom
//                                                                                                                                                                                  
// DELAY : ATTENTION, une valeur trop petite peut encombrer le port serie avec les parasites !!!                                                                                                                                          
#define CNF_DELAY_D_SENDS 200 // Delai entre chaque mise a jour d'entrees vers jeedom                                                              
#define CNF_DELAY_A_SENDS 1000 // Delai entre chaque mise a jour d'entrees vers jeedom                                                                                                                                                                                                            
//--------------------------------------------------------------------------------------------------------------------------------------------------
//
//
//--------------------------------------------------------------------------------------------------------------------------------------------------
// ---------------------- CONFIGURATIONS DES PIN ET SKETCH CUSTOM ----------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------
// PARTIE DEFINITION
// Vos #define et autre ici

/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
///////////                 
///////////                 La Librairie AHRCSwitch n'est plus necessaire non plus car integree directement dans le sketch
///////////        
///////////                 
///////////                 ATTENTION : Il est IMPERATIF d'utiliser l'IDE 1.57 ou + pour que tout soit compatible !!!
///////////                 
///////////                 
/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
//
//
//
//
#if defined(FOR_BOBOX59_ONLY) // Uniquement pour BOBOX 59
 #define CNF_DHT_1_PIN 8   
 #define CNF_DHT_2_PIN 9
 #define CNF_DHT_3_PIN 10
#endif

bool CPConfDone = true; // Fix (chevalir)


#include <EEPROM.h>
#include "Arduidom_Radio.h"
RCSwitch mySwitch = RCSwitch();

#if (CNF_DHT_1_PIN + CNF_DHT_2_PIN + CNF_DHT_3_PIN + CNF_DHT_4_PIN + CNF_DHT_5_PIN + CNF_DHT_6_PIN + CNF_DHT_7_PIN + CNF_DHT_8_PIN > 0) 
//  #include <DHT.h>
  #include "DHT.h"

#endif
#define ArduiDomVersion 'g' // Sers a verfifier le contenu de l'EEPROM
#if defined(__AVR_ATmega168__) || defined(__AVR_ATmega168P__)
#define CNF_NB_DPIN 14
#define CNF_NB_APIN 6
#define CNF_NB_CPIN 1 // Nombre de Customs (IMPOSSIBLE SUR LES 168 FAUTE DE RAM DISPONIBLE)
#elif defined(__AVR_ATmega328P__)
#define CNF_NB_DPIN 14
#define CNF_NB_APIN 6
#define CNF_NB_CPIN 16 // Extensible à 128 Maximum
#elif defined(__AVR_ATmega32U4__)
#define CNF_NB_DPIN 14
#define CNF_NB_APIN 6
#define CNF_NB_CPIN 16 // Extensible à 128 Maximum
#elif defined(__AVR_ATmega1280__) || defined(__AVR_ATmega2560__)
#define CNF_NB_DPIN 54
#define CNF_NB_APIN 16
#define CNF_NB_CPIN 16 // Extensible à 128 Maximum
#elif defined(__AVR_ATmega644__) || defined(__AVR_ATmega644P__) || defined(__AVR_ATmega1284P__)
#define CNF_NB_DPIN 14
#define CNF_NB_APIN 6
#define CNF_NB_CPIN 16 // Extensible à 128 Maximum
#endif
// Variables utilis√©es pour RCSwitch
unsigned long RFData = 0; // Valeur du Data Recu par 433
unsigned long RFAddr = 0; // Valeur de l'addresse de l'emmeteur 433
unsigned int RFProtocol = 0; // Valeur du Protocole du message 433
unsigned int RFDelay = 0; // Valeur du Protocole du message 433
unsigned int RFLenght = 0; // Valeur du Protocole du message 433
unsigned long oldRFData = 0; // Valeur du Data Recu par 433
unsigned long oldRFAddr = 0; // Valeur de l'addresse de l'emmeteur 433
unsigned int oldRFProtocol = 0; // Valeur du Protocole du message 433
unsigned long ChaconSender;
int ChaconRecevr = 0;
// Variables pour SERIE
byte SerailDataOK = 0;
char DataSerie[129]; // a string to hold incoming data
char l[3]; // a string to hold incoming data
byte LenSerial = 0;
// Variables pour le comparateur
byte OldValue[CNF_NB_DPIN]; // anciennes valeurs pour detection changements
unsigned long OldAValue[CNF_NB_APIN]; // anciennes valeurs pour detection changements
float OldCValue[CNF_NB_CPIN]; // anciennes valeurs pour customs
float CustomValue[CNF_NB_CPIN]; // valeurs en cours pour customs
unsigned long LastSend[CNF_NB_DPIN + CNF_NB_APIN + CNF_NB_CPIN]; // anciennes valeurs pour detection changements
byte NewValue = 0; // tampon nouvelle valeurs pour detection changements
unsigned long NewAValue = 0; // tampon nouvelle valeurs pour detection changementsbyte LenSerial;
float NewCValue = 0;
unsigned long ACompare = 0;
float CCompare = 0;
unsigned long LastRadioMessage = 0; // Millis du dernier message radio
// Variables systeme
unsigned long timerMAJ = 0;
char pinmode[CNF_NB_DPIN + CNF_NB_APIN + CNF_NB_CPIN]; // Pin Modes
byte bytetosend[6] = {0,0,0,0,0,0};
byte RadioRXpin = 0;
byte RadioTXPin = 0;
byte pinToSet = 0;
byte RAZRadio = 0;
byte ReadyToSend = 0; // Arduino init complet
byte negval = 0; //Valeur negative pour Customs
long tempsDHT = 0; //tempo d'actualisation
long tempsLOOP = 0; //tempo d'actualisation
float DHTValue[17] = {0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0}; // Valeurs Sondes DHT
#if (CNF_DHT_1_PIN > 0)
DHT dht1;
#endif
#if (CNF_DHT_2_PIN > 0)
DHT dht2;
#endif
#if (CNF_DHT_3_PIN > 0)
DHT dht3;
#endif
#if (CNF_DHT_4_PIN > 0)
DHT dht4;
#endif
#if (CNF_DHT_5_PIN > 0)
DHT dht5;
#endif
#if (CNF_DHT_6_PIN > 0)
DHT dht6;
#endif
#if (CNF_DHT_7_PIN > 0)
DHT dht7;
#endif
#if (CNF_DHT_8_PIN > 0)
DHT dht8;
#endif

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
  #if (CNF_DHT_1_PIN > 0)
    dht1.setup(CNF_DHT_1_PIN);
  #endif
  #if (CNF_DHT_2_PIN > 0)
    dht2.setup(CNF_DHT_2_PIN);
  #endif
  #if (CNF_DHT_3_PIN > 0)
    dht3.setup(CNF_DHT_3_PIN);
  #endif
  #if (CNF_DHT_4_PIN > 0)
    dht4.setup(CNF_DHT_4_PIN);
  #endif
  #if (CNF_DHT_5_PIN > 0)
    dht5.setup(CNF_DHT_5_PIN);
  #endif
  #if (CNF_DHT_6_PIN > 0)
    dht6.setup(CNF_DHT_6_PIN);
  #endif
  #if (CNF_DHT_7_PIN > 0)
    dht7.setup(CNF_DHT_7_PIN);
  #endif
  #if (CNF_DHT_8_PIN > 0)
    dht8.setup(CNF_DHT_8_PIN);
  #endif
  Serial.begin(115200); // Init du Port serie/USB
  Serial.setTimeout(5); // Timeout 5ms
  if (EEPROM.read(1) != ArduiDomVersion) {
    InitEEPROM();
  }
  ReloadEEPROM(0);
 //--------------------------------------------------------------------------------------------------------------------------------------------------
 // PARTIE SETUP

 // Votre partie "setup" perso ici (ne s'executera qu'une fois au demarrage de l'arduino)
    
 CustomValue[0] = 3;   
    
    
    
    
    
    
    
 // Fin de votre partie "setup"
 while (1) {
    if (Serial.find("HI") != 1) break;
  }
  Serial.println("HELLO");
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

  // PROCEDURE DE DETECTION DES CHANGEMENTS: ////////////////////////////////////

  if (ReadyToSend == 1) { // Attend que le 1er SP Soit OK
    for (int i = 0; i < CNF_NB_DPIN; i++) { // ****************************** Detection des changements de valeurs sur pins DIGITALES
      if (pinmode[i] == 'i' || pinmode[i] == 'o' || pinmode[i] == 'h') { 
        NewValue = digitalRead(i);
        if (OldValue[i] != NewValue) {
          if (millis() - LastSend[i] > CNF_DELAY_D_SENDS) { // pas d'envoi de valeur si moins de xxx ms avant la precedente
            LastSend[i] = millis();
            OldValue[i] = NewValue;
            Serial.print(i);
            Serial.print(">>");
            Serial.print(OldValue[i]);
            Serial.println("<<");
          }
        }
      }
    }
    for (int i = 0; i < CNF_NB_APIN; i++) { // ***************************** Detection des changements de valeurs sur pins ANALOGIQUES
      if (pinmode[CNF_NB_DPIN + i] == 'a') {
        NewAValue = analogRead(i);
        int aChange = 0;
        if (NewAValue > OldAValue[i]) {
          ACompare = NewAValue - OldAValue[i];
          if (ACompare > CNF_APINS_DELTA) {
            aChange = 1;
          }
        }
        if (OldAValue[i] > NewAValue) {
          ACompare = OldAValue[i] - NewAValue;
          if (ACompare > CNF_APINS_DELTA ) {
            aChange = 1;
          }
        }
  
        if (aChange == 1) {
          if (NewAValue != OldAValue[i]) {
            if (millis() - LastSend[i] > CNF_DELAY_A_SENDS) { // pas d'envoi de valeur si moins de xxx ms avant la precedente
              LastSend[i] = millis();
              Serial.print(CNF_NB_DPIN + i);
              Serial.print(">>");
              Serial.print(NewAValue);
              Serial.println("<<");
              OldAValue[i] = NewAValue;
            }
          }
        }
      }
    }
    for (int i = 0; i < CNF_NB_CPIN; i++) { // ***************************** Detection des changements de valeurs sur pins CUSTOMISEES
      if (pinmode[CNF_NB_DPIN + CNF_NB_APIN + i] == 'c') { 
        NewCValue = CustomValue[i];
        int cChange = 0;
        if (NewCValue > OldCValue[i]) {
          CCompare = NewCValue - OldCValue[i];
          if (CCompare > CNF_CPINS_DELTA) {
            cChange = 1;
          }
        }
        if (OldCValue[i] > NewCValue) {
          CCompare = OldCValue[i] - NewCValue;
          if (CCompare > CNF_CPINS_DELTA ) {
            cChange = 1;
          }
        }
        if (cChange == 1) {
          if (NewCValue != OldCValue[i]) {
            if (millis() - LastSend[i] > CNF_DELAY_A_SENDS) { // pas d'envoi de valeur si moins de xxx ms avant la precedente
              LastSend[i] = millis();
              Serial.print(CNF_NB_DPIN + CNF_NB_APIN + i);
              Serial.print(">>");
              Serial.print(NewCValue);
              Serial.println("<<");
              OldCValue[i] = NewCValue;
            }
          }
        }
      }
    }
  }


  //   __   ___  __                  __   ___  __   ___  __  ___    __
  //  /__` |__  |__) |  /\  |       |__) |__  /  ` |__  |__)  |  | /  \ |\ |
  //  .__/ |___ |  \ | /~~\ |___    |  \ |___ \__, |___ |     |  | \__/ | \|

  //
  if (SerailDataOK == 1) { // Donnees dans le buffer Serie
    Serial.print("DBG_Data to do:"); Serial.println(DataSerie);
    if (DataSerie[0] == 'S' && DataSerie[1] == 'P') { // ************************************************************ SP = Set Pin
      Serial.write("SP_");
      pinToSet = 10 * int(DataSerie[2] - '0'); // dizaines
      pinToSet += int(DataSerie[3] - '0'); // unites
      Serial.print(pinToSet);
      if (pinmode[pinToSet] == 'o' || pinmode[pinToSet] == 'i') { // also on mode i for pull up of inputs
        Serial.print("=");
        if (DataSerie[4] == '0') { digitalWrite(pinToSet, LOW); Serial.println("L"); }
        if (DataSerie[4] == '1') { digitalWrite(pinToSet, HIGH); Serial.println("H"); }
      }
      if (pinmode[pinToSet] == 'p') { // for PWM
        Serial.print("=");
        int pinvalue = 100 * int(DataSerie[4] - '0') + 10 * int(DataSerie[5] - '0') + int(DataSerie[6] - '0');
        analogWrite(pinToSet, pinvalue);
        Serial.println(pinvalue);
      }
      if (pinmode[pinToSet] == 'd') { // for customs
        Serial.print("=");
        for (int i = 0; i < 20; i++) { // Decaler de 3 vers la gauche (suppr. SRT)
          DataSerie[i] = DataSerie[4 + i];
          if (DataSerie[i] == '-') {
            DataSerie[i] = '0';
            negval = 1;
          }
        }
        if (negval == 1) {
          CustomValue[pinToSet - CNF_NB_DPIN - CNF_NB_APIN] = 0 - atof(DataSerie);
          negval = 0 ;
        } else {
          CustomValue[pinToSet - CNF_NB_DPIN - CNF_NB_APIN] = atof(DataSerie);
        }
        Serial.println(CustomValue[pinToSet - CNF_NB_DPIN - CNF_NB_APIN]);
      }
      if (pinmode[pinToSet] == 't') { // pin Radio TX
        if (DataSerie[4] == 'T') { //// Radio Mode TriState
          for (int i = 0; i < 90; i++) { // Decaler de 3 vers la gauche (suppr. SRT)
            DataSerie[i] = DataSerie[5 + i];
          }
          mySwitch.setProtocol(1);
          mySwitch.sendTriState(DataSerie);
        }
        if (DataSerie[4] == 'E') { //// Radio Mode TriState EuroDomEst
          for (int i = 0; i < 90; i++) { // Decaler de 3 vers la gauche (suppr. SRT)
            DataSerie[i] = DataSerie[5 + i];
          }
          mySwitch.setProtocol(1);
          mySwitch.setPulseLength(900);
          mySwitch.sendTriState(DataSerie);
        }
        if (DataSerie[4] == 'H') { //// Radio Mode Chacon DIO ex:H 05580042 0100
          // Modifs par Chevalir
          DataSerie[13] = 0; // group char is not used so set 0 to limit the strtol function  
          bool onOff = DataSerie[14] == '1';
          ChaconSender = strtol( &DataSerie[5], NULL, 0 );
          int ChaconRecevr = 10 * int(DataSerie[15] - '0') + int(DataSerie[16] - '0');
          for (int i = 1; i <= RADIO_REPEATS; i++) {
            mySwitch.send(ChaconSender, ChaconRecevr, onOff);
          }
        }
      }
      Serial.print("SP_OK\n");
    } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF SP

    if (DataSerie[0] == 'C' && DataSerie[1] == 'P') { // *************************************************** CP = Configure Pin mode
      //Serial.print("DBG_Serie:");
      //Serial.println(DataSerie);
      //Serial.print("DBG_CP Len=");
      //Serial.println(LenSerial);
      if (LenSerial >= (1 + CNF_NB_DPIN + CNF_NB_APIN + CNF_NB_CPIN)) {
        mySwitch.disableReceive(); // desactive la radio

        for (int i = 2; i < CNF_NB_DPIN; i++) { EEPROM.write(i, DataSerie[2 + i]); } // CPzzrzzzzzzzztoo......
        for (int i = 0; i < CNF_NB_APIN; i++) { EEPROM.write(CNF_NB_DPIN + i, DataSerie[2 + CNF_NB_DPIN + i]); } // CP..............aaaaaa
        for (int i = 0; i < CNF_NB_CPIN; i++) { EEPROM.write(CNF_NB_DPIN + CNF_NB_APIN + i, DataSerie[2 + CNF_NB_DPIN + CNF_NB_APIN + i]); } // CP..............aaaaaa
        ReadyToSend = 1;
        ReloadEEPROM(1);
        Serial.println("CP_OK");
        delay(500); // laisse le temps a jeedom de valider
      } else  {
        Serial.print("BAD_L");
      }
    } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF CP


    if (DataSerie[0] == 'R' && DataSerie[1] == 'F') { // **************************************************************** RF = ReFresh datas
      Serial.print("DATA:");
      for (int i = 0; i < CNF_NB_DPIN; i++) {
        //Serial.print(i);
        //Serial.print("=");
        if (pinmode[i] == 'i' || pinmode[i] == 'o') { // verifie que la pin est bien en entree
          Serial.print(OldValue[i]);
        } else {
          Serial.print("0");
        }
        Serial.print(",");
      }
      for (int i = 0; i < CNF_NB_APIN; i++) {
        if (pinmode[CNF_NB_DPIN + i] == 'a') { // Verifie que la pin est bien en Analog
          Serial.print(OldAValue[i]);
        } else {
          Serial.print("0");
        }
      Serial.print(",");
      }
      for (int i = 0; i < CNF_NB_CPIN; i++) {
        Serial.print(OldCValue[i]);
        if (i < CNF_NB_CPIN - 1) { Serial.print(","); } // enleve la derniere virgule
      }
      Serial.print("\n");
    }

    if (DataSerie[0] == 'P' && DataSerie[1] == 'I' && DataSerie[2] == 'N' && DataSerie[3] == 'G') { // ***************************** PING
      Serial.println("PING_OK");
      // @@RC
      if ( CPConfDone ) {
        Serial.println("CP_OK");
      }
      // end @@RC
    }

    LenSerial = 0;
    SerailDataOK = 0;
  } ///////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF IF LenSerial



  //   __        __     __      __   ___  __   ___  __  ___    __
  //  |__)  /\  |  \ | /  \    |__) |__  /  ` |__  |__)  |  | /  \ |\ |
  //  |  \ /~~\ |__/ | \__/    |  \ |___ \__, |___ |     |  | \__/ | \|
  //
  
    #if defined(FOR_BOBOX59_ONLY)
    if (mySwitch.available() || digitalRead(7) == 1) { 
    #else
    if (mySwitch.available()) { 
    #endif
    LastRadioMessage = millis();
    RFData = mySwitch.getReceivedValue();
    RFAddr = mySwitch.getReceivedAddr();
    RFProtocol = mySwitch.getReceivedProtocol();
    RFLenght = mySwitch.getReceivedBitlength();
    RFDelay = mySwitch.getReceivedDelay();
    unsigned int* raw = mySwitch.getReceivedRawdata();
    
    #if defined(FOR_BOBOX59_ONLY) //////// NON UTILISE POUR AUTRES QUE BOBOX59
    if (digitalRead(7) == 1) { 
      if (analogRead(0) < 500) RFAddr = 0;
      if (analogRead(0) >= 500) RFAddr = 1;
      if (analogRead(1) >= 500) RFAddr = RFAddr + 2;
      if (analogRead(2) >= 500) RFAddr = RFAddr + 4;
      if (analogRead(3) >= 500) RFAddr = RFAddr + 8;
      RFData = 1;
      RFProtocol = 9;
    }
    #endif

    if (oldRFData != RFData || oldRFAddr != RFAddr || oldRFProtocol != RFProtocol) {
      RAZRadio = 0;
      oldRFData = RFData;
      oldRFAddr = RFAddr;
      oldRFProtocol = RFProtocol;
      Serial.print(RadioRXpin);
      Serial.print(">>RFD:");
      Serial.print(RFData);
      Serial.print(":A:");
      Serial.print(RFAddr);
      Serial.print(":P:");
      Serial.print(RFProtocol);
      Serial.println("<<");
      ///*
      //#if defined(FOR_BOBOX59_ONLY) //////// NON UTILISE POUR AUTRES QUE BOBOX59
        Serial.print("Raw data: ");
        for (int i=0; i<= RFLenght*2; i++) {
          Serial.print(raw[i]);
          Serial.print(",");
        }
        Serial.println();
      //#endif
      //*/
    }
    mySwitch.resetAvailable();
  }// End of mySwitch Reception

  if (millis() - LastRadioMessage > 2000 && RAZRadio != 1) { // envoie une mise a 0 du radio apres 2000 millisecondes
    RAZRadio = 1;
    oldRFData = 0;
    oldRFAddr = 0;
    oldRFProtocol = 99;
    Serial.print(RadioRXpin);
    Serial.println(">>0<<");
  }

  #if (CNF_DHT_1_PIN + CNF_DHT_2_PIN + CNF_DHT_3_PIN + CNF_DHT_4_PIN + CNF_DHT_5_PIN + CNF_DHT_6_PIN + CNF_DHT_7_PIN + CNF_DHT_8_PIN > 0) // ************************************************************************************** GESTION DES SONDES DHT
  if((millis() - tempsDHT) > 45000){ //Si rien non actualisé depuis 45 Secondes
    #if (CNF_DHT_1_PIN > 1)
      DHTValue[1] = (dht1.getHumidity());
      DHTValue[2] = (dht1.getTemperature());
    #endif
    #if (CNF_DHT_2_PIN > 1)
      DHTValue[3] = (dht2.getHumidity());
      DHTValue[4] = (dht2.getTemperature());
    #endif
    #if (CNF_DHT_3_PIN > 1)
      DHTValue[5] = (dht3.getHumidity());
      DHTValue[6] = (dht3.getTemperature());
    #endif
    #if (CNF_DHT_4_PIN > 1)
      DHTValue[7] = (dht4.getHumidity());
      DHTValue[8] = (dht4.getTemperature());
    #endif
    #if (CNF_DHT_5_PIN > 1)
      DHTValue[9] = (dht5.getHumidity());
      DHTValue[10] = (dht5.getTemperature());
    #endif
    #if (CNF_DHT_6_PIN > 1)
      DHTValue[11] = (dht6.getHumidity());
      DHTValue[12] = (dht6.getTemperature());
    #endif
    #if (CNF_DHT_7_PIN > 1)
      DHTValue[13] = (dht7.getHumidity());
      DHTValue[14] = (dht7.getTemperature());
    #endif
    #if (CNF_DHT_8_PIN > 1)
      DHTValue[15] = (dht8.getHumidity());
      DHTValue[16] = (dht8.getTemperature());
    #endif
    Serial.print("DHT:");
    for (int i = 1; i < 17; i++) {
      Serial.print(DHTValue[i]);
      if (i < 16) Serial.print(";");  
    }
    Serial.print('\n');
    tempsDHT = millis(); //on stocke la nouvelle heure
  }
  #endif


  //////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////
  /////////   PLACER CI DESSOUS VOS COMMANDES PERSO POUR LES CUSTOMS  //////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////
  //
  // PARTIE LOOP : CustomValue[0 - 15] sont compatibles en negatifs ansi qu'en virgules ex: -12.4 ------ exemple : CustomValue[0] = CustomValue[1] + 1
  //
  // exemple : CustomValue[0] = CustomValue[1] + 1

  //CustomValue[0] += 0.01;

  if((millis() - tempsLOOP) > 1000){ //Si rien non actualisé depuis 45 Secondes
  
  CustomValue[0] += 0.1;

}
  









  

  //////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////// FIN DES CUSTOMS  ///////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////

  /*
  if((millis() - timerMAJ) > 30000){ // *********************************************************************** AUTO REFRESH VERS JEEDOM
    Serial.print("DATA:");
    for (int i = 0; i < CNF_NB_DPIN; i++) {
    if (pinmode[i] == 'i' || pinmode[i] == 'o') { // verifie que la pin est bien en entree
      Serial.print(OldValue[i]);
    } else {
      Serial.print("0");
    }
    Serial.print(",");
    }
    for (int i = 0; i < CNF_NB_APIN; i++) {
    if (pinmode[CNF_NB_DPIN + i] == 'a') { // Verifie que la pin est bien en Analog
      Serial.print(OldAValue[i]);
    } else {
      Serial.print("0");
    }
    Serial.print(",");
    }
    for (int i = 0; i < CNF_NB_CPIN; i++) {
      Serial.print(CustomValue[i]);
    if (i < CNF_NB_CPIN - 1) { // enleve la derniere virgule
      Serial.print(",");
    }
    }
    Serial.print("\n");
    timerMAJ = millis();
  } // END OF AUTO REFRESH
 */
 
} // EOF void loop()







//
//    ______ ____  _   _  _____ _______ _____ ____  _   _  _____
//   |  ____/ __ \| \ | |/ ____|__   __|_   _/ __ \| \ | |/ ____|
//   | |__ | |  | |  \| | |       | |    | || |  | |  \| | (___
//   |  __|| |  | | . ` | |       | |    | || |  | | . ` |\___ \ 
//   | |   | |__| | |\  | |____   | |   _| || |__| | |\  |____) |
//   |_|    \____/|_| \_|\_____|  |_|  |_____\____/|_| \_|_____/
//



//                  _       _ ______               _
//                 (_)     | |  ____|             | |
//    ___  ___ _ __ _  __ _| | |____   _____ _ __ | |_
//   / __|/ _ \ '__| |/ _` | |  __\ \ / / _ \ '_ \| __|
//   \__ \  __/ |  | | (_| | | |___\ V /  __/ | | | |_
//   |___/\___|_|  |_|\__,_|_|______\_/ \___|_| |_|\__|
//
//
void serialEvent() {
  while (Serial.available() > 0) { // Lecture du port serie
    int x;
    x = Serial.readBytes(l, 1);
    DataSerie[LenSerial] = l[0];
    if (DataSerie[LenSerial] == '\n') {
      DataSerie[LenSerial] = char(0);
      SerailDataOK = 1;
      break;
    }
    LenSerial += 1;
  }
} // EOF serialEvent()




void ReloadEEPROM(int PrintOut) {
  mySwitch.disableReceive();
  RadioRXpin = 0;
  for (int i = 2; i < CNF_NB_DPIN + CNF_NB_APIN + CNF_NB_CPIN; i++) {
    pinmode[i] = EEPROM.read(i); // Pin Modes
    if (PrintOut) Serial.print("Pin ");
    if (PrintOut) Serial.print(i);
    if (PrintOut) Serial.print(" is ");
    if (pinmode[i] == 'z') {
      if (PrintOut) Serial.print("DIS");
    }
    if (pinmode[i] == 'i') {
      pinMode(i, INPUT);
      if (PrintOut) Serial.print("IN");
    }
    if (pinmode[i] == 'a') {
      pinMode(i, INPUT);
      if (PrintOut) Serial.print("ANA");
    }
    if (pinmode[i] == 'o') {
      pinMode(i, OUTPUT);
      if (PrintOut) Serial.print("OUT");
    }
    if (pinmode[i] == 'c') {
      if (PrintOut) Serial.print("CIN");
    }
    if (pinmode[i] == 'd') {
      if (PrintOut) Serial.print("COU");
    }
    if (pinmode[i] == 'p') {
      pinMode(i, OUTPUT);
      if (PrintOut) Serial.print("PWM");
    }
    if (pinmode[i] == 'r') {
#if defined(__AVR_ATmega168__) ||defined(__AVR_ATmega168P__) ||defined(__AVR_ATmega328P__) ||defined(__AVR_ATmega32U4__)
      if (i == 2) mySwitch.enableReceive(0);
      if (i == 3) mySwitch.enableReceive(1);
#elif defined(__AVR_ATmega1280__) || defined(__AVR_ATmega2560__)
      if (i == 2) mySwitch.enableReceive(0);
      if (i == 3) mySwitch.enableReceive(1);
      if (i == 21) mySwitch.enableReceive(2);
      if (i == 20) mySwitch.enableReceive(3);
      if (i == 19) mySwitch.enableReceive(4);
      if (i == 18) mySwitch.enableReceive(5);
#endif
      RadioRXpin = i;
      if (PrintOut) Serial.print("RRX");
    }
    if (pinmode[i] == 't') {
      pinMode(i, OUTPUT);
      RadioTXPin = i;
      mySwitch.enableTransmit(i); // Transmission sur Pin
      mySwitch.setRepeatTransmit(RADIO_REPEATS); // Repete x fois le message
      if (PrintOut) Serial.print("RTX");
    }
    if (PrintOut) Serial.print("\n");
  }

}// END OF ReloadEEPROM()

void InitEEPROM() {
  EEPROM.write(1, ArduiDomVersion); // Pin Mode
  for (int i = 2; i < 200; i++) {
    EEPROM.write(i, 'z'); // Pin 0 Mode // RESERVED FOR USB
  }
   // @@RC fix bug in plugins (chevalir)
   CPConfDone=false;
   // end @@RC
} // END OF InitEEPROM()

