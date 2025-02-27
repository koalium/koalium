// Handle the Root Login Page with better styling
void handleRoot() {
  String html = R"(
  <html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
      body { font-family: Arial, sans-serif; background-color: #f4f4f9; color: #333; margin: 0; padding: 0; }
      h1 { text-align: center; padding: 20px; color: #4CAF50; }
      .container { width: 100%; max-width: 400px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
      label { font-size: 14px; color: #555; }
      input[type='text'], input[type='password'] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; }
      input[type='submit'] { background-color: #4CAF50; color: white; border: none; padding: 12px 20px; font-size: 16px; width: 100%; cursor: pointer; }
      input[type='submit']:hover { background-color: #45a049; }
      @media screen and (max-width: 600px) {
        .container { width: 80%; }
      }
    </style>
  </head>
  <body>
    <div class="container">
      <h1>Login</h1>
      <form action='/login' method='POST'>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <input type="submit" value="Login">
      </form>
    </div>
  </body>
  </html>
  )";
  server.send(200, "text/html", html);
}

// Handle Dashboard Page (Control and Monitoring)
void handleDashboard() {
  String html = R"(
  <html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
      body { font-family: Arial, sans-serif; background-color: #f4f4f9; color: #333; margin: 0; padding: 0; }
      h1 { text-align: center; padding: 20px; color: #4CAF50; }
      .container { width: 100%; max-width: 1000px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
      .section { margin: 20px 0; }
      .section h2 { color: #4CAF50; }
      .control-panel, .monitoring-panel { display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px; }
      .control-panel .button, .monitoring-panel .button { padding: 10px 20px; font-size: 16px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; transition: background-color 0.3s ease; }
      .control-panel .button:hover, .monitoring-panel .button:hover { background-color: #45a049; }
      .sensor-data { font-size: 16px; padding: 10px; background-color: #f9f9f9; border-radius: 5px; }
      @media screen and (max-width: 600px) {
        .control-panel, .monitoring-panel { flex-direction: column; align-items: center; }
        .control-panel .button, .monitoring-panel .button { width: 100%; margin-bottom: 10px; }
      }
    </style>
  </head>
  <body>
    <div class="container">
      <h1>Dashboard</h1>
      <div class="section">
        <h2>Control Dashboard</h2>
        <form action='/control' method='POST'>
          <div class="control-panel">
            <div class="button"><label>Lamp 1: <input type="checkbox" name="lamp1" " + String(lampStates[0] ? "checked" : "") + "></label></div>
            <div class="button"><label>Lamp 2: <input type="checkbox" name="lamp2" " + String(lampStates[1] ? "checked" : "") + "></label></div>
            <div class="button"><label>Lamp 3: <input type="checkbox" name="lamp3" " + String(lampStates[2] ? "checked" : "") + "></label></div>
            <div class="button"><label>Lamp 4: <input type="checkbox" name="lamp4" " + String(lampStates[3] ? "checked" : "") + "></label></div>
            <div class="button"><label>Lamp 5: <input type="checkbox" name="lamp5" " + String(lampStates[4] ? "checked" : "") + "></label></div>
            <div class="button"><label>Lamp 6: <input type="checkbox" name="lamp6" " + String(lampStates[5] ? "checked" : "") + "></label></div>
            <div class="button"><label>Lamp 7: <input type="checkbox" name="lamp7" " + String(lampStates[6] ? "checked" : "") + "></label></div>
            <div class="button"><label>Lamp 8: <input type="checkbox" name="lamp8" " + String(lampStates[7] ? "checked" : "") + "></label></div>
            <div class="button"><label>AC On: <input type="checkbox" name="acOn" " + String(acOn ? "checked" : "") + "></label></div>
            <div class="button"><label>AC Temp: <input type="number" name="acTemp" value='" + String(acTemp) + "' min='10' max='30'></label></div>
          </div>
          <div class="button"><input type="submit" value="Update Control"></div>
        </form>
      </div>
      <div class="section">
        <h2>Monitoring Dashboard</h2>
        <div class="monitoring-panel">
          <div class="sensor-data">Temperature: " + String(temperature) + " °C</div>
          <div class="sensor-data">Humidity: " + String(humidity) + " %</div>
          <div class="sensor-data">Pressure: " + String(pressure) + " hPa</div>
        </div>
      </div>
    </div>
  </body>
  </html>
  )";
  server.send(200, "text/html", html);
}
