#include <Arduino.h>

// Arduino Sketch par Benoit Masquelier     masquelierb@gmail.com
// Merci également aux contributions de Alois et Chevalir et des participants du forum Jeedom.
// Utilisable pour le plugin Arduidom sur Jeedom
//
// ATTENTION : Il est IMPERATIF d'utiliser l'IDE 1.6 ou + pour que tout soit compatible !!!
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
//#define FOR_BOBOX59_ONLY    // LIGNE A RETIRER POUR DESACTIVER DES FONCTIONS QUI ME SONT PERSONELLES $$
//
//
//#define DBG_PRINT_CP // Affiche l'etat des pins dans le port série pour debug
//#define DBG_PRINT_SERIAL // Affiche les entrées/sorties sur le port network via serie pour debug
//---------------------------------------------------------------------------------------------------------------------------------------------------
//
//---------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------- CONFIGURATIONS DU RESEAU ARDUINO --------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------------------
//
// INFO: La cle API de JeeDom est automatiquement envoyee a l'arduino via la Configuration des Pins puis stockee dans l'EEPROM
//
#define CNF_NETWORK 0 // Mettre à 1 pour Activation du Shield Ethernet sur Arduino ( consomme 35% Firmware / 12% RAM sur un UNO)
//
#define CNF_DHT 1 // Mettre à 0 pour desactiver les DHT pour gagner en espace Programme/Ram surtout sur les petits arduino ( 4,9% Firmware / 6,3% RAM sur un UNO)
//
#define CNF_RADIO 1 // Mettre à 0 pour desactiver la RADIO pour gagner en espace Programme/Ram surtout sur les petits arduino ( consomme 18,8% Firmware / 23,9% RAM sur un UNO )
//
#if (CNF_NETWORK == 1)
    #include <SPI.h>
    #include <Ethernet.h>
    IPAddress CNF_IP_ARDUIN (192, 168, 10, 191); // ADRESSE IP DE L'ARDUINO, A ADAPTER A VOTRE RESEAU
    //
    IPAddress CNF_IP_JEEDOM (192, 168, 10, 43); // ADRESSE IP JeeDom
    //
    #define CNF_PORT_JEEDOM 80 // Port d'ecoute Jeedom
    //
    //#define CNF_AID 1 // Désormais détecté automatiquement par jeedom ! //Numéro de l'arduino dans jeedom
    //
    #define CNF_JEEDOM_BOX 0 // Mettre a 1 si c'est une jeedom Box ( pas de /jeedom dans le http pour les Jeedom BOX)
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
#if (CNF_NETWORK == 1)
    //--------------------------------------------------------------------------------------------------------------------------------------------------
    //----------------------- CONFIGURATION DE L'ADRESSE MAC ARDUINO -----------------------------------------------------------------------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------------
    byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED }; // ADRESSE MAC DE L'ARDUINO, A CHANGER DANS LE CAS DE PLUSIEURS ARDUINO DANS LE MEME RESEAU
    //--------------------------------------------------------------------------------------------------------------------------------------------------
    EthernetServer server(58174);
#endif
#if (CNF_DHT == 1)
    #include "DHT.h"
#endif
#define ArduiDomVersion 107 // Sers a verfifier le contenu de l'EEPROM
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
#endif
// Variables utilisees pour RCSwitch
#if (CNF_RADIO == 1)
    unsigned long RFData = 0; // Valeur du Data Recu par 433
    unsigned long oldRFData = 0; // Valeur du Data Recu par 433
    unsigned long RFAddr = 0; // Valeur de l'addresse de l'emmeteur 433
    unsigned long oldRFAddr = 0; // Valeur de l'addresse de l'emmeteur 433
    byte RFProtocol = 0; // Valeur du Protocole du message 433
    byte oldRFProtocol = 0; // Valeur du Protocole du message 433
    unsigned int RFDelay = 0; // Valeur du Protocole du message 433
    unsigned int RFLenght = 0; // Valeur du Protocole du message 433
    unsigned long ChaconSender;
    int ChaconRecevr = 0;
    unsigned long LastRadioMessage = 0; // Millis du dernier message radio
#endif
// Variables pour SERIE
boolean SerialDataOK = false;
boolean ForceRefreshData = false; // Mise a 1 toute les 30 secondes pour envoi des données
char DataSerie[129]; // a string to hold incoming data
char l[3]; // a string to hold incoming data
byte LenSerial = 0;
// Variables pour le comparateur
byte OldValue[CNF_NB_DPIN]; // anciennes valeurs pour detection changements
unsigned int OldAValue[CNF_NB_APIN]; // anciennes valeurs pour detection changements
float OldCValue[CNF_NB_CPIN]; // anciennes valeurs pour customs
float CustomValue[CNF_NB_CPIN]; // valeurs en cours pour customs
unsigned long LastSend[CNF_NB_DPIN + CNF_NB_APIN + CNF_NB_CPIN]; // anciennes valeurs pour detection changements
byte NewValue = 0; // tampon nouvelle valeurs pour detection changements
unsigned int NewAValue = 0; // tampon nouvelle valeurs pour detection changementsbyte LenSerial;
float NewCValue = 0;
unsigned int ACompare = 0;
float CCompare = 0;
// Variables systeme
unsigned long TimerReadyToSend = 0;
unsigned long TimerCustomHook = 0; //tempo d'actualisation
String data;
String CNF_API = "....................";    // Creation de la variable vide, jeedom l'enverra pour la stocker dans l'eeprom, NE PAS LA CHANGER ICI !
//IPAddress CNF_IP_JEEDOM(0,0,0,0); // Creation de la variable VIDE, jeedom l'enverra pour la stocker dans l'eeprom, NE PAS LA CHANGER ICI !
char pinmode[CNF_NB_DPIN + CNF_NB_APIN + CNF_NB_CPIN]; // Pin Modes
byte RadioRXpin = 0;
byte RadioTXPin = 0;
byte pinToSet = 0;
boolean RAZRadio = true;
boolean ReadyToSend = false; // Arduino init complet
boolean negval = false; //Valeur negative pour Customs
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

void setup() {

    Serial.begin(115200); // Init du Port serie/USB
    Serial.setTimeout(5); // Timeout 5ms
    if (EEPROM.read(1) != ArduiDomVersion) {
        InitEEPROM();
    }
    #if (CNF_NETWORK == 1)
        Ethernet.begin(mac, CNF_IP_ARDUIN);
        server.begin();
        ReloadEEPROM();
    #endif

    TimerReadyToSend = millis();
    TimerUpdateDHT = millis();
    TimerCustomHook = millis();
    //--------------------------------------------------------------------------------------------------------------------------------------------------
    // PARTIE SETUP // DEPUIS LA V2, VOIR LE BAS DU SKETCH POUR VOS INTEGRATIONS
    /**
    ** @@RC SETUP
    **/
    setupHook(); // DEPUIS LA V2, VOIR LE BAS DU SKETCH POUR VOS INTEGRATIONS
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
    #if (CNF_NETWORK == 1)
        EthernetClient client = server.available();
        if (client) {
            #if defined(DBG_PRINT_SERIAL)
                Serial.println(F("Net in:"));
            #endif
            while (client.connected()) {
                if (client.available()) {
                    char c = client.read();
                    #if defined(DBG_PRINT_SERIAL)
                        Serial.print("(");
                    #endif
                    DataSerie[LenSerial] = c;
                    if (DataSerie[LenSerial] == '\n') {
                        DataSerie[LenSerial] = char(0);
                        SerialDataOK = true;
                    }
                    LenSerial += 1;
                    #if defined(DBG_PRINT_SERIAL)
                        Serial.write(c);
                        Serial.print(")");
                    #endif
                    if (c == '\n') {
                        #if defined(DBG_PRINT_SERIAL)
                            Serial.println("");
                            Serial.println(F("</n>"));
                        #endif
                        break;
                    }
                }
            }
    #else
        if (true) { //    remplace le if(client) {   pour les shields networks
    #endif
        if (SerialDataOK) { // Donnees dans le buffer Serie
            #if defined(DBG_PRINT_SERIAL)
                Serial.print(F("DBG_todo:")); Serial.println(DataSerie);
            #endif
            if (DataSerie[0] == 'S' && DataSerie[1] == 'P') { // ************************************************************ SP = Set Pin
                Serial.write("SP_");
                #if (CNF_NETWORK == 1)
                    //client.print("SP_");
                #endif
                pinToSet = 10 * int(DataSerie[2] - '0'); // dizaines
                pinToSet += int(DataSerie[3] - '0'); // unites
                Serial.print(pinToSet);
                #if (CNF_NETWORK == 1)
                    //client.print(pinToSet);
                #endif
                if (pinmode[pinToSet] == 'o' || pinmode[pinToSet] == 'i' || pinmode[pinToSet] == 'y') { // also on mode i for pull up of inputs
                    Serial.print("=");
                    #if (CNF_NETWORK == 1)
                        //client.print("=");
                    #endif
                    if (DataSerie[4] == '0') {
                        digitalWrite(pinToSet, LOW);
                        Serial.println("L");
                        #if (CNF_NETWORK == 1)
                            //client.println("L");
                        #endif
                    }
                    if (DataSerie[4] == '1') {
                        digitalWrite(pinToSet, HIGH);
                        Serial.println("H");
                        #if (CNF_NETWORK == 1)
                            //client.println("H");
                        #endif
                    }
                }
                if (pinmode[pinToSet] == 'p') {
                    Serial.print("=");
                    #if (CNF_NETWORK == 1)
                        //client.print("=");
                    #endif
                    int pinvalue = 100 * int(DataSerie[4] - '0') + 10 * int(DataSerie[5] - '0') + int(DataSerie[6] - '0');
                    analogWrite(pinToSet, pinvalue);
                    Serial.println(pinvalue);
                    #if (CNF_NETWORK == 1)
                        //client.println(pinvalue);
                    #endif
                }
                if (pinmode[pinToSet] == 'd') { // for customs
                    Serial.print("=");
                    #if (CNF_NETWORK == 1)
                        //client.print("=");
                    #endif
                    for (int i = 0; i < 20; i++) { // Decaler de 3 vers la gauche (suppr. SRT)
                        DataSerie[i] = DataSerie[4 + i];
                        if (DataSerie[i] == '-') {
                            DataSerie[i] = '0';
                            negval = true;
                        }
                    }
                    if (negval) {
                        CustomValue[pinToSet - CNF_NB_DPIN - CNF_NB_APIN] = 0 - atof(DataSerie);
                        negval = false ;
                    } else {
                        CustomValue[pinToSet - CNF_NB_DPIN - CNF_NB_APIN] = atof(DataSerie);
                    }
                    Serial.println(CustomValue[pinToSet - CNF_NB_DPIN - CNF_NB_APIN]);
                    #if (CNF_NETWORK == 1)
                        //client.println(CustomValue[pinToSet - CNF_NB_DPIN - CNF_NB_APIN]);
                    #endif
                }
                #if (CNF_RADIO == 1)
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
                #endif
                Serial.print(F("\nSP_OK\n"));
                #if (CNF_NETWORK == 1)
                    client.print("SP_OK\n");
                #endif
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

                    for (int i = 2; i < CNF_NB_DPIN; i++) {
                        EEPROM.write(i, DataSerie[2 + i]);    // CPzzrzzzzzzzztoo......
                    }
                    for (int i = 0; i < CNF_NB_APIN; i++) {
                        EEPROM.write(CNF_NB_DPIN + i, DataSerie[2 + CNF_NB_DPIN + i]);    // CP..............aaaaaa
                    }
                    for (int i = 0; i < CNF_NB_CPIN; i++) {
                        EEPROM.write(CNF_NB_DPIN + CNF_NB_APIN + i, DataSerie[2 + CNF_NB_DPIN + CNF_NB_APIN + i]);    // CP......................cccccccc
                    }

                    ReloadEEPROM();
                    Serial.println("CP_OK");
                    #if (CNF_NETWORK == 1)
                        client.println("CP_OK");
                    #endif
                    delay(500); // laisse le temps a jeedom de valider
                    #if CNF_NETWORK == 1
                        if (CNF_API[1] != '.' && CNF_API[1] != '\0' && CNF_API[3] != '.' && CNF_API[3] != '\0') {
                            ReadyToSend = true;
                        }
                    #else
                        ReadyToSend = true;
                    #endif
                } else {
                #if (CNF_NETWORK == 1)
                    client.print("BAD_L");
                #endif
                    Serial.print("BAD_L");
                }
            } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF CP


            if (DataSerie[0] == 'R' && DataSerie[1] == 'F') { // **************************************************************** RF = ReFresh datas
                ForceRefreshData = true;
            } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF RF

            if (DataSerie[0] == 'P' && DataSerie[1] == 'I' && DataSerie[2] == 'N' && DataSerie[3] == 'G') { // ******************************** PING
                Serial.print("PING_OK_V:");
                Serial.println(int(ArduiDomVersion));
                #if (CNF_NETWORK == 1)
                    client.print("PING_OK_V:");
                    //client.print("PING_OK_V:" + byte(ArduiDomVersion));
                    client.println(ArduiDomVersion);
                #endif
            } ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF PING

            #if (CNF_NETWORK == 1)
                if (DataSerie[0] == 'A' && DataSerie[1] == 'P') { // ****************************************************************************** AP
                    if (LenSerial >= (2 + 20)) {
                        for (int i = 0; i < 20; i++) {
                            EEPROM.write(i + 200, DataSerie[i + 2]); // Stockage de cle API en adresse 200
                        }
                        ReloadEEPROM();
                        Serial.print(DataSerie);
                        Serial.println("_OK");
                        #if (CNF_NETWORK == 1)
                            client.print(DataSerie);
                            client.println("_OK");
                        #endif
                        ReadyToSend = true;
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
                        ReloadEEPROM();
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
            #endif

            serialHook();  // @@RC allow custom code inside the serial management

            LenSerial = 0;
            SerialDataOK = false;

            #if (CNF_NETWORK == 1)
                client.stop();
                Serial.println(F("/Netw"));
            #endif
        } // End of if (SerialDataOK)

    } ///////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF IF LenSerial

    //#endif




    //Serial.print("[1]");
    //////////////////////////////////////////////// PROCEDURE DE DETECTION DES CHANGEMENTS: ////////////////////////////////////////////////////////////////////////

    if ((millis() - TimerReadyToSend) > 30000) {
        //ForceRefreshData = true;
        #if CNF_NETWORK == 1
            if (CNF_API[1] != '.' && CNF_API[1] != '\0' && CNF_API[3] != '.' && CNF_API[3] != '\0') {
                ReadyToSend = true;
            }
        #else
            ReadyToSend = true;
        #endif
        TimerReadyToSend = millis();
    }

    data = "";

    if (ReadyToSend) { // Attend que le 1er SP Soit OK
        for (int i = 0; i <
                        CNF_NB_DPIN; i++) { // ****************************** Detection des changements de valeurs sur pins DIGITALES
            if (pinmode[i] == 'i' || pinmode[i] == 'y' || pinmode[i] == 'o' || pinmode[i] == 'h') {
                NewValue = digitalRead(i);
                if (OldValue[i] != NewValue || ForceRefreshData) {
                    if (millis() - LastSend[i] >
                        CNF_DELAY_D_SENDS) { // pas d'envoi de valeur si moins de xxx ms avant la precedente
                        LastSend[i] = millis();
                        OldValue[i] = NewValue;
                        #if (CNF_NETWORK == 1)
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
        for (int i = 0; i <
                        CNF_NB_APIN; i++) { // ***************************** Detection des changements de valeurs sur pins ANALOGIQUES
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
                    if (ACompare > CNF_APINS_DELTA) {
                        aChange = 1;
                    }
                }

                if (aChange == 1 || ForceRefreshData) {
                    if (NewAValue != OldAValue[i]) {
                        if (millis() - LastSend[i] >
                            CNF_DELAY_A_SENDS) { // pas d'envoi de valeur si moins de xxx ms avant la precedente
                            LastSend[i] = millis();
                            #if (CNF_NETWORK == 1)
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
        for (int i = 0; i <
                        CNF_NB_CPIN; i++) { // ***************************** Detection des changements de valeurs sur pins CUSTOMISEES
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
                    if (CCompare > CNF_CPINS_DELTA) {
                        cChange = 1;
                    }
                }
                if (cChange == 1 || ForceRefreshData) {
                    if (NewCValue != OldCValue[i] || ForceRefreshData) {
                        if (millis() - LastSend[i] >
                            CNF_DELAY_A_SENDS) { // pas d'envoi de valeur si moins de xxx ms avant la precedente
                            LastSend[i] = millis();
                            #if (CNF_NETWORK == 1)
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
        #if (CNF_NETWORK == 1)
            if (data != "") JeeSendData();
        #endif
        ForceRefreshData = false;


        //Serial.print("[2]");

        //     __        __     __      __   ___  __   ___  __  ___    __
        //    |__)  /\  |  \ | /  \    |__) |__  /  ` |__  |__)  |  | /  \ |\ |
        //    |  \ /~~\ |__/ | \__/    |  \ |___ \__, |___ |     |  | \__/ | \|
        //

        #if (CNF_RADIO == 1)
        if (mySwitch.available()) {
            LastRadioMessage = millis();
            RFData = mySwitch.getReceivedValue();
            RFAddr = mySwitch.getReceivedAddr();
            RFProtocol = mySwitch.getReceivedProtocol();
            RFLenght = mySwitch.getReceivedBitlength();
            RFDelay = mySwitch.getReceivedDelay();
            unsigned int* raw = mySwitch.getReceivedRawdata();

            if ((oldRFData != RFData || oldRFAddr != RFAddr || oldRFProtocol != RFProtocol)
                 && rfReceptionHook() ) { // @@RC allow custom code inside the RF reception management
                RAZRadio = false;
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
        #endif

        /*
        if (millis() - LastRadioMessage > 500 && RAZRadio != true) { // envoie une mise a 0 du radio apres 500 millisecondes
            RAZRadio = true;
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

        #if (CNF_DHT == 1)
            if (DHT_QTY > 0) { // ************************************************************************************* GESTION DES SONDES DHT
                if ((millis() - TimerUpdateDHT) > DHT_DELAY) { // Actualisation Automatique des sondes DHT à 45 Secondes
                    #if (CNF_RADIO == 1)
                        mySwitch.disableReceive();
                    #endif
                    for (int i = 0; i <= 16; i++) {
                        DHTValue[i] = 999;
                    }
                    for (int j = 1; j <= DHT_QTY; j++) {
                        //Serial.print(F("DHT"));
                        //Serial.print(j);
                        dhtlib.setup(dhtpin[j]);
                        //Serial.println(dhtlib.getStatusString());
                        DHTValue[((j * 2) - 1)] = (dhtlib.getHumidity());
                        DHTValue[(j * 2)] = (dhtlib.getTemperature());
                        //Serial.println(dhtlib.getStatusString());
                        if (dhtlib.getStatusString() != "OK") {
                            DHTValue[((j * 2) - 1)] = 999;
                            DHTValue[(j * 2)] = 999;
                        }
                    }
                #if (CNF_RADIO == 1)
                    #if defined(__AVR_ATmega168__) || defined(__AVR_ATmega168P__) || defined(__AVR_ATmega328P__) || defined(__AVR_ATmega32U4__)
                        if (RadioRXpin == 2) mySwitch.enableReceive(0);
                        if (RadioRXpin == 3) mySwitch.enableReceive(1);
                    #else
                        if (RadioRXpin == 2) mySwitch.enableReceive(0);
                        if (RadioRXpin == 3) mySwitch.enableReceive(1);
                        if (RadioRXpin == 21) mySwitch.enableReceive(2);
                        if (RadioRXpin == 20) mySwitch.enableReceive(3);
                        if (RadioRXpin == 19) mySwitch.enableReceive(4);
                        if (RadioRXpin == 18) mySwitch.enableReceive(5);
                    #endif
                #endif
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
                #if (CNF_NETWORK == 1)
                    JeeSendData();
                #endif
                    TimerUpdateDHT = millis();
                }
            }
        #endif

    }
    //Serial.print("[4]");

    /////////   CUSTOMS  //////////////////
    if ((millis() - TimerCustomHook) > CUSTOM_DELAY) { //Si rien non actualisé depuis 30 Secondes
        //@@RC CUSTOM // La partie Customs est désormais dans le bas du sketch !
        customHook();
        TimerCustomHook = millis();
    }
} // EOF void loop()







//
//      ______ ____  _   _  _____ _______ _____ ____  _   _  _____
//     |  ____/ __ \| \ | |/ ____|__   __|_   _/ __ \| \ | |/ ____|
//     | |__ | |  | |  \| | |       | |    | || |  | |  \| | (___
//     |  __|| |  | | . ` | |       | |    | || |  | | . ` |\___ \
//     | |   | |__| | |\  | |____   | |   _| || |__| | |\  |____) |
//     |_|    \____/|_| \_|\_____|  |_|  |_____\____/|_| \_|_____/
//

#if (CNF_NETWORK == 1)
    void JeeSendData() {
        Serial.println(F("JeeSend"));
        Serial.println(data);
        if (data != "") {
            Serial.print("[J1]");
            EthernetClient client2 = server.available();
            Serial.print("[J2]");

            int inChar;

            Serial.print(F("cnx.."));
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

            Serial.print("[J4]");

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
            Serial.print("[J5]");

        } // end of if data != ""
    } // End of JeeSendData()
#endif




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
        if (LenSerial > 134) {
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
    for (int i = 2; i < CNF_NB_DPIN + CNF_NB_APIN + CNF_NB_CPIN; i++) {
        pinmode[i] = EEPROM.read(i); // Pin Modes
        #if defined(DBG_PRINT_CP)
            Serial.print(F("Pin "));
            Serial.print(i);
            Serial.print(F(" is "));
        #endif
        #if (CNF_NETWORK == 1)
            #if defined(__AVR_ATmega1280__) || defined(__AVR_ATmega2560__)
                if (i == 10 || i == 50 || i == 51 || i == 52) {
            #else
                if (i == 10 || i == 11 || i == 12 || i == 13) {
            #endif
            #if defined(DBG_PRINT_CP)
                Serial.print(F("NetW"));
            #endif
            pinmode[i] = 'N';
            }
        #endif
        if (pinmode[i] == 'z') {
            #if defined(DBG_PRINT_CP)
                Serial.print(F("DIS"));
            #endif
        }
        if (pinmode[i] == 'i') {
            pinMode(i, INPUT);
            #if defined(DBG_PRINT_CP)
                Serial.print(F("IN"));
            #endif
        }
        if (pinmode[i] == 'y') {
            pinMode(i, INPUT_PULLUP);
            #if defined(DBG_PRINT_CP)
                Serial.print(F("IN_pullup"));
            #endif
        }
        if (pinmode[i] == 'a') {
            pinMode(i, INPUT);
            #if defined(DBG_PRINT_CP)
                Serial.print(F("ANA"));
            #endif
        }
        if (pinmode[i] == 'o') {
            pinMode(i, OUTPUT);
            #if defined(DBG_PRINT_CP)
                Serial.print(F("OUT"));
            #endif
        }
        if (pinmode[i] == 'c') {
            #if defined(DBG_PRINT_CP)
                Serial.print(F("CIN"));
            #endif
        }
        if (pinmode[i] == 'd') {
            #if defined(DBG_PRINT_CP)
                Serial.print(F("COU"));
            #endif
        }
        if (pinmode[i] == 'p') {
            pinMode(i, OUTPUT);
            #if defined(DBG_PRINT_CP)
                Serial.print(F("PWM"));
            #endif
        }
        #if (CNF_RADIO == 1)
            if (pinmode[i] == 'r') {
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
            if (pinmode[i] == 't') {
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
            if (pinmode[i] >= 49 && pinmode[i] <= 57) {
                #if defined(DBG_PRINT_CP)
                    Serial.print(F("DHT"));
                #endif
            }
            if (pinmode[i] == '1') {
                dhtpin[1] = i;
                DHT_QTY += 1;
                #if defined(DBG_PRINT_CP)
                    Serial.print(F("1"));
                #endif
            }
            if (pinmode[i] == '2') {
                dhtpin[2] = i;
                DHT_QTY += 1;
                #if defined(DBG_PRINT_CP)
                    Serial.print(F("2"));
                #endif
            }
            if (pinmode[i] == '3') {
                dhtpin[3] = i;
                DHT_QTY += 1;
                #if defined(DBG_PRINT_CP)
                    Serial.print(F("3"));
                #endif
            }
            if (pinmode[i] == '4') {
                dhtpin[4] = i;
                DHT_QTY += 1;
                #if defined(DBG_PRINT_CP)
                    Serial.print(F("4"));
                #endif
            }
            if (pinmode[i] == '5') {
                dhtpin[5] = i;
                DHT_QTY += 1;
                #if defined(DBG_PRINT_CP)
                    Serial.print(F("5"));
                #endif
            }
            if (pinmode[i] == '6') {
                dhtpin[6] = i;
                DHT_QTY += 1;
                #if defined(DBG_PRINT_CP)
                    Serial.print(F("6"));
                #endif
            }
            if (pinmode[i] == '7') {
                dhtpin[7] = i;
                DHT_QTY += 1;
                #if defined(DBG_PRINT_CP)
                    Serial.print(F("7"));
                #endif
            }
            if (pinmode[i] == '8') {
                dhtpin[8] = i;
                DHT_QTY += 1;
                #if defined(DBG_PRINT_CP)
                    Serial.print(F("8"));
                #endif
            }
        #endif
        #if defined(DBG_PRINT_CP)
            Serial.print("\n");
        #endif
    }
    for (int i = 0; i < 20; i++) {
        CNF_API[i] = EEPROM.read(200 + i); // Cle API Jeedom
    }
}// END OF ReloadEEPROM()

void InitEEPROM() {
    EEPROM.write(1, ArduiDomVersion); // Pin Mode
    for (int i = 2; i < 400; i++) {
        EEPROM.write(i, '\0');
    }
} // END OF InitEEPROM()
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// fin code générique de Bobox59  \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
// début du custom ...            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


/*
 * Add your custom code here, method call inside the setup
 *
 * Votre partie "setup" perso ici (ne s'executera qu'une fois au demarrage de l'arduino)
 */
void setupHook() {



}
/*
 * Add your custom code here, Method call inside the main loop to manage custom values
 *
 * PLACER CI DESSOUS VOS COMMANDES PERSO POUR LES CUSTOMS (Executé toutes les 30 Secondes par défaut.
 */
void customHook () {
    // PARTIE LOOP : CustomValue[0 - 15] sont compatibles en negatifs ansi qu'en virgules ex: -12.4 ------ exemple : CustomValue[0] = CustomValue[1] + 1
    // exemple : CustomValue[0] = CustomValue[1] + 1






}
/*
 * Add your custom code here, Method call when serial data received.
 *
 * Cette méthode est appelée a chaque reception sur port USB et/ou Ethernet
 */
void serialHook() {



}

/*
 * Add your custom code here, Method call inside the RF reception
 * return true if the normal process can be called
 */
bool rfReceptionHook() {
    bool ret = true;





    return ret;
}

