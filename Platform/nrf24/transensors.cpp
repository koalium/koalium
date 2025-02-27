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

// Structure to hold the data to be sent
struct DataPacket {
  int adcValue;
  bool door1State;
  bool door2State;
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

  // Set the module as a transmitter
  radio.stopListening();

  // Set door switch pins as input
  pinMode(DOOR_SWITCH_1, INPUT_PULLUP);
  pinMode(DOOR_SWITCH_2, INPUT_PULLUP);
}

void loop() {
  // Create a data packet
  DataPacket data;

  // Read the analog value from the ADC pin
  data.adcValue = analogRead(ADC_PIN);

  // Read the state of the door switches
  data.door1State = digitalRead(DOOR_SWITCH_1);
  data.door2State = digitalRead(DOOR_SWITCH_2);

  // Send the data packet to the receiver
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

  // Wait for a short period before sending the next value
  delay(1000);
}