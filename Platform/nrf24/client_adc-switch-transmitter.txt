/*

Client Code (Transmitter)
This code reads ADC data and door switch states, sends them to the server, and listens for commands from the server (e.g., to turn on a device).

*/
#include <SPI.h>
#include <nRF24L01.h>
#include <RF24.h>

// Define the CE and CSN pins
#define CE_PIN 9
#define CSN_PIN 10

// Create an instance of the RF24 class
RF24 radio(CE_PIN, CSN_PIN);

// Define the address for communication
const byte address[6] = "00001";

// Analog pin for reading data
#define ADC_PIN A0

// Digital pins for door switches
#define DOOR_SWITCH_1 2
#define DOOR_SWITCH_2 3

// Pin for controlling the "ajir" device
#define AJIR_PIN 7

// Structure to hold the data to be sent
struct DataPacket {
  int adcValue;
  bool door1State;
  bool door2State;
};

// Structure to hold the command from the server
struct CommandPacket {
  bool turnOnAjir;
};

void setup() {
  // Initialize serial communication
  Serial.begin(9600);

  // Initialize the NRF24 module
  if (radio.begin()) {
    Serial.println("NRF24 initialized.");
  } else {
    Serial.println("NRF24 initialization failed.");
    while (1);
  }

  // Set the address for communication
  radio.openWritingPipe(address);
  radio.openReadingPipe(1, address); // Open a reading pipe for commands

  // Set the module as a transmitter
  radio.stopListening();

  // Set door switch pins as input
  pinMode(DOOR_SWITCH_1, INPUT_PULLUP);
  pinMode(DOOR_SWITCH_2, INPUT_PULLUP);

  // Set AJIR pin as output
  pinMode(AJIR_PIN, OUTPUT);
  digitalWrite(AJIR_PIN, LOW); // Ensure AJIR is off initially
}

void loop() {
  // Create a data packet
  DataPacket data;

  // Read the analog value from the ADC pin
  data.adcValue = analogRead(ADC_PIN);

  // Read the state of the door switches
  data.door1State = digitalRead(DOOR_SWITCH_1);
  data.door2State = digitalRead(DOOR_SWITCH_2);

  // Send the data packet to the server
  bool success = radio.write(&data, sizeof(data));

  // Print the result to the serial monitor
  if (success) {
    Serial.print("Sent - ADC: ");
    Serial.print(data.adcValue);
    Serial.print(", Door1: ");
    Serial.print(data.door1State ? "Open" : "Closed");
    Serial.print(", Door2: ");
    Serial.println(data.door2State ? "Open" : "Closed");
  } else {
    Serial.println("Failed to send data.");
  }

  // Listen for commands from the server
  radio.startListening();
  if (radio.available()) {
    CommandPacket command;
    radio.read(&command, sizeof(command));

    if (command.turnOnAjir) {
      digitalWrite(AJIR_PIN, HIGH); // Turn on AJIR
      Serial.println("AJIR turned ON by server.");
    } else {
      digitalWrite(AJIR_PIN, LOW); // Turn off AJIR
      Serial.println("AJIR turned OFF by server.");
    }
  }
  radio.stopListening();

  // Wait for a short period before sending the next value
  delay(1000);
}