#include <SPI.h>
#include <nRF24L01.h>
#include <RF24.h>

RF24 radio(7, 8); // CE, CSN
const byte address[6] = "00001"; // Address for communication

struct DataPacket {
  int adcValue;
  bool door1State;
  bool door2State;
};

DataPacket data;

void setup() {
  Serial.begin(9600);
  radio.begin();
  radio.openWritingPipe(address);
  radio.setPALevel(RF24_PA_MIN);
  radio.stopListening();
  
  pinMode(2, INPUT_PULLUP); // Door 1 switch
  pinMode(3, INPUT_PULLUP); // Door 2 switch
}

void loop() {
  data.adcValue = analogRead(A0); // Read ADC value
  data.door1State = digitalRead(2) == LOW; // Door 1 state (LOW means pressed, i.e., door open)
  data.door2State = digitalRead(3) == LOW; // Door 2 state (LOW means pressed, i.e., door open)

  Serial.print("ADC Value: ");
  Serial.print(data.adcValue);
  Serial.print(" Door 1: ");
  Serial.print(data.door1State ? "Open" : "Closed");
  Serial.print(" Door 2: ");
  Serial.println(data.door2State ? "Open" : "Closed");

  radio.write(&data, sizeof(data));
  delay(1000); // Send data every second
}
