#include <ESP8266WiFi.h>
#include <SD.h>
#include <SPI.h>
#include <SoftwareSerial.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BMP280.h>
#include <MySQL_Connection.h>
#include <MySQL_Cursor.h>

// Wi-Fi credentials
const char* ssid = "smartis";
const char* password = "smart12345";

// MySQL credentials (for future DB connection if needed)
IPAddress server_ip(192, 168, 1, 100);  // IP address of your MySQL server
const char* db_user = "root";  // Database username
const char* db_pass = "password";  // Database password
const char* db_name = "smart_home";  // Database name

// SIM800 Module UART
SoftwareSerial sim800Serial(12, 13);  // RX, TX pins for SIM800
const long baudRate = 9600;

// BMP280 Sensor
Adafruit_BMP280 bmp; // I2C interface for BMP280

// SD Card
File dataFile;
const int chipSelect = D8; // SD card chip select pin

// Input Pins for switches (connected to windows and door)
const int windowSwitch1 = D2; // Pin for window 1
const int windowSwitch2 = D3; // Pin for window 2
const int doorSwitch = D4;    // Pin for door
const int systemStatePin = D5; // Pin for system state (you can add more if needed)

// Pin states for controlling devices (8 outputs for controlling things like lamps)
bool lampStates[8] = {false, false, false, false, false, false, false, false};
bool doorStates[2] = {false, false};
bool windowStates[2] = {false, false};
bool acOn = false;
int acTemp = 22;
bool alarmMode = false;

// Sensor variables (BMP280 data)
float temperature = 0.0;
float humidity = 0.0;
float pressure = 0.0;

// MySQL Connection
WiFiClient client;
MySQL_Connection conn((Client *)&client);

// Function to read BMP280 sensor
void readBMP280() {
  temperature = bmp.readTemperature();
  humidity = 50.0;  // Dummy data, BMP280 doesn't measure humidity directly
  pressure = bmp.readPressure() / 100.0; // Pressure in hPa
}

// Function to initialize SD card
void setupSDCard() {
  if (!SD.begin(chipSelect)) {
    Serial.println("SD card initialization failed!");
    while (true);
  }
  Serial.println("SD card initialized.");

  // Check if the log file exists, if not, create it and add headers
  if (!SD.exists("log.csv")) {
    dataFile = SD.open("log.csv", FILE_WRITE);
    if (dataFile) {
      dataFile.println("Timestamp, Lamp1, Lamp2, Lamp3, Lamp4, Lamp5, Lamp6, Lamp7, Lamp8, Door1, Door2, Window1, Window2, AC, AC Temp, Temperature, Humidity, Pressure, System State, Alarm Mode");
      dataFile.close();
    }
  }
}

// Function to log sensor data to SD card (CSV format)
void logSensorData() {
  // Open file in append mode
  dataFile = SD.open("log.csv", FILE_WRITE);
  
  if (dataFile) {
    unsigned long timestamp = millis() / 1000; // Timestamp in seconds
    
    // Log all pin states and sensor data in CSV format
    dataFile.print(timestamp);
    dataFile.print(", ");
    for (int i = 0; i < 8; i++) dataFile.print(lampStates[i]);
    dataFile.print(", ");
    dataFile.print(doorStates[0]);
    dataFile.print(", ");
    dataFile.print(doorStates[1]);
    dataFile.print(", ");
    dataFile.print(windowStates[0]);
    dataFile.print(", ");
    dataFile.print(windowStates[1]);
    dataFile.print(", ");
    dataFile.print(acOn);
    dataFile.print(", ");
    dataFile.print(acTemp);
    dataFile.print(", ");
    dataFile.print(temperature);
    dataFile.print(", ");
    dataFile.print(humidity);
    dataFile.print(", ");
    dataFile.print(pressure);
    dataFile.print(", ");
    dataFile.print("Idle");
    dataFile.print(", ");
    dataFile.println(alarmMode);
    
    dataFile.close(); // Close the file after writing
  } else {
    Serial.println("Error opening log file for writing.");
  }
}

// Function to check for incoming SMS and process instructions
void checkSMS() {
  String smsData = "";
  if (sim800Serial.available()) {
    smsData = sim800Serial.readString();
    Serial.println("Received SMS: " + smsData);
    
    // Example logic to extract phone number and instructions
    if (smsData.indexOf("+CMTI") != -1) {
      String phoneNumber = extractPhoneNumber(smsData);
      String instruction = extractInstruction(smsData);
      
      if (isValidPhoneNumber(phoneNumber)) {
        executeInstruction(instruction);
      }
    }
  }
}

// Helper to extract phone number from SMS (simplified)
String extractPhoneNumber(String smsData) {
  // Just a placeholder function
  return "+1234567890"; // Example phone number
}

// Helper to extract instruction from SMS
String extractInstruction(String smsData) {
  // For simplicity, assume the instruction is just a string after a specific prefix
  return "TURN_ON_LAMP_1"; // Example instruction (this can be extended to other commands)
}

// Check if the phone number is valid
bool isValidPhoneNumber(String phoneNumber) {
  // Check if the number is valid (you can add your validation here)
  return true;
}

// Execute the received instruction (e.g., control lamps, AC, etc.)
void executeInstruction(String instruction) {
  if (instruction == "TURN_ON_LAMP_1") {
    lampStates[0] = true;
    logSensorData(); // Log the change
  } 
  // Add more instructions as needed, such as:
  // TURN_OFF_LAMP_1, SET_AC_TEMP_24, TOGGLE_ALARM, etc.
}

// Function to handle switch states and log changes
void handleSwitches() {
  static bool lastWindowState1 = false;
  static bool lastWindowState2 = false;
  static bool lastDoorState = false;
  
  bool currentWindowState1 = digitalRead(windowSwitch1);
  bool currentWindowState2 = digitalRead(windowSwitch2);
  bool currentDoorState = digitalRead(doorSwitch);
  
  // Detect state changes and log them
  if (currentWindowState1 != lastWindowState1 || currentWindowState2 != lastWindowState2 || currentDoorState != lastDoorState) {
    logSensorData(); // Log pin states and timestamp
    lastWindowState1 = currentWindowState1;
    lastWindowState2 = currentWindowState2;
    lastDoorState = currentDoorState;
  }
}

// Setup the system
void setup() {
  Serial.begin(115200);
  
  // Setup Wi-Fi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi!");
  
  // Initialize BMP280 sensor
  if (!bmp.begin()) {
    Serial.println("Could not find a valid BMP280 sensor, check wiring!");
    while (1);
  }

  // Setup SD card
  setupSDCard();
  
  // Setup switch pins
  pinMode(windowSwitch1, INPUT);
  pinMode(windowSwitch2, INPUT);
  pinMode(doorSwitch, INPUT);
  pinMode(systemStatePin, INPUT);

  // Read BMP280 data every minute and log
  readBMP280();
  logSensorData(); // Log initial data

  // Setup SIM800 serial communication
  sim800Serial.begin(baudRate);
}

void loop() {
  // Periodically read and log data
  static unsigned long lastLogTime = 0;
  if (millis() - lastLogTime >= 60000) { // Every 60 seconds
    readBMP280();
    logSensorData();
    lastLogTime = millis();
  }

  // Monitor switches and log changes
  handleSwitches();
  
  // Handle SMS communication via SIM800 (if required)
  checkSMS();
}
