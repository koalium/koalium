#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>
#include <MySQL_Connection.h>
#include <MySQL_Cursor.h>

// Wi-Fi Credentials
const char* ssid = "smartis";
const char* password = "smart12345";

// Database credentials
IPAddress server_ip(192, 168, 1, 100); // Replace with your MySQL server IP
const char* db_user = "root";  // MySQL username
const char* db_pass = "password";  // MySQL password
const char* db_name = "smart_home";  // Database name

WiFiClient client;
MySQL_Connection conn((Client *)&client);
ESP8266WebServer server(80);

String username = "";
String user_password = "";
String mac_address = "";

// Control states (for example, 8 lamps)
bool lampStates[8] = {false, false, false, false, false, false, false, false};
bool acOn = false;
int acTemp = 22; // Default temperature
bool alarmMode = false;

// Connect to Wi-Fi
void setupWiFi() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi!");
}

// Establish DB connection
void setupDatabase() {
  if (conn.connect(server_ip, 3306, db_user, db_pass)) {
    Serial.println("Connected to Database!");
  } else {
    Serial.println("Connection failed...");
    while (true); // Halt if no DB connection
  }
}

// HTML Login Form
String loginPage = R"(
<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
  <h2>Login</h2>
  <form action="/login" method="get">
    Username: <input type="text" name="username"><br>
    Password: <input type="password" name="password"><br>
    <input type="submit" value="Login">
  </form>
</body>
</html>
)";

// HTML Control Dashboard
String controlDashboard = R"(
<!DOCTYPE html>
<html>
<head><title>Control Dashboard</title></head>
<body>
  <h2>Monitoring</h2>
  <div>Temperature: {{temperature}} °C</div>
  <div>Humidity: {{humidity}}%</div>
  <div>Lamps: {{lamp_states}}</div>
  <div>Doors: {{door_states}}</div>
  <div>Windows: {{window_states}}</div>
  <div>System State: {{system_state}}</div>
  <div>
    <h3>Control Panel</h3>
    <button onclick="toggleLamp(1)">Lamp 1</button>
    <button onclick="toggleLamp(2)">Lamp 2</button>
    <button onclick="toggleLamp(3)">Lamp 3</button>
    <button onclick="toggleLamp(4)">Lamp 4</button>
    <button onclick="toggleLamp(5)">Lamp 5</button>
    <button onclick="toggleLamp(6)">Lamp 6</button>
    <button onclick="toggleLamp(7)">Lamp 7</button>
    <button onclick="toggleLamp(8)">Lamp 8</button>
    <button onclick="toggleAC()">Toggle AC</button>
    <button onclick="setACTemperature()">Set AC Temp</button>
    <button onclick="toggleAlarm()">Activate Alarm</button>
  </div>
</body>
</html>
)";

// Handle login route
void handleLogin() {
  username = server.arg("username");
  user_password = server.arg("password");
  mac_address = WiFi.macAddress();

  // Query to insert login attempt into DB
  String query = "INSERT INTO tryconnect (username, password, mac_address, time) VALUES ('" + username + "', '" + user_password + "', '" + mac_address + "', NOW())";
  MySQL_Cursor cur = MySQL_Cursor(&conn);
  cur.execute(query.c_str());

  // Verify credentials
  query = "SELECT * FROM users WHERE username = '" + username + "' AND password = '" + user_password + "'";
  cur.execute(query.c_str());
  if (cur.available()) {
    MySQL_Row row = cur.readRow();
    int lvl = row.getInt(3);  // Assuming 4th column is lvl
    if (lvl < 5) {
      // Show control dashboard with real-time data
      controlDashboard.replace("{{temperature}}", "25");
      controlDashboard.replace("{{humidity}}", "60");
      controlDashboard.replace("{{lamp_states}}", "ON, OFF, OFF, ON, OFF, OFF, ON, OFF");
      controlDashboard.replace("{{door_states}}", "OPEN, CLOSED");
      controlDashboard.replace("{{window_states}}", "OPEN, CLOSED");
      controlDashboard.replace("{{system_state}}", "Idle");
      server.send(200, "text/html", controlDashboard);
    } else {
      // Show monitoring dashboard for higher lvl users
      server.send(200, "text/html", "<h2>Monitoring Dashboard</h2>");
    }
  } else {
    server.send(200, "text/html", "Invalid Credentials");
  }
}

// Toggle Lamp States
void toggleLamp(int lampId) {
  lampStates[lampId - 1] = !lampStates[lampId - 1];
  String query = "UPDATE sensoresdata SET lamp" + String(lampId) + " = '" + String(lampStates[lampId - 1]) + "' WHERE id = 1"; // Assuming id=1 for this example
  MySQL_Cursor cur = MySQL_Cursor(&conn);
  cur.execute(query.c_str());
}

// Toggle AC
void toggleAC() {
  acOn = !acOn;
  String query = "UPDATE sensoresdata SET acOn = '" + String(acOn) + "' WHERE id = 1";
  MySQL_Cursor cur = MySQL_Cursor(&conn);
  cur.execute(query.c_str());
}

// Set AC Temperature
void setACTemperature() {
  acTemp = (acTemp == 22) ? 25 : 22; // Toggle between two temperatures for simplicity
  String query = "UPDATE sensoresdata SET acTemp = '" + String(acTemp) + "' WHERE id = 1";
  MySQL_Cursor cur = MySQL_Cursor(&conn);
  cur.execute(query.c_str());
}

// Toggle Alarm Mode
void toggleAlarm() {
  alarmMode = !alarmMode;
  String query = "UPDATE sensoresdata SET alarmMode = '" + String(alarmMode) + "' WHERE id = 1";
  MySQL_Cursor cur = MySQL_Cursor(&conn);
  cur.execute(query.c_str());
}

// Set up routes
void setupRoutes() {
  server.on("/", HTTP_GET, [](){
    server.send(200, "text/html", loginPage);
  });
  
  server.on("/login", HTTP_GET, handleLogin);
  
  // Add more routes for control buttons
}

void setup() {
  setupWiFi();
  setupDatabase();
  setupRoutes();
  
  server.begin();
  Serial.println("HTTP server started");
}

void loop() {
  server.handleClient();
}
