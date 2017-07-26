<?php
	//Everything in this file will be included in all of our webpages, hence GLOBAL.php

	//Function available everywhere, so that we can access our database
	function connectMongo() {
		//Connects to our MongoDB account
		$connection = new MongoClient("mongodb://admin:admin@ds061454.mlab.com:61454/simonpi");
		$db = $connection->simonpi; //Connects to the database, simonpi, on the account
		return $db;
	}

	//Contains navbar, basic layout of webpage, links to CSS, javascript, and icon	
	include "navbar.html";

	//Contains all the functions needed to read from and write to the GPIO pins
	include "GPIO.php";

	//Sets the timezone for the website to California
	date_default_timezone_set("America/Los_Angeles");

?>
