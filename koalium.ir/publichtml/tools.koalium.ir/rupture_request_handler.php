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
foreach ($_POST as $key => $value) {
    echo "$key: $value\n";
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
INSERT INTO requested_rupture (type, size, main, sub, seal, bp, bt,request_by)
VALUES ('$type', $size, '$main_layer', '$sub_layer', '$seal_layer', '$burst_pressure', '$burst_temperature','$request_by')";

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
ORDER BY ABS(rbp - $burst_pressure) ASC
LIMIT 1";

$result = $conn->query($sql_search);

if ($result->num_rows > 0) {
    // Output data of the found row
    $row = $result->fetch_assoc();
    
    $closest_bp = $row["layer"];
    $layersf =  $closest_bp ."_   ".$row["rbp"]."  ".$row["bp"];
    function parseText($input) {
    // Split the text into rows
        $rows = explode('_', $input);

        // Initialize an empty array to hold the parsed data
        $parsedData = [];
        $rowcounter=0;
        // Loop through each row
        foreach ($rows as $row) {
        // Split the row into columns
            $columns = explode(':', $row);
            
            if($rowcounter==0){
                $mylayer = $_POST['mainLayer'].":".$columns[1];
                $parsedData[] = explode(':', $mylayer);
            }else if($rowcounter==1){
                $mylayer = $_POST['subLayer'].":".$columns[1];
                $parsedData[] = explode(':', $mylayer);
            }else{
                $parsedData[] = explode(':', $row);
            }
            
           // $mycol = explode(':', $mylayer);
            $rowcounter+=1;
            // Add the columns to the parsed data array
            //$parsedData[] = $mycol;
        }
        

        return $parsedData;
    }

    function displayTable($parsedData) {
    echo "
    <style>
    table {
        width: 50%;
        margin: 20px auto;
        border-collapse: collapse;
        box-shadow: 0 2px 3px rgba(0,0,0,0.1);
    }
    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #f2f2f2;
    }
    tr:hover {
        background-color: #f9f9f9;
    }
    </style>";

    echo "<table>";
    echo "<tr><th>Layer</th><th>Thickness</th></tr>";

    foreach ($parsedData as $row) {
        if(count($row) >= 2) {
            echo "<tr><td>" . htmlspecialchars($row[0]) . "</td><td>" . htmlspecialchars($row[1]) . "</td></tr>";
        }
    }

    echo "</table>";
}

        // Example usage
        $inputText = "layer1;thickness1_layer2;thickness2_layer3;thickness3";
        $parsedData = parseText($closest_bp);

        // Display the parsed data as a table
        displayTable($parsedData);



    echo "Found element with burst pressure: $layersf<br>";
    
} else {
    echo "No matching elements found in tested.<br>";

    
}




// Close connection
$conn->close();
?>
