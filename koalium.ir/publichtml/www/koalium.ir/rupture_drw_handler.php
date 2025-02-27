<?php
header('Content-Type: text/html; charset=UTF-8');
// Database connection details
$servername = 'localhost';
$username = 'koaliumi_editor'; // Replace with your database username
$password = 'koala551364'; // Replace with your database password
$dbname = 'koaliumi_rupturium_db';
$conn = new mysqli($servername, $username, $password, $dbname);
// Get all the POST data
$size = $_POST['size'];
$type = $_POST['type'];
$qty_i = (int)$_POST['qty'];
//
$size_query = "SELECT * FROM element_raw_size WHERE element = 'rupture' AND type= '$type' AND size = '$size'" ;
$result_s = $conn->query($size_query);
$row_s = $result_s->fetch_assoc();
$diameter=$row_s['do']*0.25;
//
$size_query_q = "SELECT * FROM overtotest WHERE az <= $qty_i AND ta >= $qty_i " ;
$result_q = $conn->query($size_query_q);
$row_q = $result_q->fetch_assoc();
$qty_t=(int)((int)($row_q['kam'])+(float)($row_q['dar'])*(int)($qty_i));
$qty_d=3;
$qty_m=+(int)($qty_t)+(int)($qty_i);
$qty_total=(int)($qty_t)+(int)($qty_d)+(int)($qty_i);


// Search for the element with the closest burst pressure


// Generate the HTML content
$htmlContent = "<!DOCTYPE html>
<html>
<head>

    <title>Dynamic Form and Canvas</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: left;
            margin: 20px;
            font-family: Arial, sans-serif;
        }
        form {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }
        input[type='number'], select {
            padding: 8px;
            font-size: 14px;
			
        }
        button {
            padding: 8px 12px;
            font-size: 14px;
            cursor: pointer;
        }
        canvas {
            border: 2px solid #000;
            background-color: darkblue;
            margin: 2px;
			width: 100%;
            height: auto;
        }
        footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #555;
        }
		/* Media query to rotate the canvas on mobile devices */
        @media only screen and (max-width: 600px) {
            canvas {
				margin-top: 65px;
                transform: rotate(90deg);
                width: auto;
                height: 100%;
            }
			form {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 50px;
        }
			button {
				padding: 8px 12px;
				font-size: 14px;
				cursor: pointer;
			}
        }
    </style>
</head>
<body>
    <form id='dynamic-form' >
	    
        <input type='number' id='size' name='size' min='0.5' max='10' step='0.5' value='2' hidden>
		<label for='qty' align='right' font-style='14,bold' hidden>Qty:</label>
        <input type='number' id='qty' name='qty' value='10'  size='5' hidden>
        <input type='number' id='desqty' name='desqty' value='3'  hidden>
        <input type='number' id='testqty' name='testqty' value='4'  hidden>
        <select id='stagger' name='stagger'  hidden='true'>
            <option value='inline'>Inline</option>
            <option value='triangle' selected >Triangle</option>
        </select>
		<button type='button' onclick='laserView()'>Laser View</button>
        <button type='button' onclick='sendData()'>Send</button>
        <button type='button' onclick='clearCanvas()'>Clear View</button>
        
    </form>
    <canvas id='canvas1' width='1000' height='500'></canvas>
    <canvas id='canvas2' width='1000' height='500' style='display: none;'></canvas>
    <footer>&copy; Koalium Ltd</footer>

    <script>
        function sendData() {
            const form = document.getElementById('dynamic-form');
            const data = new FormData(form);
            fetch('https://tools.koalium.ir/tool/form_handler.php', {
                method: 'POST',
                body: data
            }).then(response => {
                if(response.ok) {
                    alert('Data sent successfully!');
                }
            });
        }

        function clearCanvas() {
            const canvas1 = document.getElementById('canvas1');
            const canvas2 = document.getElementById('canvas2');
            const ctx1 = canvas1.getContext('2d');
            const ctx2 = canvas2.getContext('2d');
            ctx1.clearRect(0, 0, canvas1.width, canvas1.height);
            ctx2.clearRect(0, 0, canvas2.width, canvas2.height);
        }

        function laserView() {
            clearCanvas();
            const qty = ".$qty_i.";
            const stagger = document.getElementById('stagger').value;
            const totalCircles = ".$qty_total.";
            const canvas1 = document.getElementById('canvas1');
            const ctx1 = canvas1.getContext('2d');
            const canvas2 = document.getElementById('canvas2');
            const ctx2 = canvas2.getContext('2d');
            const radius = parseInt(".$diameter.");
            const diameter = radius * 2;
            const xseperation = 5;
            const yseperation = 5;
            const margin = 5;
            const edgeMargin = 12;

            let circleCount = 0;

            function drawCircle(ctx, x, y, outlineColor, text) {
                ctx.beginPath();
                ctx.arc(x, y, radius, 0, 2 * Math.PI, false);
                ctx.lineWidth = 3;
                ctx.strokeStyle = outlineColor;
                ctx.stroke();
                ctx.fillStyle = 'darkgreen';
                ctx.fill();
                ctx.fillStyle = 'white';
                ctx.font = '12px Arial';
                ctx.fillText(text, x - radius / 2, y + radius / 4);
            }

            function animateCircles(ctx) {
                let col = 0;
                let row = 0;
                let canvasIndex = 1;
                const midleqty = parseInt(".$qty_m.") ;
                function drawNextCircle() {
                    if (circleCount < totalCircles) {
                        const x = edgeMargin + col * (diameter + xseperation) *0.86+ radius;
                        let y = edgeMargin + row * (diameter + yseperation) + radius + margin;

                        if (stagger === 'triangle' && col % 2 !== 0) {
                            y += (radius + yseperation/2);
                        }

                        let outlineColor = 'green';
                        if ( circleCount < qty ) {
                            outlineColor = 'yellow';
                        } else {
                            if ( circleCount < midleqty) {
                            outlineColor = 'cyan';
                            } else {
                            outlineColor = 'magenta';
                            }
                        }

                        drawCircle(ctx, x, y, outlineColor, circleCount + 1);

                        row++;
                        if (y + diameter + yseperation +radius> canvas1.height - edgeMargin) {
                            row = 0;
                            col++;
                            if (x + diameter + edgeMargin > canvas1.width - edgeMargin) {
                                canvasIndex++;
                                if (canvasIndex > 2) {
                                    return;
                                }
                                ctx = document.getElementById('canvas' + canvasIndex).getContext('2d');
                                row = 0;
                                col = 0;
                            }
                        }

                        circleCount++;
                        setTimeout(drawNextCircle, 200);
                    }
                }

                drawNextCircle();
            }

            animateCircles(ctx1);
        }
    </script>
</body>
</html>
"

// Send the generated HTML content to the client


?>
<?
echo "$htmlContent\n";?>