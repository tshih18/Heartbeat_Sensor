import pymongo		#imports the MongoDB functions
import time		#imports time functions
import serial		#imports serial functions

def main():	#function that contains all everything I want to run
	db = connectMongo()			#connects to Mongo database
	data = db.data				#conencts to 'data' collection
	ser = serial.Serial('/dev/ttyACM0', 9600)#chooses serial port and sets baud rate

	while True:	#run forever			
		#reads from the serial port and strips off '\r\n' characters
		#Ex: '45\r\n' -> '45'
		x = ser.readline().rstrip('\r\n')
			
		print x				#prints num to screen
		data.update_one(		#updates one document in 'data' collection
			{"type": "heartdata"},	#chooses document that has this entry
			{
				"$set": {	#updates the value of heartrate to x
					"heartrate": x
				},
				"$currentDate": {"lastModified": True} #updates the date
			}
		)


def connectMongo(): #function to connect to MongoDB in mlab
	#creates a connection to mlab with the database 'simonpi'
	connection = pymongo.MongoClient("mongodb://admin:admin@ds061454.mlab.com:61454/simonpi")
	db = connection.simonpi #connects to the 'simonpi' database in the connection
	return db		#returns the connection to the above ^

if (True):	#run the main function
	main()
