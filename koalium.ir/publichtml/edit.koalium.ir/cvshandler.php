<?php
// Database connection details
$servername = "localhost";
$username = "koaliumi_editor"; // Replace with your database username
$password = "koala551364"; // Replace with your database password
$dbname = "koaliumi_rupturium_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $csvData = $_POST['csv_text'];
    $rows = explode("\n", $csvData);
    foreach ($rows as $row) {
        $columns = str_getcsv($row);
        if (!empty($columns)) {
            // Assuming your table columns are named column1, column2, etc.
            $sql = "INSERT INTO importcvs (column1, column2, column3) VALUES ('$columns[0]', '$columns[1]', '$columns[2]')";
            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully<br>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
            }
        }
    }
}

// Display the content of the table
$sql = "SELECT * FROM importcvs";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Table Content:</h2>";
    echo "<table border='1'>
            <tr>
                <th>Column1</th>
                <th>Column2</th>
                <th>Column3</th>
            </tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["column1"] . "</td>
                <td>" . $row["column2"] . "</td>
                <td>" . $row["column3"] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

$conn->close();
echo"

<!DOCTYPE html>
<html>
<head>
    <title>CSV Import</title>
</head>
<body>

<form method='POST'>
    <label for='csv_text'>CSV Data:</label><br>
    <textarea name='csv_text' rows='10' cols='50'></textarea><br>
    <input type='submit' value='Import CSV'>
</form>

</body>
</html>";
?>