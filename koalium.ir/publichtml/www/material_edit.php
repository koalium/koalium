<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            height: 100vh;
        }
        header {
            width: 100%;
            background-color: #26a69a;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .form-container {
            width: 80%;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Page Header</h1>
    </header>
    <div class="form-container">
		<h2>Material Editor</h2>
        <form method="POST" action="submit_form.php" class="col s12">
            <div class="row">
                <div class="input-field col s12">
                    <input id="first_name" name="first_name" type="text" class="validate" required>
                    <label for="first_name">name (EN)</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input id="second_name" name="second_name" type="text" class="validate" required>
                    <label for="second_name">name (Fa)</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input id="density" name="density" type="number" step="0.01" class="validate" required>
                    <label for="density">Density (gr/cm3):</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input id="price" name="price" type="number" step="0.01" class="validate" required>
                    <label for="price">Price per unit (local):</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <textarea id="description" name="description" class="materialize-textarea" required></textarea>
                    <label for="description">Description</label>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <button class="btn waves-effect waves-light" type="submit" name="action">Submit
                        <i class="material-icons right">send</i>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <canvas id="myCanvas" width="800" height="400"></canvas>
    <script>
        const canvas = document.getElementById('myCanvas');
        const ctx = canvas.getContext('2d');
        
        let circleIndex = 0;
        const circles = [
            {x: 100, y: 100, radius: 50, color: 'red'},
            {x: 200, y: 200, radius: 30, color: 'blue'},
            {x: 300, y: 150, radius: 40, color: 'green'},
            // Add more circles as needed
        ];
        
        function drawCircle() {
            if (circleIndex < circles.length) {
                const circle = circles[circleIndex];
                ctx.beginPath();
                ctx.arc(circle.x, circle.y, circle.radius, 0, 2 * Math.PI);
                ctx.fillStyle = circle.color;
                ctx.fill();
                circleIndex++;
                setTimeout(drawCircle, 1000); // Draw a circle every second
            }
        }

        drawCircle();
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>
