import pymongo	#import MongoDB functions

def main():	#Contains everything that I want to run
	db = connectMongo()	#connects to MongoDB database
	data = db.data		#connects to 'data' collection in the database

	x = 7			#sets 'x' to random value '7'
	new_data = {		#set what I want the document to have
		'type': 'heartdata',	#set type to heartdata
		'name': 'heartdata', 	#set name to heartdata
		'heartrate': x		#set heartrate to the random value of x	
	}
	data.insert_one(new_data)	#Insert this new document into 'data' collection

def connectMongo():	#connects to MongoDB in mlab
	#makes connection to mlab client
	connection = pymongo.MongoClient("mongodb://admin:admin@ds061454.mlab.com:61454/simonpi")
	db = connection.simonpi	#connects to 'simonpi' database in the connection
	return db		#returns the connection to 'simonpi'

if (True):	#run the main function
	main()
