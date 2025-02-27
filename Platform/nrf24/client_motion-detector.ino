/*



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
const byte serverAddress[6] = "00003";

// Pin for the PIR sensor
#define PIR_PIN 2

// Structure to hold the motion data
struct MotionData {
  bool motionDetected;
};

// Variable to store the previous state of the PIR sensor
bool prevMotionState = false;

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
  radio.openWritingPipe(serverAddress);

  // Set the module as a transmitter
  radio.stopListening();

  // Set PIR sensor pin as input
  pinMode(PIR_PIN, INPUT);
}

void loop() {
  // Read the current state of the PIR sensor
  bool currentMotionState = digitalRead(PIR_PIN);

  // Check if the motion state has changed
  if (currentMotionState != prevMotionState) {
    // Create a motion data packet
    MotionData motionData;
    motionData.motionDetected = currentMotionState;

    // Send the motion data to the server
    bool success = sendMotionData(motionData);

    // Print the result to the serial monitor
    if (success) {
      Serial.print("Motion state changed: ");
      Serial.println(motionData.motionDetected ? "Motion Detected" : "No Motion");
    } else {
      Serial.println("Failed to send motion data.");
    }

    // Update the previous motion state
    prevMotionState = currentMotionState;
  }

  // Small delay to avoid flooding the server with data
  delay(100);
}

bool sendMotionData(MotionData motionData) {
  // Retry mechanism
  bool success = false;
  unsigned long startTime = millis();
  const unsigned long retryTimeout = 300; // Retry timeout in milliseconds

  while (!success && (millis() - startTime < retryTimeout)) {
    // Send the motion data to the server
    success = radio.write(&motionData, sizeof(motionData));

    if (!success) {
      delay(50); // Small delay before retrying
    }
  }

  return success;
}