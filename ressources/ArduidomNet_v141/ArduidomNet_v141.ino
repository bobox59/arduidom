

// Arduino Sketch par Benoit Masquelier   masquelierb@gmail.com
// Utilisable pour le plugin Arduidom sur Jeedom
//
// ATTENTION : Il est IMPERATIF d'utiliser l'IDE 1.57 ou + pour que tout soit compatible !!!
//
//
//
// Les PINs 10,11,12,13 Sont RESERVEES A LA CONNECTION Ethernet pour les UNO !!!
// Les PINs 10,50,51,52 Sont RESERVEES A LA CONNECTION Ethernet pour les MEGA !!!
//
//
/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
///////////
///////////                 Veuillez prendre le temps de lire et configurer les variables ci-dessous. Merci.
///////////
/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
//
//
//--------------------------------------------------------------------------------------------------------------------------------------------------
//#define FOR_BOBOX59_ONLY  // LIGNE A RETIRER POUR DESACTIVER DES FONCTIONS QUI ME SONT PERSONELLES $$
//--------------------------------------------------------------------------------------------------------------------------------------------------
//
//--------------------------------------------------------------------------------------------------------------------------------------------------
// --------------------------CONFIGURATIONS DU RESEAU ARDUINO --------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------
//
// INFO: La cle API de JeeDom est automatiquement envoyee a l'arduino via la Configuration des Pins puis stockee dans l'EEPROM
//
#define CNF_NETWORK 1 //Activation du Shield Ethernet sur Arduino
//
#if (CNF_NETWORK == 1)
#include <SPI.h>
#include <Ethernet.h>
IPAddress CNF_IP_ARDUIN (192, 168, 1, 220); // ADRESSE IP DE L'ARDUINO, A ADAPTER A VOTRE RESEAU
IPAddress CNF_IP_JEEDOM (192, 168, 1, 2); // ADRESSE IP JeeDom
#endif
#define CNF_PORT_JEEDOM 80 // Port d'ecoute Jeedom

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
// Configurations Jeedom
//
#define CNF_AID 3 // Numéro de l'arduino dans jeedom
//
#define CNF_JEEDOM_BOX 0 // Mettre a 1 si c'est une jeedom Box ( pas de /jeedom dans le http pour les Jeedom BOX)
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
#include <EEPROM.h>
#include "Arduidom_Radio.h"
RCSwitch mySwitch = RCSwitch();
#if (CNF_NETWORK == 1)

byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED }; // ADRESSE MAC DE L'ARDUINO, A CHANGER DANS LE CAS DE PLUSIEURS ARDUINO DANS LE MEME RESEAU


EthernetServer server(58174);
#endif


#if (CNF_DHT_1_PIN + CNF_DHT_2_PIN + CNF_DHT_3_PIN + CNF_DHT_4_PIN + CNF_DHT_5_PIN + CNF_DHT_6_PIN + CNF_DHT_7_PIN + CNF_DHT_8_PIN > 0)
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
#define CNF_NB_CPIN 8
#elif defined(__AVR_ATmega32U4__)
#define CNF_NB_DPIN 14
#define CNF_NB_APIN 6
#define CNF_NB_CPIN 16
#elif defined(__AVR_ATmega1280__) || defined(__AVR_ATmega2560__)
#define CNF_NB_DPIN 54
#define CNF_NB_APIN 16
#define CNF_NB_CPIN 16
#elif defined(__AVR_ATmega644__) || defined(__AVR_ATmega644P__) || defined(__AVR_ATmega1284P__)
#define CNF_NB_DPIN 14
#define CNF_NB_APIN 6
#define CNF_NB_CPIN 16
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
byte RefreshData = 0; // Mise a 1 toute les 30 secondes pour envoi des données
unsigned long ChaconSender;
int ChaconRecevr = 0;
// Variables pour SERIE
byte k=1; // Variable utilisée dans les DHT
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
String data;
String CNF_API = "....................";  // Creation de la variable vide, jeedom l'enverra pour la stocker dans l'eeprom, NE PAS LA CHANGER ICI !
//IPAddress CNF_IP_JEEDOM(0,0,0,0); // Creation de la variable VIDE, jeedom l'enverra pour la stocker dans l'eeprom, NE PAS LA CHANGER ICI !
char pinmode[CNF_NB_DPIN + CNF_NB_APIN + CNF_NB_CPIN]; // Pin Modes
byte bytetosend[6] = {0, 0, 0, 0, 0, 0};
byte RadioRXpin = 0;
byte RadioTXPin = 0;
byte pinToSet = 0;
byte RAZRadio = 0;
byte ReadyToSend = 0; // Arduino init complet
byte negval = 0; //Valeur negative pour Customs
long tempsDHT = 0; //tempo d'actualisation
long tempsLOOP = 0; //tempo d'actualisation
float DHTValue[17] = {0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0}; // Valeurs Sondes DHT
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
#if (CNF_NETWORK == 1)
  Ethernet.begin(mac, CNF_IP_ARDUIN);
  server.begin();
#endif

  //--------------------------------------------------------------------------------------------------------------------------------------------------
  // PARTIE SETUP

  // Votre partie "setup" perso ici (ne s'executera qu'une fois au demarrage de l'arduino)

  CustomValue[0] = 3;







  // Fin de votre partie "setup"
#if (CNF_NETWORK != 1)
  while (1) {
    if (Serial.find("HI") != 1) break;
  }
#endif
  Serial.println("HELLO");
} // End Of void setup()


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
  //  .__/ |___ |  \ | /~~\ |___    |  \ |___ \__, |___ |     |  | \__/ | \|

  //
#if (CNF_NETWORK == 1)
  EthernetClient client = server.available();
  if (client) {
    Serial.println(F("Network in:"));
    while (client.connected()) {
      if (client.available()) {
        char c = client.read();
        Serial.print("(");
        DataSerie[LenSerial] = c;
        if (DataSerie[LenSerial] == '\n') {
          DataSerie[LenSerial] = char(0);
          SerailDataOK = 1;
        }
        LenSerial += 1;
        Serial.write(c);
        Serial.print(")");
        if (c == '\n') {
          Serial.println("");
          Serial.println(F("</ndetected>"));
          break;
        }
      }
    }
    if (SerailDataOK == 1) { // Donnees dans le buffer Serie
      Serial.print(F("DBG_Data to do:")); Serial.println(DataSerie);
      if (DataSerie[0] == 'S' && DataSerie[1] == 'P') { // ************************************************************ SP = Set Pin
        Serial.write("SP_");
#if (CNF_NETWORK == 1)
        client.print("SP_");
#endif
        pinToSet = 10 * int(DataSerie[2] - '0'); // dizaines
        pinToSet += int(DataSerie[3] - '0'); // unites
        Serial.print(pinToSet);
#if (CNF_NETWORK == 1)
        client.print(pinToSet);
#endif
        if (pinmode[pinToSet] == 'o' || pinmode[pinToSet] == 'i') { // also on mode i for pull up of inputs
          Serial.print("=");
#if (CNF_NETWORK == 1)
          client.print("=");
#endif
          if (DataSerie[4] == '0') {
            digitalWrite(pinToSet, LOW);
            Serial.println("L");
#if (CNF_NETWORK == 1)
            client.println("L");
#endif
          }
          if (DataSerie[4] == '1') {
            digitalWrite(pinToSet, HIGH);
            Serial.println("H");
#if (CNF_NETWORK == 1)
            client.println("H");
#endif
          }
        }
        if (pinmode[pinToSet] == 'p') {
          Serial.print("=");
#if (CNF_NETWORK == 1)
          client.print("=");
#endif
          int pinvalue = 100 * int(DataSerie[4] - '0') + 10 * int(DataSerie[5] - '0') + int(DataSerie[6] - '0');
          analogWrite(pinToSet, pinvalue);
          Serial.println(pinvalue);
#if (CNF_NETWORK == 1)
          client.println(pinvalue);
#endif
        }
        if (pinmode[pinToSet] == 'd') { // for customs
          Serial.print("=");
#if (CNF_NETWORK == 1)
          client.print("=");
#endif
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
#if (CNF_NETWORK == 1)
          client.println(CustomValue[pinToSet - CNF_NB_DPIN - CNF_NB_APIN]);
#endif
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
            ChaconSender = 10000000 * int(DataSerie[5] - '0');
            ChaconSender += 1000000 * int(DataSerie[6] - '0');
            ChaconSender += 100000 * int(DataSerie[7] - '0');
            ChaconSender += 100000 * int(DataSerie[8] - '0' / 10); // Bien que cette ligne soit "bizarre" c'est NORMAL et VOULU (Merci alois pour l'astuce)
            ChaconSender += 1000 * int(DataSerie[9] - '0');
            ChaconSender += 100 * int(DataSerie[10] - '0');
            ChaconSender += 10 * int(DataSerie[11] - '0');
            ChaconSender += 1 * int(DataSerie[12] - '0');

            int ChaconRecevr = 10 * int(DataSerie[15] - '0') + int(DataSerie[16] - '0');

            if (DataSerie[14] == '0') {
              for (int i = 1; i <= RADIO_REPEATS; i++) {
                mySwitch.send(ChaconSender, ChaconRecevr, false);
              }
            } else {
              for (int i = 1; i <= RADIO_REPEATS; i++) {
                mySwitch.send(ChaconSender, ChaconRecevr, true);
              }
            }
          }
        }
        Serial.print(F("SP_OK\n"));
#if (CNF_NETWORK == 1)
        client.print(F("SP_OK\n"));
#endif
      } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF SP

      if (DataSerie[0] == 'C' && DataSerie[1] == 'P') { // *************************************************** CP = Configure Pin mode
        //Serial.print("DBG_Serie:");
        //Serial.println(DataSerie);
        //Serial.print("DBG_CP Len=");
        //Serial.println(LenSerial);
        if (LenSerial >= (1 + CNF_NB_DPIN + CNF_NB_APIN + CNF_NB_CPIN)) {
          mySwitch.disableReceive(); // desactive la radio

          for (int i = 2; i < CNF_NB_DPIN; i++) {
            EEPROM.write(i, DataSerie[2 + i]);  // CPzzrzzzzzzzztoo......
          }
          for (int i = 0; i < CNF_NB_APIN; i++) {
            EEPROM.write(CNF_NB_DPIN + i, DataSerie[2 + CNF_NB_DPIN + i]);  // CP..............aaaaaa
          }
          for (int i = 0; i < CNF_NB_CPIN; i++) {
            EEPROM.write(CNF_NB_DPIN + CNF_NB_APIN + i, DataSerie[2 + CNF_NB_DPIN + CNF_NB_APIN + i]);  // CP..............aaaaaa
          }

          ReadyToSend = 1;
          ReloadEEPROM(1);
          Serial.println("CP_OK");
#if (CNF_NETWORK == 1)
          client.println("CP_OK");
#endif
          delay(500); // laisse le temps a jeedom de valider
        } else  {
#if (CNF_NETWORK == 1)
          client.print("BAD_L");
#endif
          Serial.print("BAD_L");
        }
      } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF CP


      if (DataSerie[0] == 'R' && DataSerie[1] == 'F') { // **************************************************************** RF = ReFresh datas
        Serial.print("DATA:");
#if (CNF_NETWORK == 1)
        client.print("DATA:");
#endif
        for (int i = 0; i < CNF_NB_DPIN; i++) {
          //Serial.print(i);
          //Serial.print("=");
          if (pinmode[i] == 'i' || pinmode[i] == 'o') { // verifie que la pin est bien en entree
            Serial.print(OldValue[i]);
#if (CNF_NETWORK == 1)
            client.print(OldValue[i]);
#endif
          } else {
            Serial.print("0");
#if (CNF_NETWORK == 1)
            client.print("0");
#endif
          }
          Serial.print(",");
#if (CNF_NETWORK == 1)
          client.print(",");
#endif
        }
        for (int i = 0; i < CNF_NB_APIN; i++) {
          if (pinmode[CNF_NB_DPIN + i] == 'a') { // Verifie que la pin est bien en Analog
            Serial.print(OldAValue[i]);
#if (CNF_NETWORK == 1)
            client.print(OldAValue[i]);
#endif
          } else {
            Serial.print("0");
#if (CNF_NETWORK == 1)
            client.print("0");
#endif
          }
          Serial.print(",");
#if (CNF_NETWORK == 1)
          client.print(",");
#endif
        }
        for (int i = 0; i < CNF_NB_CPIN; i++) {
          Serial.print(OldCValue[i]);
#if (CNF_NETWORK == 1)
          client.print(OldCValue[i]);
#endif
          if (i < CNF_NB_CPIN - 1) {
            Serial.print(",");  // enleve la derniere virgule
#if (CNF_NETWORK == 1)
            client.print(",");
#endif
          }
        }
        Serial.print("\n");
#if (CNF_NETWORK == 1)
        client.print("\n");
#endif
      } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF RF

      if (DataSerie[0] == 'P' && DataSerie[1] == 'I' && DataSerie[2] == 'N' && DataSerie[3] == 'G') { // ******************************** PING
        ReadyToSend = 1;
        Serial.println("PING_OK");
#if (CNF_NETWORK == 1)
        client.println("PING_OK");
#endif
      } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF PING

      if (DataSerie[0] == 'A' && DataSerie[1] == 'P') { // ****************************************************************************** AP
        if (LenSerial >= (2 + 20)) {
          for (int i = 0; i < 20; i++) {
            EEPROM.write(i + 200, DataSerie[i + 2]); // Stockage de cle API en adresse 200
          }
          ReloadEEPROM(1);
          Serial.print(DataSerie);
          Serial.println("_OK");
#if (CNF_NETWORK == 1)
          client.print(DataSerie);
          client.println("_OK");
#endif
        } else {
          Serial.println("API_BAD");
#if (CNF_NETWORK == 1)
          client.println("API_BAD");
#endif
        }
      } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF AP

      if (DataSerie[0] == 'I' && DataSerie[1] == 'P') { // ****************************************************************************** IP
        if (LenSerial >= (2 + 12)) {
          EEPROM.write(230, (100 * DataSerie[2] + 10 * DataSerie[3] + DataSerie[4])); // Stockage de IP Jeedom en adresse 230
          EEPROM.write(231, (100 * DataSerie[5] + 10 * DataSerie[6] + DataSerie[7])); // Stockage de IP Jeedom en adresse 230
          EEPROM.write(232, (100 * DataSerie[8] + 10 * DataSerie[9] + DataSerie[10])); // Stockage de IP Jeedom en adresse 230
          EEPROM.write(233, (100 * DataSerie[11] + 10 * DataSerie[12] + DataSerie[13])); // Stockage de IP Jeedom en adresse 230
          ReloadEEPROM(1);
          Serial.print(DataSerie);
          Serial.println("_OK");
#if (CNF_NETWORK == 1)
          client.print(DataSerie);
          client.println("_OK");
#endif
        } else {
          Serial.println("IP_BAD");
#if (CNF_NETWORK == 1)
          client.println("IP_BAD");
#endif
        }
      } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF IP


      LenSerial = 0;
      SerailDataOK = 0;
      client.stop();
      Serial.println(F("/Network"));
    }

  } ///////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF IF LenSerial

#endif




  //Serial.print("[1]");
  //////////////////////////////////////////////// PROCEDURE DE DETECTION DES CHANGEMENTS: ////////////////////////////////////////////////////////////////////////

  if ((millis() - timerMAJ) > 30000) { // *********************************************************************** AUTO REFRESH VERS JEEDOM
    RefreshData = 1;
    timerMAJ = millis();
  }

  data = "";

  if (ReadyToSend == 1) { // Attend que le 1er SP Soit OK
    for (int i = 0; i < CNF_NB_DPIN; i++) { // ****************************** Detection des changements de valeurs sur pins DIGITALES
      if (pinmode[i] == 'i' || pinmode[i] == 'o' || pinmode[i] == 'h') {
        NewValue = digitalRead(i);
        if (OldValue[i] != NewValue || RefreshData == 1) {
          if (millis() - LastSend[i] > CNF_DELAY_D_SENDS) { // pas d'envoi de valeur si moins de xxx ms avant la precedente
            LastSend[i] = millis();
            OldValue[i] = NewValue;
            data = data + i;
            data = data + "=";
            data = data + OldValue[i];
            data = data + "&";
            //Serial.print(i);
            //Serial.print(">>");
            //Serial.print(OldValue[i]);
            //Serial.println("<<");
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

        if (aChange == 1 || RefreshData == 1) {
          if (NewAValue != OldAValue[i]) {
            if (millis() - LastSend[i] > CNF_DELAY_A_SENDS) { // pas d'envoi de valeur si moins de xxx ms avant la precedente
              LastSend[i] = millis();
              data = data + (CNF_NB_DPIN + i);
              data = data + "=";
              data = data + NewAValue;
              data = data + "&";
              //Serial.print(CNF_NB_DPIN + i);
              //Serial.print(">>");
              //Serial.print(NewAValue);
              //Serial.println("<<");
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
        if (cChange == 1 || RefreshData == 1) {
          if (NewCValue != OldCValue[i] || RefreshData == 1) {
            if (millis() - LastSend[i] > CNF_DELAY_A_SENDS) { // pas d'envoi de valeur si moins de xxx ms avant la precedente
              LastSend[i] = millis();
              data = data + (CNF_NB_DPIN + CNF_NB_APIN + i);
              data = data + "=";
              data = data + NewCValue;
              data = data + "&";
              //Serial.print(CNF_NB_DPIN + CNF_NB_APIN + i);
              //Serial.print(">>");
              //Serial.print(NewCValue);
              //Serial.println("<<");
              OldCValue[i] = NewCValue;
            }
          }
        }
      }
    }
#if (CNF_NETWORK == 1)
    if (data != "") JeeSendData();
#endif
    RefreshData = 0;
  }

  //Serial.print("[2]");

  //   __        __     __      __   ___  __   ___  __  ___    __
  //  |__)  /\  |  \ | /  \    |__) |__  /  ` |__  |__)  |  | /  \ |\ |
  //  |  \ /~~\ |__/ | \__/    |  \ |___ \__, |___ |     |  | \__/ | \|
  //

  if (mySwitch.available()) {
    LastRadioMessage = millis();
    RFData = mySwitch.getReceivedValue();
    RFAddr = mySwitch.getReceivedAddr();
    RFProtocol = mySwitch.getReceivedProtocol();
    RFLenght = mySwitch.getReceivedBitlength();
    RFDelay = mySwitch.getReceivedDelay();
    unsigned int* raw = mySwitch.getReceivedRawdata();

    if (oldRFData != RFData || oldRFAddr != RFAddr || oldRFProtocol != RFProtocol) {
      RAZRadio = 0;
      oldRFData = RFData;
      oldRFAddr = RFAddr;
      oldRFProtocol = RFProtocol;
      data = "";
      data += RadioRXpin;
      Serial.print(RadioRXpin);

      data += "=RFD:";
      Serial.print(">>RFD:");

      data += RFData;
      Serial.print(RFData);

      data += ":A:";
      Serial.print(":A:");

      data += RFAddr;
      Serial.print(RFAddr);

      data += ":P:";
      Serial.print(":P:");

      data += RFProtocol;
      Serial.print(RFProtocol);

      Serial.println("<<");

#if (CNF_NETWORK == 1)
      JeeSendData();
#endif
      /*
      #if defined(FOR_BOBOX59_ONLY) //////// NON UTILISE POUR AUTRES QUE BOBOX59
        Serial.print("Raw data: ");
        for (int i=0; i<= RFLenght*2; i++) {
          Serial.print(raw[i]);
          Serial.print(",");
        }
        Serial.println();
      #endif
      */
    }
    mySwitch.resetAvailable();
  }// End of mySwitch Reception

  /*  if (millis() - LastRadioMessage > 500 && RAZRadio != 1) { // envoie une mise a 0 du radio apres 500 millisecondes
      RAZRadio = 1;
      oldRFData = 0;
      oldRFAddr = 0;
      oldRFProtocol = 99;

      Serial.print(RadioRXpin);
      data = "";
      data += RadioRXpin;

      Serial.println(">>0<<");
      data += "=0";

  #if (CNF_NETWORK == 1)
      JeeSendData();
  #endif

    }
  */
  //Serial.print("[3]");

#if (CNF_DHT_1_PIN + CNF_DHT_2_PIN + CNF_DHT_3_PIN + CNF_DHT_4_PIN + CNF_DHT_5_PIN + CNF_DHT_6_PIN + CNF_DHT_7_PIN + CNF_DHT_8_PIN > 0) // ************************************************************************************** GESTION DES SONDES DHT
  k=1;
  if ((millis() - tempsDHT) > 45000) { //Si rien non actualisé depuis 45 Secondes
#if (CNF_DHT_1_PIN > 1)
    k = k + 2;
    DHTValue[1] = (dht1.getHumidity());
    DHTValue[2] = (dht1.getTemperature());
#endif
#if (CNF_DHT_2_PIN > 1)
    k = k + 2;
    DHTValue[3] = (dht2.getHumidity());
    DHTValue[4] = (dht2.getTemperature());
#endif
#if (CNF_DHT_3_PIN > 1)
    k = k + 2;
    DHTValue[5] = (dht3.getHumidity());
    DHTValue[6] = (dht3.getTemperature());
#endif
#if (CNF_DHT_4_PIN > 1)
    k = k + 2;
    DHTValue[7] = (dht4.getHumidity());
    DHTValue[8] = (dht4.getTemperature());
#endif
#if (CNF_DHT_5_PIN > 1)
    k = k + 2;
    DHTValue[9] = (dht5.getHumidity());
    DHTValue[10] = (dht5.getTemperature());
#endif
#if (CNF_DHT_6_PIN > 1)
    k = k + 2;
    DHTValue[11] = (dht6.getHumidity());
    DHTValue[12] = (dht6.getTemperature());
#endif
#if (CNF_DHT_7_PIN > 1)
    k = k + 2;
    DHTValue[13] = (dht7.getHumidity());
    DHTValue[14] = (dht7.getTemperature());
#endif
#if (CNF_DHT_8_PIN > 1)
    k = k + 2;
    DHTValue[15] = (dht8.getHumidity());
    DHTValue[16] = (dht8.getTemperature());
#endif
    Serial.print("DHT:");
    data = "";
    for (int i = 1; i < k; i++) {
      data += 500 + i;
      Serial.print(DHTValue[i]);
      data += "=";
      data += DHTValue[i];
      //if (i < 16) {
        Serial.print(";");
        data += "&";
      //}
    }
    Serial.print('\n');
    Serial.println(data);
    JeeSendData();
    tempsDHT = millis(); //on stocke la nouvelle heure
  }
#endif

  //Serial.print("[4]");

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

  if ((millis() - tempsLOOP) > 5000) { //Si rien non actualisé depuis 5 Secondes

    CustomValue[0] = CustomValue[1] / 2;
    CustomValue[2]++;
    tempsLOOP = millis(); // Ligne a ne pas modifier. Merci.
  }












  //////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////// FIN DES CUSTOMS  ///////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////////////////////////////////

  //Serial.print("[5]");


} // EOF void loop()







//
//    ______ ____  _   _  _____ _______ _____ ____  _   _  _____
//   |  ____/ __ \| \ | |/ ____|__   __|_   _/ __ \| \ | |/ ____|
//   | |__ | |  | |  \| | |       | |    | || |  | |  \| | (___
//   |  __|| |  | | . ` | |       | |    | || |  | | . ` |\___ \ 
//   | |   | |__| | |\  | |____   | |   _| || |__| | |\  |____) |
//   |_|    \____/|_| \_|\_____|  |_|  |_____\____/|_| \_|_____/
//

#if (CNF_NETWORK == 1)

void JeeSendData() {
  Serial.println(F("JeeSend"));
  Serial.println(data);
  if (data != "") {
    //Serial.print("[J1]");
    EthernetClient client2 = server.available();
    //Serial.print("[J2]");

    int inChar;

    Serial.print(F("connect..."));
    if (client2.connect(CNF_IP_JEEDOM, CNF_PORT_JEEDOM)) {

      Serial.println(F("OK"));

      #if (CNF_JEEDOM_BOX == 0)
      client2.print(F("GET /jeedom/plugins/arduidom/core/php/jeeArduidom.php?api="));
      #endif
      #if (CNF_JEEDOM_BOX == 1)
      client2.print(F("GET /plugins/arduidom/core/php/jeeArduidom.php?api="));
      #endif
      client2.print(CNF_API);
      client2.print(F("&arduid="));
      client2.print(CNF_AID);
      client2.print("&");
      int datalength = data.length();
      //Serial.println(datalength);
      //Serial.println(data);
      data = data.substring(0, datalength - 1); // supprimer le dernier &
      client2.print(data);
      client2.println(F(" HTTP/1.1"));
      client2.print("Host: "); // SERVER ADDRESS HERE TOO
      client2.println(CNF_IP_JEEDOM); // SERVER ADDRESS HERE TOO
      delay(100); // delai 100ms compatibilité nginx
      client2.println(F("Connection: close"));
      client2.println();
      //Serial.println(data);

    }
    else
    {
      Serial.println(F("fail"));
    }

    // connectLoop controls the hardware fail timeout
    int connectLoop = 0;

    while (client2.connected())
    {
      while (client2.available())
      {
        inChar = client2.read();
        //Serial.write(inChar);
        // set connectLoop to zero if a packet arrives
        connectLoop = 0;
      }

      connectLoop++;

      // if more than 5000 milliseconds since the last packet
      if (connectLoop > 5000)
      {
        // then close the connection from this end.
        //Serial.println();
        Serial.println(F("Timeout"));
        client2.stop();
      }
      // this is a delay for the connectLoop timing
      delay(1);
    }

    //Serial.println();

    Serial.println(F("close."));
    // close client end
    client2.stop();
  } // end of if data != ""
} // End of JeeSendData()
#endif

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
  for (int i = 0; i < 20; i++) {
    CNF_API[i] = EEPROM.read(200 + i); // Cle API Jeedom
  }
  //IPAddress CNF_IP_JEEDOM (EEPROM.read(230), EEPROM.read(231), EEPROM.read(232), EEPROM.read(233)); // IP JeeDom

}// END OF ReloadEEPROM()

void InitEEPROM() {
  EEPROM.write(1, ArduiDomVersion); // Pin Mode
  for (int i = 2; i < 400; i++) {
    EEPROM.write(i, '\0'); // Pin 0 Mode // RESERVED FOR USB
  }
} // END OF InitEEPROM()

