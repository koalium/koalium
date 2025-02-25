from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route('/rupture_request_handler', methods=['POST'])
def rupture_request_handler():
    # Get JSON data from the request
    data = request.json

    # Validate the data
    if not data:
        return jsonify({'error': 'No data received'}), 400

    # Extract fields from the JSON data
    type = data.get('type')
    size = data.get('size')
    main_layer = data.get('mainLayer')
    sub_layer = data.get('subLayer')
    seal_layer = data.get('sealLayer')
    burst_pressure = data.get('burstPressure')
    burst_temperature = data.get('burstTemperature')
    action = data.get('action')

    # Validate required fields
    if not all([type, size, main_layer, sub_layer, seal_layer, burst_pressure, burst_temperature, action]):
        return jsonify({'error': 'Missing required fields'}), 400

    # Process the data (e.g., save to database, perform calculations, etc.)
    # For now, we'll just return the received data as a response
    response = {
        'status': 'success',
        'message': 'Data received successfully',
        'data': {
            'type': type,
            'size': size,
            'main_layer': main_layer,
            'sub_layer': sub_layer,
            'seal_layer': seal_layer,
            'burst_pressure': burst_pressure,
            'burst_temperature': burst_temperature,
            'action': action
        }
    }

    return jsonify(response), 200

if __name__ == '__main__':
    app.run(debug=True)