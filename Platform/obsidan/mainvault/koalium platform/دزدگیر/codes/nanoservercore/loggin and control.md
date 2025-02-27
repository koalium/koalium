#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>
#include <SPI.h>
#include <SD.h>
#include <SoftwareSerial.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BMP280.h>

const char* ssid = "smartis";
const char* password = "smart12345";

// Web server on port 80
ESP8266WebServer server(80);

// Pin states for controlling devices (8 lamps, AC)
bool lampStates[8] = {false, false, false, false, false, false, false, false};
bool acOn = false;
int acTemp = 22; // Default AC temperature

// Sensor data
float temperature = 0.0;
float humidity = 0.0;
float pressure = 0.0;

// User credentials (simulate database check)
const String correctUsername = "admin";
const String correctPassword = "password123";
int userLevel = 0;  // 0 = Guest, 1 = User, 2 = Admin

// Pins for switches (Windows, Door)
const int windowSwitch1 = D2;
const int windowSwitch2 = D3;
const int doorSwitch = D4;
const int systemStatePin = D5;

// SD card
File dataFile;
const int chipSelect = D8;

Adafruit_BMP280 bmp; // BMP280 sensor for temperature, humidity, pressure

// Function to initialize SD card
void setupSDCard() {
  if (!SD.begin(chipSelect)) {
    Serial.println("SD card initialization failed!");
    while (true);
  }
  Serial.println("SD card initialized.");
  if (!SD.exists("log.csv")) {
    dataFile = SD.open("log.csv", FILE_WRITE);
    if (dataFile) {
      dataFile.println("Timestamp, Lamp1, Lamp2, Lamp3, Lamp4, Lamp5, Lamp6, Lamp7, Lamp8, Door1, Door2, Window1, Window2, AC, AC Temp, Temperature, Humidity, Pressure, System State, Alarm Mode");
      dataFile.close();
    }
  }
}

// Function to log sensor data to SD card
void logSensorData() {
  dataFile = SD.open("log.csv", FILE_WRITE);
  if (dataFile) {
    unsigned long timestamp = millis() / 1000; // Timestamp in seconds
    dataFile.print(timestamp);
    dataFile.print(", ");
    for (int i = 0; i < 8; i++) dataFile.print(lampStates[i]);
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
    dataFile.println();
    dataFile.close();
  }
}

// Function to read BMP280 sensor data
void readBMP280() {
  temperature = bmp.readTemperature();
  humidity = 50.0;  // Dummy value, since BMP280 doesn't measure humidity directly
  pressure = bmp.readPressure() / 100.0; // Convert to hPa
}

// Handle root route - Login page
void handleRoot() {
  String html = "<html><body><h1>Login</h1><form action='/login' method='POST'>";
  html += "<label>Username: </label><input type='text' name='username'><br>";
  html += "<label>Password: </label><input type='password' name='password'><br>";
  html += "<input type='submit' value='Login'>";
  html += "</form></body></html>";
  server.send(200, "text/html", html);
}

// Handle login POST request
void handleLogin() {
  String username = server.arg("username");
  String password = server.arg("password");

  // Check credentials (simulate DB check)
  if (username == correctUsername && password == correctPassword) {
    userLevel = 2; // Admin level
    server.sendHeader("Location", "/dashboard", true);
    server.send(303);  // Redirect to dashboard
  } else {
    server.send(401, "text/html", "<html><body><h1>Invalid credentials</h1></body></html>");
  }
}

// Handle dashboard page
void handleDashboard() {
  String html = "<html><body><h1>Dashboard</h1>";
  if (userLevel >= 2) {  // Admin level (can control devices)
    html += "<h2>Control Dashboard</h2>";
    html += "<form action='/control' method='POST'>";
    html += "<label>Lamp 1: </label><input type='checkbox' name='lamp1' " + String(lampStates[0] ? "checked" : "") + "><br>";
    html += "<label>Lamp 2: </label><input type='checkbox' name='lamp2' " + String(lampStates[1] ? "checked" : "") + "><br>";
    html += "<label>Lamp 3: </label><input type='checkbox' name='lamp3' " + String(lampStates[2] ? "checked" : "") + "><br>";
    html += "<label>Lamp 4: </label><input type='checkbox' name='lamp4' " + String(lampStates[3] ? "checked" : "") + "><br>";
    html += "<label>Lamp 5: </label><input type='checkbox' name='lamp5' " + String(lampStates[4] ? "checked" : "") + "><br>";
    html += "<label>Lamp 6: </label><input type='checkbox' name='lamp6' " + String(lampStates[5] ? "checked" : "") + "><br>";
    html += "<label>Lamp 7: </label><input type='checkbox' name='lamp7' " + String(lampStates[6] ? "checked" : "") + "><br>";
    html += "<label>Lamp 8: </label><input type='checkbox' name='lamp8' " + String(lampStates[7] ? "checked" : "") + "><br>";
    html += "<label>AC On: </label><input type='checkbox' name='acOn' " + String(acOn ? "checked" : "") + "><br>";
    html += "<label>AC Temp: </label><input type='number' name='acTemp' value='" + String(acTemp) + "' min='10' max='30'><br>";
    html += "<input type='submit' value='Update'>";
    html += "</form>";
    html += "<h2>Monitoring Dashboard</h2>";
    html += "<p>Temperature: " + String(temperature) + " °C</p>";
    html += "<p>Humidity: " + String(humidity) + " %</p>";
    html += "<p>Pressure: " + String(pressure) + " hPa</p>";
  } else {
    html += "<h2>Monitoring Dashboard</h2>";
    html += "<p>Temperature: " + String(temperature) + " °C</p>";
    html += "<p>Humidity: " + String(humidity) + " %</p>";
    html += "<p>Pressure: " + String(pressure) + " hPa</p>";
  }
  html += "</body></html>";
  server.send(200, "text/html", html);
}

// Handle control post request
void handleControl() {
  // Get values from the form
  lampStates[0] = server.hasArg("lamp1");
  lampStates[1] = server.hasArg("lamp2");
  lampStates[2] = server.hasArg("lamp3");
  lampStates[3] = server.hasArg("lamp4");
  lampStates[4] = server.hasArg("lamp5");
  lampStates[5] = server.hasArg("lamp6");
  lampStates[6] = server.hasArg("lamp7");
  lampStates[7] = server.hasArg("lamp8");
  acOn = server.hasArg("acOn");
  acTemp = server.arg("acTemp").toInt();

  // Log changes to SD card
  logSensorData();

  // Redirect back to the dashboard
  server.sendHeader("Location", "/dashboard", true);
  server.send(303);  // Redirect to dashboard
}

// Setup the server
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
    Serial.println("Could not find a valid BMP280 sensor.");
    while (1);
  }

  // Setup SD card
  setupSDCard();

  // Define routes
  server.on("/", HTTP_GET, handleRoot);              // Root page
  server.on("/login", HTTP_POST, handleLogin);       // Login validation
  server.on("/dashboard", HTTP_GET, handleDashboard); // Dashboard page
  server.on("/control", HTTP_POST, handleControl);    // Handle control commands

  server.begin(); // Start the server
}

void loop() {
  // Handle incoming HTTP requests
  server.handleClient();

  // Read sensor data and log every minute
  static unsigned long lastLogTime = 0;
  if (millis() - lastLogTime >= 60000) {
    readBMP280();
    logSensorData();
    lastLogTime = millis();
  }
}
