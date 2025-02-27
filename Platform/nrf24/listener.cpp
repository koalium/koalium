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
  radio.openReadingPipe(0, address);

  // Set the module as a receiver
  radio.startListening();
}

void loop() {
  // Check if there is data available to read
  if (radio.available()) {
    // Variable to store the received ADC value
    int adcValue = 0;

    // Read the data
    radio.read(&adcValue, sizeof(adcValue));

    // Print the received value to the serial monitor
    Serial.print("Received: ");
    Serial.println(adcValue);
  }
}