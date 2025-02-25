<!DOCTYPE html>
<html>
<head>
    <title>New Rupture Burst Test</title>
    <script>
        function fetchData(url, elementId) {
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById(elementId);
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item;
                        option.textContent = item;
                        select.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchData('material_handler.php?table=type', 'type');
            fetchData('material_handler.php?table=element_raw_size', 'size');
            fetchData('material_handler.php?table=material&db=koaliumi_rupturium_db', 'main_material');
            fetchData('material_handler.php?table=material&db=koaliumi_rupturium_db', 'vacuum_material');
            fetchData('material_handler.php?table=material&db=koaliumi_rupturium_db', 'seal_material');
        });
    </script>
</head>
<body>
    <h1>New Rupture Burst Test</h1>
    <form action="rupture_burst_data_handler.php" method="post">
        <label for="type">Type:</label>
        <select id="type" name="type"></select><br>

        <label for="size">Size:</label>
        <select id="size" name="size"></select><br>

        <label>Layers:</label>
        <div>
            <?php
            $layers = ['main', 'vacuum', 'seal'];
            foreach ($layers as $layer) {
                echo "<h3>".ucfirst($layer)."</h3>";

                echo "<label for='{$layer}_material'>Material:</label>";
                echo "<select id='{$layer}_material' name='{$layer}_material'></select><br>";

                echo "<label for='{$layer}_thickness'>Thickness:</label>";
                echo "<input type='number' id='{$layer}_thickness' name='{$layer}_thickness' step='0.05' min='0.05' max='5.5'><br>";
            }
            ?>
        </div>

        <label for="burst_temp">Burst Temperature:</label>
        <input type="number" id="burst_temp" name="burst_temp" step="0.1" min="0.01" max="221"><br>

        <label for="burst_temp_range">Burst Temperature Range:</label>
        <input type="number" id="burst_temp_range" name="burst_temp_range" step="1" min="-270" max="550"><br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
