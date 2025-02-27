/*

Second Client Code (Relay Controller)






*/
#include <SPI.h>
#include <nRF24L01.h>
#include <RF24.h>

// Define the CE and CSN pins
#define CE_PIN 9
#define CSN_PIN 10

// Create an instance of the RF24 class
RF24 radio(CE_PIN, CSN_PIN);

// Define the address for communication with the server
const byte serverAddress[6] = "00002";

// Pins for controlling the relays
#define RELAY1_PIN 7
#define RELAY2_PIN 8

// Structure to hold the command from the server
struct CommandPacket {
  bool turnOnRelays;
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

  // Set the address for communication with the server
  radio.openReadingPipe(0, serverAddress);

  // Set the module as a receiver
  radio.startListening();

  // Set relay pins as output
  pinMode(RELAY1_PIN, OUTPUT);
  pinMode(RELAY2_PIN, OUTPUT);

  // Ensure relays are off initially
  digitalWrite(RELAY1_PIN, LOW);
  digitalWrite(RELAY2_PIN, LOW);
}

void loop() {
  // Check if there is data available to read
  if (radio.available()) {
    // Variable to store the received command
    CommandPacket command;

    // Read the command
    radio.read(&command, sizeof(command));

    // Control the relays based on the command
    if (command.turnOnRelays) {
      digitalWrite(RELAY1_PIN, HIGH);
      digitalWrite(RELAY2_PIN, HIGH);
      Serial.println("Relays turned ON.");
    } else {
      digitalWrite(RELAY1_PIN, LOW);
      digitalWrite(RELAY2_PIN, LOW);
      Serial.println("Relays turned OFF.");
    }
  }
}