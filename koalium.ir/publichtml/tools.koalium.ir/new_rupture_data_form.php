
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rupture Disk Design</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #007bff;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        select, input[type="number"], input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        button[type="submit"] {
            background-color: #007bff;
            color: #fff;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        button[type="button"] {
            background-color: #28a745;
            color: #fff;
        }
        button[type="button"]:hover {
            background-color: #218838;
        }
    </style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rupture Disk</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form action="rupture_burst_data_handler.php" method="post" >
		<label><h2>rupture disk</h2></label>
        <label for="type">Type:</label>
        <select id="type" name="type">
            <?php
            $type_query = "SELECT type FROM types";
            $result = $conn->query($type_query);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['type']}'>{$row['type']}</option>";
            }
            ?>
        </select><br>

        <label for="size">Size:</label>
        <select id="size" name="size">
            <?php
            $size_query = "SELECT * FROM element_raw_size WHERE element = 'rupture' AND type= 'reverse'" ;
            $result = $conn->query($size_query);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['size']}'>{$row['size']}</option>";
            }
            ?>
        </select><br>

        <label for="layers"><h2>Layers:</h2></label><br>
        
        <label for="sheet_material">main:</label>
        <select id="sheet_material" name="sheet_material">
            <?php
            $size_query = "SELECT name FROM materials WHERE layers='m,v'" ;
            $result = $conn->query($size_query);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['name']}'>{$row['name']}</option>";
            }
            ?>
        </select><br>

        <label for="thickness_main">Thickness (0.05 to 5.5 step 0.05):</label>
        <input type="number" id="thickness_main" name="thickness_main" min="0.05" max="5.5" step="0.05"><br>
		
		<label>sub:</label><br>
		<select id="sheet_material_sub" name="sheet_material_sub">
            <?php
            $size_query = "SELECT name FROM materials  WHERE layers='s'" ;
            $result = $conn->query($size_query);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['name']}'>{$row['name']}</option>";
            }
            ?>
        </select><br>
		<label for="thickness_sub">Thickness (0.05 to 5.5 step 0.05):</label>
        <input type="number" id="thickness_sub" name="thickness_sub" min="0.05" max="5.5" step="0.05"><br>
		
		<label>seal:</label><br>
		<select id="sheet_material_seal" name="sheet_material_seal">
            <?php
            $size_query = "SELECT name FROM materials  WHERE layers='s'" ;
            $result = $conn->query($size_query);
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['name']}'>{$row['name']}</option>";
            }
            ?>
        </select><br>
		<label for="thickness_seal">Thickness (0.05 to 5.5 step 0.05):</label>
        <input type="number" id="thickness_seal" name="thickness_seal" min="0.05" max="5.5" step="0.05"><br>
		
        <label for="burst_temp">Burst Temperature (0.01 to 221 step 0.1):</label>
        <input type="number" id="burst_temp" name="burst_temp" min="0.01" max="221" step="0.1"><br>

        <label for="burst_temp_cold">Burst Temperature (-270 to 550 step 1):</label>
        <input type="number" id="burst_temp_cold" name="burst_temp_cold" min="-270" max="550" step="1"><br>
		
		

        <button type="submit">Submit</button>
    </form>
</body>
</html>

<?php
$conn->close();
?>
