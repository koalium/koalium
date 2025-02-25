
<?php
// Database connection details
$servername = "localhost";
$username = "koaliumi_editor"; // Replace with your database username
$password = "koala551364"; // Replace with your database password
$dbname = "koaliumi_rupturium_db";
 echo "Error: " .$_POST['type']." " . $_POST['size'].$_POST['mainLayer'].$_POST['subLayer'].$_POST['sealLayer'].$_POST['burstPressure'].$_POST['burstTemperature'];
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Form data
$type = $_POST['type'];
$size = $_POST['size'];
$main_layer = $_POST['mainLayer'];
$sub_layer = $_POST['subLayer'];
$seal_layer = $_POST['sealLayer'];
$burst_pressure = $_POST['burstPressure'];
$burst_temperature = $_POST['burstTemperature'];
$request_by = "user"; // Replace with the actual requester identifier
$desc = "Request description"; // Replace with actual description





// Insert form data into the table
$sql_insert = "
INSERT INTO requested_rupture (type, size, main, sub, seal, bp, bt)
VALUES ('$type', $size, '$main_layer', '$sub_layer', '$seal_layer', '$burst_pressure', '$burst_temperature')";

if ($conn->query($sql_insert) === TRUE) {
    echo "New record created successfully.<br>";
} else {
    echo "Error: " . $sql_insert . "<br>" . $conn->error;
}

// Search for the element with the closest burst pressure
$sql_search = "
SELECT *
FROM tested
WHERE size = $size
AND type = '$type'
ORDER BY ABS(bp - $burst_pressure) ASC
LIMIT 1";

$result = $conn->query($sql_search);

if ($result->num_rows > 0) {
    // Output data of the found row
    $row = $result->fetch_assoc();
    
    $closest_bp = $row["layer"];

    echo "Found element with burst pressure: $closest_bp<br>";
    
} else {
    echo "No matching elements found in tested.<br>";

    
}




// Close connection
$conn->close();
?>
