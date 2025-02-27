#include <SoftwareSerial.h>
#include <ESP8266WiFi.h>
#include <Wire.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BMP280.h>

#define RX_PIN 13
#define TX_PIN 15

// SIM800 Serial communication setup
SoftwareSerial sim800(RX_PIN, TX_PIN);
Adafruit_BMP280 bmp;  // Create an object for the BMP280 sensor

// Wi-Fi credentials
const char* ssid = "smartis";
const char* password = "smart12345";

// Server URL to send data to
const String serverURL = "http://smartis.koalium.ir/smarthandler.php";

// Unique device ID for authentication
const String deviceID = "Device1234"; // Change this to a unique ID for your device

// Function to initialize SIM800
void setupSIM800() {
  sim800.begin(9600);
  delay(1000);

  // Check if SIM800 is ready
  sim800.println("AT");
  delay(1000);
  if (sim800.available()) {
    String response = sim800.readString();
    Serial.println(response);
  }

  // Set GPRS to work in HTTP mode
  sim800.println("AT+CSQ");  // Check signal quality
  delay(1000);

  sim800.println("AT+CGATT=1");  // Attach to GPRS
  delay(2000);

  sim800.println("AT+SAPBR=3,1,\"CONTYPE\",\"GPRS\""); // Configure GPRS
  delay(1000);
  sim800.println("AT+SAPBR=3,1,\"APN\",\"internet\""); // Set APN, depending on your carrier
  delay(1000);
  sim800.println("AT+SAPBR=1,1");  // Activate the bearer
  delay(2000);
}

// Function to send HTTP POST request with sensor data
void sendDataToServer(String sensorData) {
  String postData = "deviceID=" + deviceID + "&data=" + sensorData;

  sim800.println("AT+HTTPINIT");
  delay(1000);
  sim800.println("AT+HTTPPARA=\"URL\",\"" + serverURL + "\"");
  delay(1000);

  sim800.println("AT+HTTPPARA=\"CONTENT\",\"application/x-www-form-urlencoded\"");
  delay(1000);

  sim800.println("AT+HTTPDATA=" + String(postData.length()) + ",10000");
  delay(1000);
  sim800.println(postData);
  delay(1000);

  sim800.println("AT+HTTPACTION=1");  // Send POST request
  delay(5000);

  String response = sim800.readString();
  Serial.println(response);

  sim800.println("AT+HTTPTERM");  // Terminate HTTP session
  delay(1000);
}

void setup() {
  Serial.begin(115200);
  sim800.begin(9600);

  // Connect to Wi-Fi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi!");

  // Initialize BMP280 sensor
  if (!bmp.begin()) {
    Serial.println("Could not find a valid BMP280 sensor.");
    while (1);
  }

  // Setup the SIM800 module
  setupSIM800();
}

void loop() {
  // Read sensor data from BMP280
  float temperature = bmp.readTemperature();
  float humidity = 45.0;  // Simulated, replace with actual sensor reading
  float pressure = bmp.readPressure() / 100.0F;

  // Create a JSON string with sensor data
  String sensorData = "{\"temperature\": " + String(temperature) + 
                       ", \"humidity\": " + String(humidity) +
                       ", \"pressure\": " + String(pressure) + "}";

  // Send data to the server
  sendDataToServer(sensorData);

  // Wait 60 seconds before sending data again
  delay(60000);
}

[[]]
