#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>
#include <SoftwareSerial.h>
#include <SD.h>
#include <Wire.h>
#include <Adafruit_BMP280.h>

// Replace with your network credentials
const char* ssid = "smartis";
const char* password = "smart12345";

ESP8266WebServer server(80);

// BMP280 sensor
Adafruit_BMP280 bmp;

// SD card pin
#define SD_CS_PIN D8

// Switch pins
#define WINDOW1_PIN D3
#define WINDOW2_PIN D4
#define WINDOW3_PIN D5
#define DOOR_PIN D6

// AJIR pin (output pin to trigger an action)
#define AJIR_PIN D0

// Lamp and AC states
bool lampStates[8] = {false};
bool acState = false;

// Switch states
bool window1State = false;
bool window2State = false;
bool window3State = false;
bool doorState = false;

// Situation states
enum Situation { IDLE, LOW, HIGH };
Situation currentSituation = IDLE;

// WTF state
bool wtfState = false;

// User phone number (replace with actual number)
String userPhoneNumber = "+1234567890";

// Timer for saving sensor data to SD card
unsigned long lastSaveTime = 0;
const unsigned long saveInterval = 60000;  // 1 minute

void setup() {
  Serial.begin(115200);

  // Initialize BMP280 sensor
  if (!bmp.begin(0x76)) {  // BMP280 I2C address (0x76 or 0x77)
    Serial.println("Could not find a valid BMP280 sensor, check wiring!");
    while (1);
  }
  Serial.println("BMP280 sensor initialized.");

  // Initialize SD card
  if (!SD.begin(SD_CS_PIN)) {
    Serial.println("SD card initialization failed!");
    return;
  }
  Serial.println("SD card initialized.");

  // Create a CSV file if it doesn't exist
  if (!SD.exists("/db.csv")) {
    File dbFile = SD.open("/db.csv", FILE_WRITE);
    if (dbFile) {
      dbFile.println("Timestamp,Temperature (C),Pressure (Pa),Humidity (%),Window1,Window2,Window3,Door,Lamp1,Lamp2,Lamp3,Lamp4,Lamp5,Lamp6,Lamp7,Lamp8,AC,Situation,WTF");
      dbFile.close();
      Serial.println("Created db.csv");
    } else {
      Serial.println("Failed to create db.csv");
    }
  }

  // Initialize switch pins as inputs
  pinMode(WINDOW1_PIN, INPUT);
  pinMode(WINDOW2_PIN, INPUT);
  pinMode(WINDOW3_PIN, INPUT);
  pinMode(DOOR_PIN, INPUT);

  // Initialize AJIR pin as output
  pinMode(AJIR_PIN, OUTPUT);
  digitalWrite(AJIR_PIN, LOW);

  // Setting up ESP8266 as an Access Point
  WiFi.softAP(ssid, password);

  IPAddress IP = WiFi.softAPIP();
  Serial.print("AP IP address: ");
  Serial.println(IP);

  // Define server routes
  server.on("/", handleRoot);
  server.on("/login", handleLogin);
  server.on("/control", handleControl);
  server.on("/setSituation", handleSetSituation);

  server.begin();
  Serial.println("HTTP server started");
}

void loop() {
  server.handleClient();

  // Check switch states and log changes
  checkSwitches();

  // Save sensor data and pin states to SD card every 1 minute
  if (millis() - lastSaveTime >= saveInterval) {
    saveSensorData();
    lastSaveTime = millis();
  }
}

void checkSwitches() {
  bool newWindow1State = digitalRead(WINDOW1_PIN);
  bool newWindow2State = digitalRead(WINDOW2_PIN);
  bool newWindow3State = digitalRead(WINDOW3_PIN);
  bool newDoorState = digitalRead(DOOR_PIN);

  if (newWindow1State != window1State || newWindow2State != window2State || newWindow3State != window3State || newDoorState != doorState) {
    window1State = newWindow1State;
    window2State = newWindow2State;
    window3State = newWindow3State;
    doorState = newDoorState;
    savePinStates("Switch state change");

    // Check if situation is HIGH and a window/door is opened
    if (currentSituation == HIGH && (window1State || window2State || window3State || doorState)) {
      wtfState = true;
      digitalWrite(AJIR_PIN, HIGH);
      sendAlertSMS();
    }
  }
}

void sendAlertSMS() {
  String message = "WTF State Triggered! ";
  if (window1State) message += "Window 1 opened. ";
  if (window2State) message += "Window 2 opened. ";
  if (window3State) message += "Window 3 opened. ";
  if (doorState) message += "Door opened.";

  // Send SMS (replace with actual SMS sending code)
  Serial.println("Sending SMS: " + message);
  // sim800Serial.println("AT+CMGS=\"" + userPhoneNumber + "\"");
  // delay(1000);
  // sim800Serial.print(message);
  // delay(1000);
  // sim800Serial.write(26);  // Send Ctrl+Z to end the message
  // delay(1000);
}

void saveSensorData() {
  float temperature = bmp.readTemperature();
  float pressure = bmp.readPressure();
  float humidity = 0.0;  // Replace with actual humidity reading if you have a sensor

  File dbFile = SD.open("/db.csv", FILE_WRITE);
  if (dbFile) {
    String data = String(millis()) + "," + String(temperature) + "," + String(pressure) + "," + String(humidity);
    data += "," + String(window1State) + "," + String(window2State) + "," + String(window3State) + "," + String(doorState);
    for (int i = 0; i < 8; i++) {
      data += "," + String(lampStates[i]);
    }
    data += "," + String(acState) + "," + String(currentSituation) + "," + String(wtfState);
    dbFile.println(data);
    dbFile.close();
    Serial.println("Saved sensor data and pin states to db.csv");
  } else {
    Serial.println("Failed to open db.csv");
  }
}

void savePinStates(String reason) {
  File dbFile = SD.open("/db.csv", FILE_WRITE);
  if (dbFile) {
    String data = String(millis()) + ",,,,";  // Timestamp, empty fields for sensor data
    data += String(window1State) + "," + String(window2State) + "," + String(window3State) + "," + String(doorState);
    for (int i = 0; i < 8; i++) {
      data += "," + String(lampStates[i]);
    }
    data += "," + String(acState) + "," + String(currentSituation) + "," + String(wtfState);
    dbFile.println(data);
    dbFile.close();
    Serial.println("Saved pin states to db.csv due to: " + reason);
  } else {
    Serial.println("Failed to open db.csv");
  }
}

void handleRoot() {
  String html = "<form action='/login' method='POST'>";
  html += "Username: <input type='text' name='username'><br>";
  html += "Password: <input type='password' name='password'><br>";
  html += "<input type='submit' value='Login'></form>";
  server.send(200, "text/html", html);
}

void handleLogin() {
  String username = server.arg("username");
  String password = server.arg("password");

  // Dummy authentication
  if (username == "admin" && password == "admin123") {
    server.sendHeader("Location", "/control");
    server.send(302, "text/plain", "Logged in, redirecting to control panel");
  } else {
    server.send(401, "text/plain", "Login failed");
  }
}

void handleControl() {
  String html = "<h1>Control Panel</h1>";
  html += "<p>Ambient Temp: " + String(bmp.readTemperature()) + " C</p>";
  html += "<p>Pressure: " + String(bmp.readPressure()) + " Pa</p>";
  html += "<p>Situation: <form action='/setSituation' method='POST'>";
  html += "<select name='situation'>";
  html += "<option value='0'" + String(currentSituation == IDLE ? " selected" : "") + ">Idle</option>";
  html += "<option value='1'" + String(currentSituation == LOW ? " selected" : "") + ">Low</option>";
  html += "<option value='2'" + String(currentSituation == HIGH ? " selected" : "") + ">High</option>";
  html += "</select><input type='submit' value='Set'></form></p>";
  for (int i = 1; i <= 8; i++) {
    html += "<p>Lamp " + String(i) + ": <a href='/toggle?lamp=" + String(i) + "'>Toggle</a></p>";
  }
  html += "<p>Air Conditioner: <a href='/toggle?ac=1'>Toggle</a></p>";
  server.send(200, "text/html", html);
}

void handleSetSituation() {
  int situation = server.arg("situation").toInt();
  currentSituation = static_cast<Situation>(situation);
  server.sendHeader("Location", "/control");
  server.send(302, "text/plain", "Situation set, redirecting to control panel");
}

void handleToggle() {
  if (server.hasArg("lamp")) {
    int lamp = server.arg("lamp").toInt() - 1;
    lampStates[lamp] = !lampStates[lamp];
    savePinStates("Lamp toggle");
  } else if (server.hasArg("ac")) {
    acState = !acState;
    savePinStates("AC toggle");
  }
  server.sendHeader("Location", "/control");
  server.send(302, "text/plain", "Toggled, redirecting to control panel");
}