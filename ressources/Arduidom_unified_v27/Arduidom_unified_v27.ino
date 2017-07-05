#include <Arduino.h>
// Arduino Sketch par Benoit Masquelier     masquelierb@gmail.com
// Merci également aux contributions de Alois et Chevalir et des participants du forum Jeedom.
// Utilisable pour le plugin Arduidom sur Jeedom
//
// ATTENTION : Il est IMPERATIF d'utiliser l'IDE 1.6.7 ou + pour que tout soit compatible !!!
//
// Les PINs 10,11,12,13 Sont RESERVEES A LA CONNECTION Ethernet pour les UNO !!!
// Les PINs 10,50,51,52 Sont RESERVEES A LA CONNECTION Ethernet pour les MEGA !!!
//
/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
///////////
///////////         Vos Parties CUSTOMS et PERSONNELLES se trouveront désormais A LA FIN du sketch !
///////////
///////////         Veuillez prendre le temps de lire et configurer les variables ci-dessous. Merci.
///////////
/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
//
//---------------------------------------------------------------------------------------------------------------------------------------------------
//#define FOR_BOBOX59_ONLY    // LIGNE A RETIRER POUR DESACTIVER DES FONCTIONS QUI ME SONT PERSONELLES
//#define DBG_PRINT_CP
//
//
//---------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------- CONFIGURATIONS DU RESEAU ARDUINO --------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------
//
// INFO: La cle API de JeeDom est automatiquement envoyee a l'arduino via la Configuration des Pins puis stockee dans l'EEPROM
//
// Le réseau ou wifi sont définis désormais par le CNF_BOARD_MODEL !
//
//
//                                              TYPE DE BOARD
//                                      ----------------------------------
//
#define CNF_BOARD_MODEL 1  // Mettre la valeur correspondante au tableau ci-dessous !
//                         // 
//                         // 1 = Arduino Generique (UNO / NANO / MEGA / etc) en USB
//                         // 
//                         // 11 = Arduino (UNO / NANO / MEGA / etc) en LAN avec un SHIELD ETHERNET
//                         // 
//                         // 21 = ESP 201 en WiFi (basé sur l'ESP8266, 22 pins + 4 pins   ----   Librairie "ESP8266WiFi" nécessaire
//                         // 22 = WeMOS D1 Mini en WiFi (basé sur l'ESP8266, 16 pins + port USB
//
//
#define CNF_DHT 1 // Mettre à 0 pour desactiver les DHT pour gagner en espace Programme/Ram surtout sur les petits arduino ( 4,9% Firmware / 6,3% RAM sur un UNO)
#define CNF_DHT11_COMPATIBILITY 0 // Mettre à 1 pour activer la compatibilité des Sondes DHT 11, Attention, un delai de 2 secondes par sonde Dht est ajouté !!!
//
#define CNF_RADIO 1 // Mettre à 0 pour desactiver la RADIO pour gagner en espace Programme/Ram surtout sur les petits arduino ( consomme 18,8% Firmware / 23,9% RAM sur un UNO )
//
#if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29 )
    #if (CNF_BOARD_MODEL > 20 && CNF_BOARD_MODEL <= 29 )
        #include <ESP8266WiFi.h>
        const char* ssid     = "???????????"; // SSID de votre routeur WiFi
        const char* password = "???????????"; // Clé WiFi de votre routeur
    #else
        #include <SPI.h>
        #include <Ethernet.h>
    #endif
    //
    IPAddress CNF_IP_JEEDOM (192, 168, 1, 2); // ADRESSE IP du serveur JeeDom
    //
    IPAddress CNF_IP_ARDUIN (192, 168, 1, 203); // ADRESSE IP DE L'ARDUINO
    //                /!\ l'IP est en DHCP sur WeMOS /!\
    //          il vous faudra creer une reservation d'IP sur votre routeur pour eviter les soucis !
    //
    IPAddress CNF_IP_GATEWAY (192, 168, 1, 254);

    IPAddress CNF_IP_MASK(255,255,255,0);
    //
    #define CNF_PORT_JEEDOM 80 // Port d'ecoute Jeedom
    //
    #define CNF_JEEDOM_BOX 1 // Mettre a 1 si c'est une jeedom Box ( pas de /jeedom dans le http pour les Jeedom BOX)
    //
    // CNF_AID est Désormais détecté automatiquement par jeedom ! Numéro de l'arduino dans jeedom
#endif

//
//
//--------------------------------------------------------------------------------------------------------------------------------------------------
// ---------------------- CONFIGURATIONS DES DIFFERENTES VARIABLES DISPONIBLE ----------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------
// DELAI D'EXECUTION ET MISE A JOUR DES CUSTOMS (ATTENTION A NE PAS METTRE TROP BAS POUR NE PAS SURCHARGER L'ARDUINO)
#define CUSTOM_DELAY 30000 // Temps en MILLISECONDES ou est executé la partie Customs
//
#define DHT_DELAY 45000 // Temps en MILLISECONDES entre les captures et envoi des valeurs de sondes DHT
// EMETEUR RADIO
#define RADIO_REPEATS 10 // Nombre de repetitions des envois de messages Radio (1 a 20, augmenter en cas de soucis de transmission vers prises)
//
// DELTA
#define CNF_APINS_DELTA 10 // Valeur du delta pour envoi changement vers jeedom (si la valeur change d'au moins xxx en une fois, envoi a jeedom
#define CNF_CPINS_DELTA 0.1 // Valeur du delta pour envoi changement vers jeedom (si la valeur change d'au moins xxx en une fois, envoi a jeedom
//
// DELAY : ATTENTION, une valeur trop petite peut encombrer le port serie avec les parasites !!!
#define CNF_DELAY_D_SENDS 200 // Delai entre chaque mise a jour d'entrees vers jeedom
#define CNF_DELAY_A_SENDS 1000 // Delai entre chaque mise a jour d'entrees vers jeedom
//--------------------------------------------------------------------------------------------------------------------------------------------------
// -------- LES SONDES DHT se configurent désormais directement dans la configuration des pins du plugin -------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------
// PARTIE DEFINITION
// Vos #define et autre ici




/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
///////////
///////////                                 La Librairie AHRCSwitch n'est plus necessaire non plus car integree directement dans le sketch
///////////
///////////                                 ATTENTION : Il est IMPERATIF d'utiliser l'IDE 1.6.7 ou + pour que tout soit compatible !!!
///////////
/////////// /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\ /!\
//
//
//
//
#include <EEPROM.h>
#if (CNF_RADIO == 1)
    #include "Arduidom_Radio.h"
    RCSwitch mySwitch = RCSwitch();
#endif
#if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29)
    //--------------------------------------------------------------------------------------------------------------------------------------------------
    //----------------------- CONFIGURATION DE L'ADRESSE MAC ARDUINO -----------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------------
    //byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED }; // ADRESSE MAC DE L'ARDUINO, A CHANGER DANS LE CAS DE PLUSIEURS ARDUINO DANS LE MEME RESEAU
    //--------------------------------------------------------------------------------------------------------------------------------------------------
    #if (CNF_BOARD_MODEL > 20 && CNF_BOARD_MODEL <= 29)
        WiFiServer server(58174);
    #else
        EthernetServer server(58174);
    #endif
#endif
#if (CNF_DHT == 1)
    #include "DHT.h"
#endif
#define ArduiDomVersion 127 // Sers a verfifier le contenu de l'EEPROM
#if defined(__AVR_ATmega168__) || defined(__AVR_ATmega168P__)
    #define CNF_NB_DPIN 14
    #define CNF_NB_APIN 6
    #define CNF_NB_CPIN 1 // Nombre de Customs (IMPOSSIBLE SUR LES 168 FAUTE DE RAM DISPONIBLE)
#elif defined(__AVR_ATmega328P__)
    #define CNF_NB_DPIN 14
    #define CNF_NB_APIN 6
    #define CNF_NB_CPIN 8 // Extensible à 128 Maximum
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
#elif (CNF_BOARD_MODEL == 21) // esp201
    #define CNF_NB_DPIN 15
    #define CNF_NB_APIN 1
    #define CNF_NB_CPIN 16 // Extensible à 128 Maximum
#elif (CNF_BOARD_MODEL == 22) // wemos d1 mini
    #define CNF_NB_DPIN 17
    #define CNF_NB_APIN 1
    #define CNF_NB_CPIN 16 // Extensible à 128 Maximum
#else
    #error "IMPOSSIBLE DE DETECTER LE TYPE DE BOARD, VERIFIER LA CONFIGURATION du CNF_BOARD_MODEL, sinon CONTACTER BOBOX59"
#endif

// Variables utilisees pour RCSwitch
#if (CNF_RADIO == 1)
    unsigned long RFData = 0; // Valeur du Data Recu par 433
    unsigned long RFDataLastSend = 0; // Valeur du Data Recu par 433
    unsigned long RFAddr = 0; // Valeur de l'addresse de l'emmeteur 433
    unsigned long RFAddrLastSend = 0; // Valeur de l'addresse de l'emmeteur 433
    byte RFProtocol = 0; // Valeur du Protocole du message 433
    byte RFProtocolLastSend = 0; // Valeur du Protocole du message 433
    unsigned int RFDelay = 0; // Valeur du Protocole du message 433
    unsigned int RFLenght = 0; // Valeur du Protocole du message 433
    unsigned long ChaconSender;
    int ChaconRecevr = 0;
    unsigned long LastRadioMessage = 0; // Millis du dernier message radio
#endif
// Variables pour SERIE
boolean SerialDataOK = false;
boolean ForceRefreshData = false; // Mise a 1 toute les 30 secondes pour envoi des données
char DataSerie[216]; // a string to hold incoming data
char l[3]; // a string to hold incoming data
byte LenSerial = 0;
// Variables pour le comparateur
byte OldValue[CNF_NB_DPIN +1]; // anciennes valeurs pour detection changements
unsigned int OldAValue[CNF_NB_APIN +1]; // anciennes valeurs pour detection changements
float OldCValue[CNF_NB_CPIN +1]; // anciennes valeurs pour customs
float CustomValue[CNF_NB_CPIN +1]; // valeurs en cours pour customs
unsigned long LastSend[CNF_NB_DPIN + CNF_NB_APIN + CNF_NB_CPIN +1]; // anciennes valeurs pour detection changements
byte NewValue = 0; // tampon nouvelle valeurs pour detection changements
unsigned int NewAValue = 0; // tampon nouvelle valeurs pour detection changementsbyte LenSerial;
float NewCValue = 0;
unsigned long TimerDelays[CNF_NB_DPIN +1] = {0};
byte TimerDelayAction[CNF_NB_DPIN +1] = {0};
int TimerBlink[CNF_NB_DPIN +1] = {0};
unsigned int ACompare = 0;
float CCompare = 0;
// Variables systeme
unsigned long TimerReadyToSend = 0;
unsigned long TimerCustomHook = 0; //tempo d'actualisation
String data;
String request;
byte API_LEN = 20;
byte compteur = 0;
String CNF_API = "....";    // Creation de la variable vide, jeedom l'enverra pour la stocker dans l'eeprom, NE PAS LA CHANGER ICI !
byte RadioRXpin = 0;
byte RadioTXPin = 0;
byte pinToSet = 0;
boolean RAZRadio = false;
boolean ReadyToSend = false; // Arduino init complet
boolean negval = false; //Valeur negative pour Customs
byte check;
#if (CNF_DHT == 1)
    unsigned long TimerUpdateDHT = 0; //tempo d'actualisation
    byte dhtpin[9] = {0,0,0,0,0,0,0,0};
    byte DHT_QTY = 0;
    float DHTValue[17] = {0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0}; // Valeurs Sondes DHT
    DHT dhtlib;
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

void InitEEPROM() {
    Serial.println(F("CLEAR EEPROM"));
    for (int i = 0; i < 512; i++) {
        EEPROM.write(i, '\0');
    }
    #if (CNF_BOARD_MODEL > 20)
    EEPROM.commit();
    #endif
    EEPROM.write(240, ArduiDomVersion); // Pin Mode
    #if (CNF_BOARD_MODEL > 20)
    EEPROM.commit();
    #endif
}
// END OF InitEEPROM()




//
//      ______ ____  _   _  _____ _______ _____ ____  _   _  _____
//     |  ____/ __ \| \ | |/ ____|__   __|_   _/ __ \| \ | |/ ____|
//     | |__ | |  | |  \| | |       | |    | || |  | |  \| | (___
//     |  __|| |  | | . ` | |       | |    | || |  | | . ` |\___ \
//     | |   | |__| | |\  | |____   | |   _| || |__| | |\  |____) |
//     |_|    \____/|_| \_|\_____|  |_|  |_____\____/|_| \_|_____/
//

void JeeSendData() {
    #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29)
        Serial.println(F("JeeSend"));
        Serial.println(data);
        if (data != "") {
            Serial.print("[J1]");
            #if (CNF_BOARD_MODEL > 20 && CNF_BOARD_MODEL <= 29)
                WiFiClient client2;
            #else
                EthernetClient client2 = server.available();
            #endif
            Serial.println("[J2]");

            int inChar;
            Serial.print("Connect to ");
            Serial.print(CNF_IP_JEEDOM);
            Serial.println(F("..."));
            if (client2.connect(CNF_IP_JEEDOM, CNF_PORT_JEEDOM)) {

                Serial.println(F("OK"));
                //Serial.print(F("GET "));
                client2.print(F("GET "));
                #if (CNF_JEEDOM_BOX == 0)
                //Serial.print(F("/jeedom"));
                    client2.print(F("/jeedom"));
                #endif
                //Serial.print(F("/plugins/arduidom/core/php/jeeArduidom.php?api="));
                client2.print(F("/plugins/arduidom/core/php/jeeArduidom.php?api="));
                //Serial.print(CNF_API);
                client2.print(CNF_API);
                //Serial.print(F("&arduid=net&"));
                client2.print(F("&arduid=net&"));
                int datalength = data.length();
                //Serial.println(datalength);
                //Serial.println(data);
                data = data.substring(0, datalength - 1); // supprimer le dernier &
                //Serial.print(data);
                client2.print(data);
                //Serial.println(F(" HTTP/1.1"));
                client2.println(F(" HTTP/1.1"));
                //Serial.print("Host: "); // SERVER ADDRESS HERE TOO
                client2.print("Host: "); // SERVER ADDRESS HERE TOO
                //Serial.println(CNF_IP_JEEDOM); // SERVER ADDRESS HERE TOO
                client2.println(CNF_IP_JEEDOM); // SERVER ADDRESS HERE TOO
                delay(100); // delai 100ms compatibilité nginx
                client2.println(F("Connection: close"));
                client2.println();
                //Serial.println(data);
                Serial.print("[J3]");

            }
            else
            {
                Serial.println(F("fail"));
            }

            Serial.println("[J4]");

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
            Serial.println("[J5]");

        } // end of if data != ""
    #endif
} // End of JeeSendData()




//                    _       _ ______               _
//                   (_)     | |  ____|             | |
//      ___  ___ _ __ _  __ _| | |____   _____ _ __ | |_
//     / __|/ _ \ '__| |/ _` | |  __\ \ / / _ \ '_ \| __|
//     \__ \  __/ |  | | (_| | | |___\ V /  __/ | | | |_
//     |___/\___|_|  |_|\__,_|_|______\_/ \___|_| |_|\__|
//
//
void serialEvent() {
    while (Serial.available() > 0) { // Lecture du port serie
        Serial.readBytes(l, 1);
        DataSerie[LenSerial] = l[0];
        if (DataSerie[LenSerial] == '\n') {
            DataSerie[LenSerial] = char(0);
            SerialDataOK = true;
            break;
        }
        LenSerial += 1;
        if (LenSerial > 214) {
            LenSerial = 0;
            break;
        }
    }
} // EOF serialEvent()




void ReloadEEPROM() {
    #if (CNF_RADIO == 1)
        mySwitch.disableReceive();
        RadioRXpin = 0;
    #endif
    #if (CNF_DHT == 1)
        DHT_QTY = 0;
        for (int i = 1; i <= 8; i++) {
            dhtpin[i] = 0;
        }
    #endif
    #if (CNF_BOARD_MODEL == 22)
      for (int i = 0; i < CNF_NB_DPIN + CNF_NB_APIN + CNF_NB_CPIN; i++) {
    #else
      for (int i = 2; i < CNF_NB_DPIN + CNF_NB_APIN + CNF_NB_CPIN; i++) {
    #endif
        //EEPROM.get(i, pin_mode[i]); // Pin Modes
        #if defined(DBG_PRINT_CP)
            Serial.print(F("Pin "));
            Serial.print(i);
            Serial.print(F(" is "));
            Serial.print(char(EEPROM.read(i)));
            Serial.print(F(":"));
        #endif
        #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 19)
            #if defined(__AVR_ATmega1280__) || defined(__AVR_ATmega2560__)
                if (i == 10 || i == 50 || i == 51 || i == 52) {
            #else
                if (i == 10 || i == 11 || i == 12 || i == 13) {
            #endif
                #if defined(DBG_PRINT_CP)
                    Serial.print(F("NetW"));
                #endif
                EEPROM.write(i,'N');
            }
        #endif
        #if (CNF_BOARD_MODEL == 21) //esp201
            if ((i > 5 && i < 12) || i == 2 || i == 3) {
            #if defined(DBG_PRINT_CP)
                Serial.print(F("N/A"));
            #endif
            EEPROM.write(i,'N');
            }
        #endif

        #if (CNF_BOARD_MODEL == 22) //d1mini                  1 & 3 => Port série USB
            if ((i >= 6 && i <= 11) || i == 0 || i == 2 || i == 1 || i == 3) {
            #if defined(DBG_PRINT_CP)
                Serial.print(F("N/A"));
            #endif
            EEPROM.write(i,'N');
            }
        #endif

        if (EEPROM.read(i) == 'z' || EEPROM.read(i) == '\0') {
            TimerDelays[i] = 0;
            #if defined(DBG_PRINT_CP)
                Serial.print(F("DIS"));
            #endif
        }
        if (EEPROM.read(i) == 'i') {
            pinMode(i, INPUT);
            #if defined(DBG_PRINT_CP)
                Serial.print(F("IN"));
            #endif
        }
        if (EEPROM.read(i) == 'j') {
            pinMode(i, INPUT);
            #if defined(DBG_PRINT_CP)
                Serial.print(F("IN_reverse"));
            #endif
        }
        if (EEPROM.read(i) == 'y') {
            pinMode(i, INPUT_PULLUP);
            #if defined(DBG_PRINT_CP)
                Serial.print(F("IN_pullup"));
            #endif
        }
        if (EEPROM.read(i) == 'a') {
            pinMode(i, INPUT);
            #if defined(DBG_PRINT_CP)
                Serial.print(F("ANA"));
            #endif
        }
        if (EEPROM.read(i) == 'o') {
            pinMode(i, OUTPUT);
            #if defined(DBG_PRINT_CP)
                Serial.print(F("OUT"));
            #endif
        }
        if (EEPROM.read(i) == 'e') {
            pinMode(i, OUTPUT);
            digitalWrite(i, HIGH);
            #if defined(DBG_PRINT_CP)
                Serial.print(F("OUTD"));
            #endif
        }
        if (EEPROM.read(i) == 'c') {
            #if defined(DBG_PRINT_CP)
                Serial.print(F("CIN"));
            #endif
        }
        if (EEPROM.read(i) == 'd') {
            #if defined(DBG_PRINT_CP)
                Serial.print(F("COU"));
            #endif
        }
        if (EEPROM.read(i) == 'p') {
            pinMode(i, OUTPUT);
            TimerDelays[i] = 0;
            #if defined(DBG_PRINT_CP)
                Serial.print(F("PWM"));
            #endif
        }
        if (EEPROM.read(i) == 'u') {
            pinMode(i, OUTPUT);
            TimerDelays[i] = 0;
            #if defined(DBG_PRINT_CP)
                Serial.print(F("OPUP"));
            #endif
        }
        if (EEPROM.read(i) == 'v') {
            pinMode(i, OUTPUT);
            TimerDelays[i] = 0;
            digitalWrite(i, 1);
            #if defined(DBG_PRINT_CP)
                Serial.print(F("OPDWN"));
            #endif
        }
        if (EEPROM.read(i) == 'x') {
            pinMode(i, OUTPUT);
            TimerDelays[i] = 0;
            #if defined(DBG_PRINT_CP)
                Serial.print(F("XCHG"));
            #endif
        }
        if (EEPROM.read(i) == 'b') {
            pinMode(i, OUTPUT);
            TimerDelays[i] = 0;
            #if defined(DBG_PRINT_CP)
                Serial.print(F("BLNK"));
            #endif
        }
        #if (CNF_RADIO == 1)
            if (EEPROM.read(i) == 'r') {
                if (i == 2) mySwitch.enableReceive(0);
                if (i == 3) mySwitch.enableReceive(1);
                #if defined(__AVR_ATmega168__) ||defined(__AVR_ATmega168P__) ||defined(__AVR_ATmega328P__) ||defined(__AVR_ATmega32U4__)
                #else
                    if (i == 21) mySwitch.enableReceive(2);
                    if (i == 20) mySwitch.enableReceive(3);
                    if (i == 19) mySwitch.enableReceive(4);
                    if (i == 18) mySwitch.enableReceive(5);
                #endif
                RadioRXpin = i;
                #if defined(DBG_PRINT_CP)
                    Serial.print(F("RRX"));
                #endif
            }
            if (EEPROM.read(i) == 't') {
                pinMode(i, OUTPUT);
                RadioTXPin = i;
                mySwitch.enableTransmit(i); // Transmission sur Pin
                mySwitch.setRepeatTransmit(RADIO_REPEATS); // Repete x fois le message
                #if defined(DBG_PRINT_CP)
                    Serial.print(F("RTX"));
                #endif
            }
        #endif
        #if (CNF_DHT == 1)
            if (EEPROM.read(i) >= 49 && EEPROM.read(i) <= 57) {
                #if defined(DBG_PRINT_CP)
                    Serial.print(F("DHT"));
                #endif
            }
            for (byte d=1; d <= 8; d++) {
                if (EEPROM.read(i) == '0' + d) {
                    dhtpin[d] = i;
                    DHT_QTY += 1;
                    #if defined(DBG_PRINT_CP)
                        Serial.print(d);
                    #endif
                }
            }
        #endif
        #if defined(DBG_PRINT_CP)
            Serial.print("\n");
        #endif


    }

    #if (CNF_BOARD_MODEL > 10)
  
      API_LEN = EEPROM.read(400);
      if (API_LEN == 20) CNF_API = "....................";
      if (API_LEN == 32) CNF_API = "................................";
      if (API_LEN == 48) CNF_API = "................................................";
      for (int i = 0; i < API_LEN; i++) {
          CNF_API[i] = EEPROM.read(401 + i); // Cle API Jeedom
      }
      Serial.println();
      Serial.print(F("API LEN:"));
      Serial.println(API_LEN);
      Serial.print(F("API="));
      Serial.println(CNF_API);

    #endif
            
}// END OF ReloadEEPROM()


void setup() {
    CNF_API.reserve(49);
    data.reserve(216); // Reserve 216 bytes for data string used in many functions
    request.reserve(20); // Reserve 20 bytes for request replys
    Serial.begin(115200); // Init du Port serie/USB
    Serial.setTimeout(5); // Timeout 5ms
    #if (CNF_BOARD_MODEL > 20)
    EEPROM.begin(512);
    #endif
    #if defined(DBG_PRINT_CP)
         Serial.print("Check eep version");
         delay(1000);
    #endif
    if (EEPROM.read(240) != ArduiDomVersion) {
        #if defined(DBG_PRINT_CP)
             Serial.println("init eeprom...");
        #endif
        InitEEPROM();
    } else {
      Serial.println("OK");
    }
    #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 19)
        Ethernet.begin(mac, CNF_IP_ARDUIN);
        server.begin();
        ReloadEEPROM();
    #endif
    #if (CNF_BOARD_MODEL > 20 && CNF_BOARD_MODEL <= 29)
        // We start by connecting to a WiFi network
        Serial.println();
        Serial.println();
        Serial.print(F("Connecting to "));
        Serial.println(ssid);
        delay(500);
        WiFi.begin(ssid, password);
        while (WiFi.status() != WL_CONNECTED) {
          delay(500);
          Serial.print(".");
        }
        Serial.println("");
        Serial.println(F("WiFi connected"));  
        WiFi.config(CNF_IP_ARDUIN, CNF_IP_GATEWAY, CNF_IP_MASK);
        Serial.print(F("IP address: "));
        Serial.println(WiFi.localIP());
        Serial.println();
        server.begin();
        ReloadEEPROM();
    #endif

    TimerReadyToSend = millis();
    #if (CNF_DHT == 1)
        TimerUpdateDHT = millis();
    #endif
    TimerCustomHook = millis();
    //for (int i=0; i <= CNF_NB_DPIN; i++) {
    //    TimerDelays[i] = millis();
    //}

    //--------------------------------------------------------------------------------------------------------------------------------------------------
    // PARTIE SETUP // DEPUIS LA V2, VOIR LE BAS DU SKETCH POUR VOS INTEGRATIONS
    /**
    ** @@RC SETUP
    **/
    //setupHook(); // DEPUIS LA V2, VOIR LE BAS DU SKETCH POUR VOS INTEGRATIONS
    //--------------------------------------------------------------------------------------------------------------------------------------------------
    delay(1000);
    Serial.println("HELLO");
} // End Of void setup()




//     __      __   _     _      _
//     \      / /  (_)   | |    | |
//      \ \  / /__  _  __| |    | |     ___   ___  _ __
//       \ \/ / _ \| |/ _` |    | |    / _ \ / _ \| '_ \
//        \  / (_) | | (_| |    | |___| (_) | (_) | |_) |
//         \/ \___/|_|\__,_|    |______\___/ \___/| .__/
//                                                | |
//                                                |_|
//
void loop() {

    //     __   ___  __                  __   ___  __   ___  __  ___    __
    //    /__` |__  |__) |  /\  |       |__) |__  /  ` |__  |__)  |  | /  \ |\ |
    //    .__/ |___ |  \ | /~~\ |___    |  \ |___ \__, |___ |     |  | \__/ | \|
    //
delay(1); // Compatibility WiFi Modules

    #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29)
        #if (CNF_BOARD_MODEL > 20 && CNF_BOARD_MODEL <= 29)
            WiFiClient client = server.available();
        #else
            EthernetClient client = server.available();
        #endif
        if (client) {
            Serial.println(F("Net in:"));
            while (client.connected()) {
                if (client.available()) {
                    char c = client.read();
                    Serial.print("(");
                    DataSerie[LenSerial] = c;
                    if (DataSerie[LenSerial] == '\n') {
                        DataSerie[LenSerial] = char(0);
                        SerialDataOK = true;
                    }
                    LenSerial += 1;
                    Serial.write(c);
                    Serial.print(")");
                    if (c == '\n') {
                        Serial.println("");
                        Serial.println(F("</n>"));
                        break;
                    }
                }
            }
        }
    #endif
    if (SerialDataOK) { // Donnees dans le buffer Serie
        boolean check = false; // it was set to false if all is ok
        Serial.print(F("DBG_todo:")); Serial.println(DataSerie);

        if (DataSerie[0] == 'S' && DataSerie[1] == 'P') { // ************************************************************ SP = Set Pin
            request = DataSerie;
            pinToSet = 10 * int(DataSerie[2] - '0'); // dizaines
            pinToSet += int(DataSerie[3] - '0'); // unites
            //Serial.print(F("DBG_pintoset=")); Serial.println(pinToSet);
            //Serial.print(F("DBG_pinMode=")); Serial.println(char(EEPROM.read(pinToSet)));
            if (EEPROM.read(pinToSet) == 'o' || EEPROM.read(pinToSet) == 'i' || EEPROM.read(pinToSet) == 'y') { // also on mode i for pull up of inputs
                Serial.println(F("DBG_PinMode is o/i/y"));
                if (DataSerie[4] == '0') {
                    digitalWrite(pinToSet, LOW);
                    check = true;
                }
                if (DataSerie[4] == '1') {
                    digitalWrite(pinToSet, HIGH);
                    check = true;
                }
            }
            if (EEPROM.read(pinToSet) == 'e') { // also on mode i for pull up of inputs
                Serial.println(F("DBG_PinMode is e"));
                if (DataSerie[4] == '0') {
                    digitalWrite(pinToSet, HIGH);
                    check = true;
                }
                if (DataSerie[4] == '1') {
                    digitalWrite(pinToSet, LOW);
                    check = true;
                }
            }
            if (EEPROM.read(pinToSet) == 'x') { // x : invert output
                int pintime = 1000 * int(DataSerie[4] - '0') + 100 * int(DataSerie[5] - '0') + 10 * int(DataSerie[6] - '0') + int(DataSerie[7] - '0');
                    digitalWrite(pinToSet, 1 - digitalRead(pinToSet));
                    check = true;
                if (pintime > 0) {
                    TimerDelays[pinToSet] = millis() + pintime;
                    TimerDelayAction[pinToSet] = 'x';
                }
            }
            if (EEPROM.read(pinToSet) == 'v') { // v : pulse down
                digitalWrite(pinToSet, 0);
                check = true;
                int pintime = 1000 * int(DataSerie[4] - '0') + 100 * int(DataSerie[5] - '0') + 10 * int(DataSerie[6] - '0') + int(DataSerie[7] - '0');

                TimerDelays[pinToSet] = millis() + pintime;
                TimerDelayAction[pinToSet] = 'u';
            }
            if (EEPROM.read(pinToSet) == 'u') { // u : pulse up
                digitalWrite(pinToSet, 1);
                check = true;
                int pintime = 1000 * int(DataSerie[4] - '0') + 100 * int(DataSerie[5] - '0') + 10 * int(DataSerie[6] - '0') + int(DataSerie[7] - '0');

                TimerDelays[pinToSet] = millis() + pintime;
                TimerDelayAction[pinToSet] = 'd';
            }
            if (EEPROM.read(pinToSet) == 'b') { // b : blinking
                digitalWrite(pinToSet, 1);
                check = true;
                int pintime = 1000 * int(DataSerie[4] - '0') + 100 * int(DataSerie[5] - '0') + 10 * int(DataSerie[6] - '0') + int(DataSerie[7] - '0');
                if (pintime > 0) {
                    TimerDelays[pinToSet] = millis() + pintime;
                    TimerDelayAction[pinToSet] = 'b';
                    TimerBlink[pinToSet] = pintime;
                } else {
                    digitalWrite(pinToSet,0);
                    TimerDelays[pinToSet] = 0;
                }
            }
            if (EEPROM.read(pinToSet) == 'p') { // pwm
                int pinvalue = 100 * int(DataSerie[4] - '0') + 10 * int(DataSerie[5] - '0') + int(DataSerie[6] - '0');
                analogWrite(pinToSet, pinvalue);
                check = true;
            }
            if (EEPROM.read(pinToSet) == 'd') { // for customs
                for (int i = 0; i < 20; i++) { // Decaler de 3 vers la gauche (suppr. SRT)
                    DataSerie[i] = DataSerie[4 + i];
                    if (DataSerie[i] == '-') {
                        DataSerie[i] = '0';
                        negval = true;
                    }
                }
                check = true;
                if (negval) {
                    CustomValue[pinToSet - CNF_NB_DPIN - CNF_NB_APIN] = 0 - atof(DataSerie);
                    negval = false ;
                } else {
                    CustomValue[pinToSet - CNF_NB_DPIN - CNF_NB_APIN] = atof(DataSerie);
                }
            }
            #if (CNF_RADIO == 1)
                if (EEPROM.read(pinToSet) == 't') { // pin Radio TX
                    if (DataSerie[4] == 'T') { //// Radio Mode TriState
                        for (int i = 0; i < 90; i++) { // Decaler de 3 vers la gauche (suppr. SRT)
                            DataSerie[i] = DataSerie[5 + i];
                        }
                        mySwitch.setProtocol(1);
                        mySwitch.sendTriState(DataSerie);
                        check = true;
                    }
                    if (DataSerie[4] == 'E') { //// Radio Mode TriState EuroDomEst
                        for (int i = 0; i < 90; i++) { // Decaler de 3 vers la gauche (suppr. SRT)
                            DataSerie[i] = DataSerie[5 + i];
                        }
                        mySwitch.setProtocol(1);
                        mySwitch.setPulseLength(900);
                        mySwitch.sendTriState(DataSerie);
                        check = true;
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
                        check = true;
                    }
                }
            #endif
            Serial.print(request);
            #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29)
                client.print(request);
            #endif
            if (check == true) {
                Serial.println(F("_OK"));
                #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29)
                    client.println(F("_OK"));
                #endif
            } else {
                Serial.println(F("_BAD"));
                #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29)
                    client.println(F("_BAD"));
                #endif
            }

        } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF SP

        if (DataSerie[0] == 'C' && DataSerie[1] == 'P') { // *************************************************** CP = Configure Pin mode
            //Serial.print("DBG_Serie:");
            //Serial.println(DataSerie);
            //Serial.print("DBG_CP Len=");
            //Serial.println(LenSerial);
            if (LenSerial >= (1 + CNF_NB_DPIN + CNF_NB_APIN + CNF_NB_CPIN)) {
                #if (CNF_RADIO == 1)
                    mySwitch.disableReceive(); // desactive la radio
                #endif

                #if (CNF_BOARD_MODEL == 22)
                  for (int i = 0; i < CNF_NB_DPIN; i++) {
                #else
                  for (int i = 2; i < CNF_NB_DPIN; i++) {
                #endif
                EEPROM.write(i, DataSerie[2 + i]);    // CPzzrzzzzzzzztoo......
                }
                #if (CNF_BOARD_MODEL > 20)
                EEPROM.commit();
                #endif
                for (int i = 0; i < CNF_NB_APIN; i++) {
                    EEPROM.write(CNF_NB_DPIN + i, DataSerie[2 + CNF_NB_DPIN + i]);    // CP..............aaaaaa
                }
                #if (CNF_BOARD_MODEL > 20)
                EEPROM.commit();
                #endif
                for (int i = 0; i < CNF_NB_CPIN; i++) {
                    EEPROM.write(CNF_NB_DPIN + CNF_NB_APIN + i, DataSerie[2 + CNF_NB_DPIN + CNF_NB_APIN + i]);    // CP......................cccccccc
                }
                #if (CNF_BOARD_MODEL > 20)
                EEPROM.commit();
                #endif
                
                Serial.println(F("CP_OK"));
                #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29)
                    client.println(F("CP_OK"));
                #endif
                delay(500); // laisse le temps a jeedom de valider
                #if (CNF_BOARD_MODEL <= 9)
                    ReadyToSend = true;
                #endif
                ReloadEEPROM();
            } else {
            #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29)
                client.println(F("BAD_L"));
            #endif
                Serial.println(F("BAD_L"));
            }
        } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF CP


        if (DataSerie[0] == 'R' && DataSerie[1] == 'F') { // **************************************************************** RF = ReFresh datas
            ForceRefreshData = true;
        } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF RF

        if (DataSerie[0] == 'R' && DataSerie[1] == 'E') { // **************************************************************** RF = ReFresh datas
            ReloadEEPROM;
        } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF RF


        if (DataSerie[0] == 'T' && DataSerie[1] == 'S') { // **************************************************************** RF = ReFresh datas
            for (byte cntm = 1; cntm < CNF_NB_DPIN + CNF_NB_APIN + CNF_NB_CPIN; cntm++) {
              Serial.print(F("DBG_pinMode_EEP=")); Serial.println(EEPROM.read(cntm));
            }
        } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF TS

        if (DataSerie[0] == 'P' && DataSerie[1] == 'I' && DataSerie[2] == 'N' && DataSerie[3] == 'G') { // ******************************** PING
            Serial.print("PING_OK_V:");
            Serial.println(int(ArduiDomVersion));
            #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29)
                client.print("PING_OK_V:");
                //client.print("PING_OK_V:" + byte(ArduiDomVersion));
                client.println(ArduiDomVersion);
            #endif
        } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF PING

        #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29)
            if (DataSerie[0] == 'A' && DataSerie[1] == 'P') { // ****************************************************************************** AP
                if (LenSerial >= (2 + 20)) {
                    API_LEN = 20;
                    if (LenSerial > (25)) { API_LEN = 32; }
                    if (LenSerial > (36)) { API_LEN = 48; }
                    Serial.print(F("Write API LEN:"));
                    Serial.println(API_LEN);
                    EEPROM.write(400, API_LEN);
                    for (int i = 0; i < API_LEN; i++) {
                        Serial.print(F("Write API @ "));
                        Serial.println(i + 401);
                        EEPROM.write(i + 401, DataSerie[i + 2]); // Stockage de cle API en adresse 200
                    }
                    EEPROM.commit();
                    ReloadEEPROM();
                    Serial.print(DataSerie);
                    Serial.println("_OK");
                    client.print(DataSerie);
                    client.println("_OK");
                    ReadyToSend = true;
                } else {
                    Serial.println("API_BAD");
                    client.println("API_BAD");
                }
            } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF AP

            if (DataSerie[0] == 'I' && DataSerie[1] == 'P') { // ****************************************************************************** IP
                if (LenSerial >= (2 + 12)) {
                    EEPROM.write(230, (100 * DataSerie[2] + 10 * DataSerie[3] + DataSerie[4])); // Stockage de IP Jeedom en adresse 230
                    EEPROM.write(231, (100 * DataSerie[5] + 10 * DataSerie[6] + DataSerie[7])); // Stockage de IP Jeedom en adresse 230
                    EEPROM.write(232, (100 * DataSerie[8] + 10 * DataSerie[9] + DataSerie[10])); // Stockage de IP Jeedom en adresse 230
                    EEPROM.write(233, (100 * DataSerie[11] + 10 * DataSerie[12] + DataSerie[13])); // Stockage de IP Jeedom en adresse 230
                    EEPROM.commit();
                    ReloadEEPROM();
                    Serial.print(DataSerie);
                    Serial.println("_OK");
                    client.print(DataSerie);
                    client.println("_OK");
                } else {
                    Serial.println("IP_BAD");
                    client.println("IP_BAD");
                }
            } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF IP
        #endif

        //serialHook();  // @@RC allow custom code inside the serial management

        LenSerial = 0;
        SerialDataOK = false;

        #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29)
            client.stop();
            Serial.println(F("/Netw"));
        #endif

    } ///////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF IF SerialDataOK

    for (int i=0; i <= CNF_NB_DPIN; i++){
        if (TimerDelays[i] != 0) {
            if (millis() > TimerDelays[i]) {
                switch (TimerDelayAction[i]) {
                    case 'u':
                        digitalWrite(i,1);
                        TimerDelays[i] = 0;
                        break;
                    case 'd':
                        digitalWrite(i,0);
                        TimerDelays[i] = 0;
                        break;
                    case 'x':
                        digitalWrite(i,1 - digitalRead(i));
                        TimerDelays[i] = 0;
                        break;
                    case 'b':
                        digitalWrite(i,1 - digitalRead(i));
                        TimerDelays[i] = millis() + TimerBlink[i];
                        break;
                }
            }
        }
    }

    //////////////////////////////////////////////// PROCEDURE DE DETECTION DES CHANGEMENTS: ////////////////////////////////////////////////////////////////////////

    if (ReadyToSend == false) {
        if ((millis() - TimerReadyToSend) > 30000) { // Démarrage auto du ReadyToSend sur les shield ethernet apres 30s si clé api fournie
            //ForceRefreshData = true;
            #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29)
                if (CNF_API[1] != '.' && CNF_API[1] != '\0' && CNF_API[3] != '.' && CNF_API[3] != '\0') {
                    ReadyToSend = true;
                }
            #else
                ReadyToSend = true;
            #endif
            TimerReadyToSend = millis();
        }
    }
    data = "";

    if (ReadyToSend) { // Attend que le 1er SP Soit OK
        for (int i = 0; i < CNF_NB_DPIN; i++) { // ****************************** Detection des changements de valeurs sur pins DIGITALES
            check = EEPROM.read(i);
            if (check == 'i' || check == 'y' || check == 'o' || check == 'e' || check == 'h' || check == 'x' || check == 'u' || check == 'v') {
                if (check != 'e') {
                  NewValue = digitalRead(i);
                } else {
                  NewValue = 1 - digitalRead(i);
                }
                if (OldValue[i] != NewValue || ForceRefreshData) {
                    if (millis() - LastSend[i] > CNF_DELAY_D_SENDS) { // pas d'envoi de valeur si moins de xxx ms avant la precedente
                        LastSend[i] = millis();
                        OldValue[i] = NewValue;
                        #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29)
                            data = data + i;
                            data = data + "=";
                            data = data + OldValue[i];
                            data = data + "&";
                        #else
                            Serial.print(i);
                            Serial.print(">>");
                            Serial.print(OldValue[i]);
                            Serial.println("<<");
                        #endif
                    }
                }
            }

            if (check == 'j') {
                NewValue = 1 - digitalRead(i);
                if (OldValue[i] != NewValue || ForceRefreshData) {
                    if (millis() - LastSend[i] > CNF_DELAY_D_SENDS) { // pas d'envoi de valeur si moins de xxx ms avant la precedente
                        LastSend[i] = millis();
                        OldValue[i] = NewValue;
                        #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29)
                            data = data + i;
                            data = data + "=";
                            data = data + OldValue[i];
                            data = data + "&";
                        #else
                            Serial.print(i);
                            Serial.print(">>");
                            Serial.print(OldValue[i]);
                            Serial.println("<<");
                        #endif
                    }
                }
            }
        }
        for (int i = 0; i < CNF_NB_APIN; i++) { // ***************************** Detection des changements de valeurs sur pins ANALOGIQUES
            check = EEPROM.read(CNF_NB_DPIN + i);
            if (check == 'a' || check == 'o' || check == 'e') {
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
                    if (ACompare > CNF_APINS_DELTA) {
                        aChange = 1;
                    }
                }

                if (aChange == 1 || ForceRefreshData) {
                    if (NewAValue != OldAValue[i]) {
                        if (millis() - LastSend[i] >
                            CNF_DELAY_A_SENDS) { // pas d'envoi de valeur si moins de xxx ms avant la precedente
                            LastSend[i] = millis();
                            #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29)
                                data = data + (CNF_NB_DPIN + i);
                                data = data + "=";
                                data = data + NewAValue;
                                data = data + "&";
                            #else
                                Serial.print(CNF_NB_DPIN + i);
                                Serial.print(">>");
                                Serial.print(NewAValue);
                                Serial.println("<<");
                            #endif
                            OldAValue[i] = NewAValue;
                        }
                    }
                }
            }
        }
        for (int i = 0; i < CNF_NB_CPIN; i++) { // ***************************** Detection des changements de valeurs sur pins CUSTOMISEES
            if (EEPROM.read(CNF_NB_DPIN + CNF_NB_APIN + i) == 'c') {
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
                    if (CCompare > CNF_CPINS_DELTA) {
                        cChange = 1;
                    }
                }
                if (cChange == 1 || ForceRefreshData) {
                    if (NewCValue != OldCValue[i] || ForceRefreshData) {
                        if (millis() - LastSend[i] >
                            CNF_DELAY_A_SENDS) { // pas d'envoi de valeur si moins de xxx ms avant la precedente
                            LastSend[i] = millis();
                            #if (CNF_BOARD_MODEL > 10 && CNF_BOARD_MODEL <= 29)
                                data = data + (CNF_NB_DPIN + CNF_NB_APIN + i);
                                data = data + "=";
                                data = data + NewCValue;
                                data = data + "&";
                            #else
                                Serial.print(CNF_NB_DPIN + CNF_NB_APIN + i);
                                Serial.print(">>");
                                Serial.print(NewCValue);
                                Serial.println("<<");
                            #endif
                            OldCValue[i] = NewCValue;
                        }
                    }
                }
            }
        }
        if (data != "") JeeSendData();
        ForceRefreshData = false;


        //Serial.print("[2]");

        //     __        __     __      __   ___  __   ___  __  ___    __
        //    |__)  /\  |  \ | /  \    |__) |__  /  ` |__  |__)  |  | /  \ |\ |
        //    |  \ /~~\ |__/ | \__/    |  \ |___ \__, |___ |     |  | \__/ | \|
        //

        #if (CNF_RADIO == 1)
            #if defined(FOR_BOBOX59_ONLY)
                if (digitalRead(7) == 0) RAZRadio = false;
                if (mySwitch.available() || digitalRead(7) == 1) {
            #else
                if (mySwitch.available()) {
            #endif
                RFData = mySwitch.getReceivedValue();
                RFAddr = mySwitch.getReceivedAddr();
                RFProtocol = mySwitch.getReceivedProtocol();
                RFLenght = mySwitch.getReceivedBitlength();
                RFDelay = mySwitch.getReceivedDelay();
                //unsigned int* raw = mySwitch.getReceivedRawdata();

                if ((RFDataLastSend != RFData || RFAddrLastSend != RFAddr || RFProtocolLastSend != RFProtocol)
                     && rfReceptionHook()) { // @@RC allow custom code inside the RF reception management
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

                    JeeSendData();

                    RFDataLastSend = RFData;
                    RFAddrLastSend = RFAddr;
                    RFProtocolLastSend = RFProtocol;
                    LastRadioMessage = millis();

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


            if (millis() - LastRadioMessage > 500 && RAZRadio == false) { // envoie une mise a 0 du radio apres 500 millisecondes
                RFDataLastSend = 0;
                RFAddrLastSend = 0;
                RFProtocolLastSend = 0;
            }
        #endif

        //Serial.print("[3]");

        #if (CNF_DHT == 1)
            if (DHT_QTY > 0) { // ************************************************************************************* GESTION DES SONDES DHT
                if ((millis() - TimerUpdateDHT) > DHT_DELAY) {
                    for (int i = 0; i <= 16; i++) {
                        DHTValue[i] = 999;
                    }
                    for (int j = 1; j <= DHT_QTY; j++) {
                        dhtlib.setup(dhtpin[j]);
                        #if (CNF_DHT11_COMPATIBILITY == 1)
                            delay(dhtlib.getMinimumSamplingPeriod());
                        #endif
                        //Serial.print("STA=");
                        //Serial.println(dhtlib.getStatusString());
                        //Serial.print("MOD=");
                        //Serial.println(dhtlib.getModel());
                        DHTValue[((j * 2) - 1)] = (dhtlib.getHumidity());
                        DHTValue[(j * 2)] = (dhtlib.getTemperature());
                        if (dhtlib.getStatusString() != "OK") {
                            DHTValue[((j * 2) - 1)] = 999;
                            DHTValue[(j * 2)] = 999;
                        }
                    }
                    Serial.print("DHT:");
                    data = "";
                    for (int i = 1; i <= 16; i++) {
                        data += 500 + i;
                        if (DHTValue[i] != 999) {
                            Serial.print(DHTValue[i]);
                            data += "=";
                            data += DHTValue[i];
                        } else {
                            Serial.print("na");
                            data += "=na";
                        }
                        //if (i < 16) {
                        Serial.print(";");
                        data += "&";
                        //}
                    }
                    Serial.print('\n');
                    Serial.println(data);
                    JeeSendData();
                    TimerUpdateDHT = millis();
                }
            }
        #endif


        //Serial.print("[4]");

        /////////   CUSTOMS  //////////////////
        if ((millis() - TimerCustomHook) > CUSTOM_DELAY) { //Si rien non actualisé depuis 30 Secondes
            //@@RC CUSTOM // La partie Customs est désormais dans le bas du sketch !
            //customHook();
            TimerCustomHook = millis();
        }

    } // End if (ReadyToSend)

if (Serial.available() > 0) serialEvent();
  
} // EOF void loop()




//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// fin code générique de Bobox59  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// début du custom ...            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//
// Add your custom code here, method call inside the setup
//
// Votre partie "setup" perso ici (ne s'executera qu'une fois au demarrage de l'arduino)
//

void setupHook() {



}
//
// Add your custom code here, Method call inside the main loop to manage custom values
//
// PLACER CI DESSOUS VOS COMMANDES PERSO POUR LES CUSTOMS (Executé toutes les 30 Secondes par défaut.
//

void customHook () {
    // PARTIE LOOP : CustomValue[0 - 15] sont compatibles en negatifs ansi qu'en virgules ex: -12.4 ------ exemple : CustomValue[0] = CustomValue[1] + 1
    // exemple : CustomValue[0] = CustomValue[1] + 1
    
    //CustomValue[0] = CustomValue[2];
    //CustomValue[1] = CustomValue[2] - 12;


}

//
// Add your custom code here, Method call when serial data received.
//
// Cette méthode est appelée a chaque reception sur port USB et/ou Ethernet
//

void serialHook() {



}

//
// Add your custom code here, Method call inside the RF reception
// return true if the normal process can be called
//

bool rfReceptionHook() {
    bool ret = true;





    return ret;
}
