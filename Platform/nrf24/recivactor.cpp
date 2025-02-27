#include <SPI.h>
#include <nRF24L01.h>
#include <RF24.h>
#include <SD.h>

// Define the CE and CSN pins
#define CE_PIN 9
#define CSN_PIN 10

// Create an instance of the RF24 class
RF24 radio(CE_PIN, CSN_PIN);

// Define the address for communication
const byte address[6] = "00001";

// SD card chip select pin
#define SD_CS_PIN 4

// Structure to hold the received data
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
  radio.openReadingPipe(0, address);

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
  // Check if there is data available to read
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

    // Log the data to the SD card
    logDataToSD(data);
  }
}

void logDataToSD(DataPacket data) {
  // Open the log file (create if it doesn't exist)
  File file = SD.open("log.txt", FILE_WRITE);

  if (file) {
    // Write the data to the file
    file.print("ADC: ");
    file.print(data.adcValue);
    file.print(", Door1: ");
    file.print(data.door1State ? "Open" : "Closed");
    file.print(", Door2: ");
    file.println(data.door2State ? "Open" : "Closed");

    // Close the file
    file.close();
    Serial.println("Data logged to SD card.");
  } else {
    Serial.println("Error opening log file.");
  }
}