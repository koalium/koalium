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
            position: relative;
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
        .loading {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            text-align: center;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid rgba(0, 123, 255, 0.2);
            border-top: 5px solid #007bff;
            border-radius: 50%;
            animation: spin 1.1s linear infinite;
            margin: 10px auto;
        }
        .spinner-text {
            font-size: 18px;
            margin-top: 10px;
        }
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Rupture Disk</h1>
        <form id="ruptureForm" action="./rupture_request_handler.php" method="POST">
            <label for="type">Type:</label>
            <select id="type" name="type" required>
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

				// Fetch data from database
				$sql = "SELECT type FROM element_types WHERE element='rupture'";
				$result = $conn->query($sql);

				// Populate select input with data
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						echo "<option value='" . $row['type'] . "'>" . $row['type'] . "</option>";
					}
				} else {
					echo "<option value=''>No options available</option>";
				}

				// Close connection
				$conn->close();
				?>
                
            </select>

            <label for="size">Size:</label>
			<select id="size" name="size" required>
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

				// Fetch data from database
				$sql = "SELECT size FROM element_raw_size WHERE element='rupture' AND type='flat'";
				$result = $conn->query($sql);

				// Populate select input with data
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						echo "<option value='" . $row['size'] . "'>" . $row['size'] . "</option>";
					}
				} else {
					echo "<option value=''>No options available</option>";
				}

				// Close connection
				$conn->close();
				?>
                
            </select>
            <input type="number" id="size" name="size" step="0.5" min="0.5" max="102" value="4" required>

            <label for="mainLayer">Main Layer Material:</label>
            <select id="mainLayer" name="mainLayer">
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

				// Fetch data from database
				$sql = "SELECT name FROM materials WHERE layers LIKE '%m%'";
				$result = $conn->query($sql);

				// Populate select input with data
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$selected = ($row['name'] == 'ss316') ? "selected" : "";
						echo "<option value='" . $row['name'] . "' $selected>" . $row['name'] . "</option>";
					}
				} else {
					echo "<option value=''>No options available</option>";
				}

				// Close connection
				$conn->close();
				?>
                
            </select>

            <label for="subLayer">Sub Layer Material:</label>
            <select id="subLayer" name="subLayer">
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

				// Fetch data from database
				$sql = "SELECT name FROM materials WHERE layers LIKE '%m%'";
				$result = $conn->query($sql);

				// Populate select input with data
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$selected = ($row['name'] == 'ss316') ? "selected" : "";
						echo "<option value='" . $row['name'] . "' $selected>" . $row['name'] . "</option>";
					}
				} else {
					echo "<option value=''>No options available</option>";
				}

				// Close connection
				$conn->close();
				?>
            </select>

            <label for="sealLayer">Seal Layer Material:</label>
            <select id="sealLayer" name="sealLayer">
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

				// Fetch data from database
				$sql = "SELECT name FROM materials WHERE layers LIKE '%s%'";
				$result = $conn->query($sql);

				// Populate select input with data
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$selected = ($row['name'] == 'ptfe') ? "selected" : "";
						echo "<option value='" . $row['name'] . "' $selected>" . $row['name'] . "</option>";
					}
				} else {
					echo "<option value=''>No options available</option>";
				}

				// Close connection
				$conn->close();
				?>
            </select>

            <label for="burstPressure">Burst Pressure:</label>
            <input type="number" id="burstPressure" name="burstPressure" step="0.5" min="0" value="5" required>

            <label for="burstTemperature">Burst Temperature:</label>
            <input type="number" id="burstTemperature" name="burstTemperature" step="1" min="-270" value="25">

            <label for="qty">Quantity:</label>
            <input type="number" id="qty" name="qty" step="1" min="1" value="1">

            <input type="hidden" id="user-ip" name="user-ip" value="">

            <div class="button-group">
                <button type="button" onclick="submitFormWithLoading('./rupture_request_handler.php', 'Searching')">Thickness</button>
                <button type="button" onclick="submitFormWithLoading('new_rupture_data_form.php', 'Calculation')">Save</button>
                <button type="button" onclick="submitFormWithLoading('rupture_drw_handler.php', 'Verification')">Laser</button>
            </div>
        </form>
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <div class="spinner-text" id="spinnerText"></div>
        </div>
    </div>

    <script>
        async function getUserIP() {
            const response = await fetch('https://api.ipify.org?format=json');
            const data = await response.json();
            document.getElementById('user-ip').value = data.ip;
        }
        getUserIP();

        function submitFormWithLoading(action, text) {
            const spinnerText = document.getElementById('spinnerText');
            const loadingContainer = document.getElementById('loading');
            const form = document.getElementById('ruptureForm');

            spinnerText.textContent = text;
            loadingContainer.style.display = 'block';

            setTimeout(() => {
                loadingContainer.style.display = 'none';
                form.action = action;
                form.submit();
            }, 1100);
        }
    </script>
</body>
</html>
