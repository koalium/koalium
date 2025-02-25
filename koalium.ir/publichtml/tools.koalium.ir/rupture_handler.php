
<?php
// Database connection details
$servername = "localhost";
$username = "koaliumi_editor";
$password = "koala551364";
$dbname = "koaliumi_rupturium_db";

// Create connection
$connection = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Get the table parameter from the request
$table = $_POST['table'];

if ($table) {
    $query = "SELECT name FROM $table";
    $result = $connection->query($query);

    if ($result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row['name'];
        }
        echo json_encode($data);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode(['error' => 'No table specified']);
}

$connection->close();
?>
