/*Server Code (Receiver with SD Card Logging and Command Sending to Second Client)
This code includes a retry mechanism to ensure the command is sent successfully to the second client.*/

#include <SPI.h>
#include <nRF24L01.h>
#include <RF24.h>
#include <SD.h>

// Define the CE and CSN pins
#define CE_PIN 9
#define CSN_PIN 10

// Create an instance of the RF24 class
RF24 radio(CE_PIN, CSN_PIN);

// Define the address for communication with the first client
const byte firstClientAddress[6] = "00001";

// Define the address for communication with the second client
const byte secondClientAddress[6] = "00002";

// SD card chip select pin
#define SD_CS_PIN 4

// Structure to hold the received data from the first client
struct DataPacket {
  int adcValue;
  bool door1State;
  bool door2State;
};

// Structure to hold the command to be sent to the second client
struct CommandPacket {
  bool turnOnRelays;
};

// Variables to store the previous state of the switches
bool prevDoor1State = HIGH;
bool prevDoor2State = HIGH;

// Retry timeout in milliseconds
const unsigned long retryTimeout = 300;

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

  // Set the address for communication with the first client
  radio.openReadingPipe(0, firstClientAddress);

  // Set the address for communication with the second client
  radio.openWritingPipe(secondClientAddress);

  // Set the module as a receiver
  radio.startListening();

  // Initialize the SD card
  if (SD.begin(SD_CS_PIN)) {
    Serial.println("SD card initialized.");
  } else {
    Serial.println("SD card initialization failed.");
    while (1);
  }
}

void loop() {
  // Check if there is data available to read from the first client
  if (radio.available()) {
    // Variable to store the received data
    DataPacket data;

    // Read the data
    radio.read(&data, sizeof(data));

    // Print the received data to the serial monitor
    Serial.print("Received - ADC: ");
    Serial.print(data.adcValue);
    Serial.print(", Door1: ");
    Serial.print(data.door1State ? "Open" : "Closed");
    Serial.print(", Door2: ");
    Serial.println(data.door2State ? "Open" : "Closed");

    // Check if the switch states have changed
    if (data.door1State != prevDoor1State || data.door2State != prevDoor2State) {
      // Log the change to the SD card
      logDataToSD(data);

      // Update the previous states
      prevDoor1State = data.door1State;
      prevDoor2State = data.door2State;
    }

    // Check if WTF=ON (example condition)
    if (data.door1State == LOW && data.door2State == LOW) { // Example condition for WTF=ON
      // Send a command to the second client to turn on the relays
      sendCommandToSecondClient(true);
    } else {
      // Send a command to the second client to turn off the relays
      sendCommandToSecondClient(false);
    }
  }
}

void sendCommandToSecondClient(bool turnOnRelays) {
  // Create a command packet
  CommandPacket command;
  command.turnOnRelays = turnOnRelays;

  // Retry mechanism
  bool success = false;
  unsigned long startTime = millis();

  while (!success && (millis() - startTime < retryTimeout)) {
    // Send the command to the second client
    radio.stopListening();
    success = radio.write(&command, sizeof(command));
    radio.startListening();

    if (success) {
      Serial.println("Command sent successfully.");
    } else {
      Serial.println("Failed to send command. Retrying...");
      delay(50); // Small delay before retrying
    }
  }

 