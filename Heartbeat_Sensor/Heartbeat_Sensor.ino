#include <Time.h>     //import time library
#define sensorPin 0   //define AO pin as the sensor pin
#define period 100    //time in milliseconds  //How often we will measure from the sensor
#define sampleTime 10 //time in seconds       //How long our sample time is
#define minCutOff 20                          //Cut off so we don't detect the lower peak
#define numSamples (sampleTime * 1000 / period)//Caculates num of samples we need
#define adjustment 2                          //Adjusts the heartrate number to match
                                              //the reading from sensor on phone.
//initialize global variables
int lastBeats[numSamples];  //tracks if there was beat in that sample. 1 if yes, 0 if no
int i;
int preValueOne = 0;
int preValueTwo = 0;
void setup () //Stuff that will be run once at the beginning
{
  Serial.begin (9600);              //Starts the serial port and sets the baud rate to 9600
  for(i = 0; i < numSamples; i++){  //intializes array to 0's
    lastBeats[i] = 0;
  }
}
void loop () //Stuff that will be run over and over
{
    //initalies variables
    static int numLoop = 0;   //tracks num of loops
    
    int isGreatThanCutOff = 0;//tracks if greater than cut off
    int isPeak = 0;           //tracks if there is a peak
    int beat = 0;             //tracks if there is a beat
    int beatPerMin = 0;       //tracks beats per min
    
    for(i = numSamples-2; i >= 0; i--)  //shifts all values back by one
      lastBeats[i+1] = lastBeats[i];    //so, the value numsamples ago is deleted
    
    int rawValue = analogRead (sensorPin);  //reads from the sensor
    int currentValue = rawValue;            //sets the value to the current value

    if(currentValue > minCutOff)            //if this value is greater than 20
      isGreatThanCutOff = 1;                //set cutoff tracker to 1
    
    if(numLoop >= 2)                        //if more than 2 loops, check for beats
      if((preValueOne > preValueTwo) && (preValueOne > currentValue)) //checks for peak
        isPeak = 1;     
    if(isGreatThanCutOff && isPeak){ //if there is a peak and above cutoff
      lastBeats[0] = 1;              //tells array that there was a beat 
      beat = 1;
    }
    else
      lastBeats[0] = 0;              //if no beat set most current point in array to 0
    
    int sum = 0;                    //sum of beats
    for(i = 0; i < numSamples; i++) //goes through beat tracking array
      sum += lastBeats[i];          //and sums all the instances of a beat

    beatPerMin = sum * (60/sampleTime) * adjustment;    //Calculates Beats Per Min
                                                        //and accounts for error
    preValueTwo = preValueOne;    //sets the 2nd most recent value to the 1st most recent
    preValueOne = currentValue;   //sets the 1st most recent value to the current value
    numLoop++;                    //increases numLoops to track number of loops

    Serial.println(beatPerMin); //Sends the heartrate value over serial
    
    delay (period); //stops everything for 100ms
}

