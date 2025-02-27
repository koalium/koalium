
#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>
#include <SoftwareSerial.h>
#include <SD.h>

// Replace with your network credentials
const char* ssid = "smartis";
const char* password = "smart12345";

ESP8266WebServer server(80);

// Dummy database for demonstration
struct User {
  String username;
  String password;
  int level;
};

User users[] = {
  {"user1", "pass1", 2},
  {"admin", "admin123", 5}
};

// Dummy sensor data
float ambientTemp = 25.5;
float humidity = 60.0;

// SIM800 UART communication
#define SIM800_RX_PIN D7  // Connect SIM800 TX to this pin
#define SIM800_TX_PIN D8  // Connect SIM800 RX to this pin
SoftwareSerial sim800Serial(SIM800_RX_PIN, SIM800_TX_PIN);

// Allowed phone number (replace with your number)
String allowedPhoneNumber = "+1234567890";

// SD card pin
#define SD_CS_PIN D8

// Lamp and AC states
bool lampStates[8] = {false};
bool acState = false;

// Timer for saving states to SD card
unsigned long lastSaveTime = 0;
const unsigned long saveInterval = 60000;  // 60 seconds

void setup() {
  Serial.begin(115200);
  sim800Serial.begin(9600);  // SIM800 default baud rate

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
      dbFile.println("Timestamp,Lamp1,Lamp2,Lamp3,Lamp4,Lamp5,Lamp6,Lamp7,Lamp8,AC");
      dbFile.close();
      Serial.println("Created db.csv");
    } else {
      Serial.println("Failed to create db.csv");
    }
  }

  // Create a log file if it doesn't exist
  if (!SD.exists("/log.txt")) {
    File logFile = SD.open("/log.txt", FILE_WRITE);
    if (logFile) {
      logFile.println("Timestamp,Event");
      logFile.close();
      Serial.println("Created log.txt");
    } else {
      Serial.println("Failed to create log.txt");
    }
  }

  // Setting up ESP8266 as an Access Point
  WiFi.softAP(ssid, password);

  IPAddress IP = WiFi.softAPIP();
  Serial.print("AP IP address: ");
  Serial.println(IP);

  // Define server routes
  server.on("/", handleRoot);
  server.on("/login", handleLogin);
  server.on("/control", handleControl);

  server.begin();
  Serial.println("HTTP server started");

  // Initialize SIM800 module
  setupSIM800();

  // Log system startup
  logEvent("System started");
}

void loop() {
  server.handleClient();
  checkSMS();  // Check for incoming SMS

  // Save pin states to SD card every 60 seconds
  if (millis() - lastSaveTime >= saveInterval) {
    savePinStates();
    lastSaveTime = millis();
  }
}

void savePinStates() {
  File dbFile = SD.open("/db.csv", FILE_WRITE);
  if (dbFile) {
    String data = String(millis());  // Timestamp
    for (int i = 0; i < 8; i++) {
      data += "," + String(lampStates[i]);
    }
    data += "," + String(acState);
    dbFile.println(data);
    dbFile.close();
    Serial.println("Saved pin states to db.csv");
  } else {
    Serial.println("Failed to open db.csv");
  }
}

void logEvent(String event) {
  File logFile = SD.open("/log.txt", FILE_WRITE);
  if (logFile) {
    String logEntry = String(millis()) + "," + event;
    logFile.println(logEntry);
    logFile.close();
    Serial.println("Logged event: " + event);
  } else {
    Serial.println("Failed to open log.txt");
  }
}

void setupSIM800() {
  sim800Serial.println("AT");  // Check if SIM800 is responding
  delay(1000);
  sim800Serial.println("AT+CMGF=1");  // Set SMS text mode
  delay(1000);
  sim800Serial.println("AT+CNMI=1,2,0,0,0");  // Set SMS notification
  delay(1000);
}

void checkSMS() {
  if (sim800Serial.available()) {
    String response = sim800Serial.readString();
    Serial.println("SIM800 Response: " + response);

    if (response.indexOf("+CMT:") != -1) {  // Check if an SMS is received
      String phoneNumber = parsePhoneNumber(response);
      String message = parseMessage(response);

      Serial.println("Received SMS from: " + phoneNumber);
      Serial.println("Message: " + message);

      logEvent("SMS received from " + phoneNumber + ": " + message);

      if (phoneNumber == allowedPhoneNumber) {
        processSMSCommand(message);
      } else {
        Serial.println("Unauthorized phone number.");
        logEvent("Unauthorized SMS from " + phoneNumber);
      }
    }
  }
}

String parsePhoneNumber(String response) {
  int startIndex = response.indexOf("+CMT:") + 7;
  int endIndex = response.indexOf("\"", startIndex);
  return response.substring(startIndex, endIndex);
}

String parseMessage(String response) {
  int startIndex = response.indexOf("\n") + 1;
  return response.substring(startIndex);
}

void processSMSCommand(String command) {
  command.trim();
  Serial.println("Processing command: " + command);

  if (command == "STATUS") {
    String status = "Ambient Temp: " + String(ambientTemp) + " C, Humidity: " + String(humidity) + " %";
    sendSMS(allowedPhoneNumber, status);
    logEvent("Sent status SMS to " + allowedPhoneNumber);
  } else if (command.startsWith("TOGGLE")) {
    int lampNumber = command.substring(7).toInt();
    if (lampNumber >= 1 && lampNumber <= 8) {
      lampStates[lampNumber - 1] = !lampStates[lampNumber - 1];
      String reply = "Lamp " + String(lampNumber) + " is now " + (lampStates[lampNumber - 1] ? "ON" : "OFF");
      sendSMS(allowedPhoneNumber, reply);
      logEvent("Toggled Lamp " + String(lampNumber) + " to " + (lampStates[lampNumber - 1] ? "ON" : "OFF"));
    }
  } else {
    sendSMS(allowedPhoneNumber, "Invalid command.");
    logEvent("Invalid SMS command: " + command);
  }
}

void sendSMS(String phoneNumber, String message) {
  sim800Serial.println("AT+CMGS=\"" + phoneNumber + "\"");
  delay(1000);
  sim800Serial.print(message);
  delay(1000);
  sim800Serial.write(26);  // Send Ctrl+Z to end the message
  delay(1000);
  Serial.println("SMS sent to " + phoneNumber);
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

  for (User user : users) {
    if (user.username == username && user.password == password) {
      if (user.level > 3) {
        server.sendHeader("Location", "/control");
        server.send(302, "text/plain", "Logged in, redirecting to control panel");
        logEvent("User " + username + " logged in and accessed control panel");
      } else {
        server.send(200, "text/plain", "Logged in, but access level is insufficient");
        logEvent("User " + username + " logged in but has insufficient access level");
      }
      return;
    }
  }
  server.send(401, "text/plain", "Login failed");
  logEvent("Failed login attempt with username: " + username);
}

void handleControl() {
  String html = "<h1>Control Panel</h1>";
  html += "<p>Ambient Temp: " + String(ambientTemp) + " C</p>";
  html += "<p>Humidity: " + String(humidity) + " %</p>";
  for (int i = 1; i <= 8; i++) {
    html += "<p>Lamp " + String(i) + ": <a href='/toggle?lamp=" + String(i) + "'>Toggle</a></p>";
  }
  html += "<p>Air Conditioner: <a href='/toggle?ac=1'>Toggle</a></p>";
  server.send(200, "text/html", html);
}

void handleToggle() {
  if (server.hasArg("lamp")) {
    int lamp = server.arg("lamp").toInt() - 1;
    lampStates[lamp] = !lampStates[lamp];
    logEvent("Toggled Lamp " + String(lamp + 1) + " to " + (lampStates[lamp] ? "ON" : "OFF"));
  } else if (server.hasArg("ac")) {
    acState = !acState;
    logEvent("Toggled AC to " + (acState ? "ON" : "OFF"));
  }
  server.sendHeader("Location", "/control");
  server.send(302, "text/plain", "Toggled, redirecting to control panel");
}