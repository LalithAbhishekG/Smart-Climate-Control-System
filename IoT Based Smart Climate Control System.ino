#include <SoftwareSerial.h>
#include <LiquidCrystal_I2C.h>
#include "DHT.h"

//PIN Configurations
#define DHTTYPE DHT11
#define COLS 16
#define ROWS 2
#define DHTPIN 12
#define RX 2
#define TX 3
#define RELAY 5

//Device Configurations
DHT dht(DHTPIN, DHTTYPE);
SoftwareSerial esp8266(RX,TX); 
LiquidCrystal_I2C lcd(0x27, COLS, ROWS);

//Topic of Project as String for displaying in LCD
String TOPIC = "IoT Based Smart Climate Control System";

//Network Configurations
String AP = "";//Network Name
String PASS = ""; // Network PASSWORD
String HOST = ""; // Server IP
String PORT = "80"; //Server Port

//Temperature and Humidity readings
float t,h;
String t_string,h_string;

//Keep track of count of AT commands
int countTrueCommand;
int countTimeCommand; 
boolean found = false;

//Keep track if Relay is On/Off
bool relay_is_on = false;

void setup() {
  //Initialize LCD Display
  lcd.init();                    
  lcd.backlight();

  //Logic to display the Topic
  for (int i=0; i < COLS; i++) {
    TOPIC = " " + TOPIC;  
  } 
  TOPIC = TOPIC + " ";
  for (int position = 0; position < TOPIC.length(); position++) {
    lcd.setCursor(0, 0);
    lcd.print(TOPIC.substring(position, position + COLS));
    delay(250);
  }
  delay(1000);

  //Initialize Serial communiction
  Serial.begin(9600);
  delay(1000);

  //Initialize DHT Sensor
  dht.begin();
  pinMode(RELAY,OUTPUT);
  digitalWrite(RELAY,LOW);

  //Initialize Wifi(ESP8266-01) module
  esp8266.begin(115200);
  delay(2000);

  //Establish Connection with ESP8266-01
  sendCommand("AT",5,"OK");

  //Set the communcation mode as 1
  sendCommand("AT+CWMODE=1",5,"OK");
}
void loop() {

  //Read Temperature and Humidity
  t = dht.readTemperature();
  h = dht.readHumidity();
  t_string = String(t);
  h_string = String(h);
  delay(2000);

  //Display the readings in terminal
  Serial.println("Temperature= "+t_string);
  Serial.println("Humidity= "+h_string);
  delay(1500);

  //Display the readings in LCD
  lcd.setCursor(0,0);
  lcd.print("Temp="+t_string+"C");
  lcd.setCursor(0,1);
  lcd.print("Humidity="+h_string+"%");

  // Logic to turn On HIGHER Temperature
  if (t > 20 && !relay_is_on && h < = 85) {
      relay_is_on = true;
      digitalWrite(RELAY, HIGH);
      delay(4000);
  }
  // Logic to turn off with HIGHER Humidity 
  if (h > 85 && relay_is_on && t < 20) {
      relay_is_on = false;
      Serial.println("High Humidity");
      digitalWrite(RELAY, LOW);
      delay(4000);
  }

  //Commands to communicate through ESP8266 to Server

  //Command to connect to the given Wifi network
  sendCommand("AT+CWJAP=\""+ AP +"\",\""+ PASS +"\"",20,"OK");  

  //Defining the URL for GET method
  String getData = "GET /ClimateControl/receiver.php?Temp="+t_string+"&Humidity="+h_string+"\r\n Host:"+HOST+":"+PORT+"\r\n\r\n";

  //Set the role of device as only sneder
  sendCommand("AT+CIPMUX=1",5,"OK");

  //Establish a TCP Connection to the given HOST
  sendCommand("AT+CIPSTART=0,\"TCP\",\""+ HOST +"\","+ PORT,15,"OK");
  delay(5000);

  //Define the size of the data to be sent
  sendCommand("AT+CIPSEND=0," +String(getData.length()+4),10,">");
  delay(2000);

  //Send the data in the GET request
  esp8266.println(getData);
  delay(5000);
  countTrueCommand++;

  //Close the established connection
  sendCommand("AT+CIPCLOSE=0",20,"OK");
  delay(2000);

  //Cler the LCD screen
  lcd.clear();
}

//Function to send required command and to check for correct Replay

//The command to be sent(string), maximum time to wait for reply(int[milliseconds] and the expected reply(char array) are the parameters required to call the function)
void sendCommand(String command, int maxTime, char readReplay[]){

  Serial.print(countTrueCommand);
  Serial.print(". at command => ");
  Serial.print(command);
  Serial.print(" ");

  //Logic to declare failure if expected reply is not received within the given time
  while(countTimeCommand < (maxTime*1))
  {
    esp8266.println(command);
    //checks if expected reply characters is part of actual reply or not
    if(esp8266.find(readReplay))
    {
      found = true;
      break;
    }
    countTimeCommand++;
  }

  //if reply is found declare success and print in terminal
  if(found == true)
  {
    Serial.println("Success");
    countTrueCommand++;
    countTimeCommand = 0;
  }

  //if reply is not found declare failure and print in terminal
  if(found == false)
  {
    Serial.println("Fail");
    countTrueCommand = 0;
    countTimeCommand = 0;
  }
  found = false;
  delay(2000);
}
