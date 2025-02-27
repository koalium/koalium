<?php
// Server-side PHP script to handle sensor data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $deviceID = $_POST['deviceID']; // Device ID
    $data = $_POST['data']; // Sensor data
    
    // Authenticate the device (could be a simple check for valid device ID)
    if ($deviceID === 'Device1234') {
        // Save the sensor data in the database
        $db = new mysqli('localhost', 'username', 'password', 'database');
        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }

        $stmt = $db->prepare("INSERT INTO sensor_data (deviceID, data, timestamp) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $deviceID, $data);
        $stmt->execute();
        $stmt->close();
        
        // Respond back to the device
        echo "Data received successfully!";
    } else {
        echo "Invalid device ID!";
    }
} else {
    echo "Invalid request method!";
}
?>
