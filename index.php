<!DOCTYPE html>
<html>

<head>
<title>Red Mango</title>
</head>

<body>
<div class="wrapper">

<header>
	<h1>Red Mango Team</h1>
<?php
	include "GLOBAL.php"; 
	$db = connectMongo();		//Connect to MongoDB at mLab
	$collection = $db->data;	//Connect to 'data' collection
	$heartrate_input = "";		//Initializes this to blank, will store heartrate val
?>

<?php
	//If submit button with name "submit_heartrate" is pressed, do this PHP that
	//should autofill the heartrate in the 'heartrate' input box
	if(isset($_POST["submit_heartrate"])){
		$query = array('type' => 'heartdata');	//Search criteria
		$cursor = $collection->find($query);	//Find all doc containing query
		foreach ($cursor as $doc) {		//For each doc, do this
			$heartrate_input = $doc['heartrate'];	//Get the heartrate
		}

	}
	//NOTE: Even though it says foreach, there is only one document with this type, so
	//that is the only one's heartrate that is taken
?>
</header>

<center> <!-- BEGIN center -->
<h1>Heart Rate Submission Form</h1>

<h2>
Recording Instructions:
</h2>

<!-- Intructions on how to measure heartrate and submit the form -->
<p>
1. Place one of your fingers on the heartrate sensor for at least 10 seconds, so that it can get an accurate measurement.<br>
2. Press the <span style="font-weight: bold;">Measure Heart Rate</span> button below.<br>
3. Fill in all the required information below and press the <span style="font-weight:bold">Record</span> button to submit your data to our database.<br>
4. If successful, a success message will appear at the bottom telling you that whether you are below the average range, in it, or above it. Also the LEDs will light up accordingly: 
<span style="color:blue;">BLUE=BELOW</span>, 
<span style="color:green;">GREEN = AVERAGE</span>, 
<span style="color:red;">RED=ABOVE</span>. <br>
5.You will be able to see your data on the <span style="font-weight: bold">Heart Rate Data</span> page.<br>

<!-- NOTES: I use <span> </span> to bold and color certain parts of the text. -->
</p>

<!-- When this form is submitted it will autofill the heartrate into the 'Heart Rate' box -->
<form name="form_heartrate" method="post"> <!-- the method must be equal to "post" -->
		<input type="submit" class="button_colors" value="Measure Heartrate" name="submit_heartrate"><br>
		<!-- the name for the submit button must be the same as in the -->
		<!-- PHP if statement above, Ex: name="submit" -> $_POST['submit'] -->
</form>

<div class="heartbeat" id="heartbeat"> <!-- BEGIN heartrate div -->
	Heart Rate (BPM): <?php
				$query = array('type' => 'heartdata');	//Search criteria
				$cursor = $collection->find($query);	//Find docs matching
				foreach ($cursor as $doc) {		//For each do this
					echo $doc['heartrate'];		//Get heartrate
				}
			?><br><br> 	
</div> <!-- end heartbeat> -->

<!-- Form so that users can submit their data -->
<form name="form_demographics" method="post">
	*Name:<br>
	<!-- 'required' forces the user to fill in the field in order to submit -->
	<input type="text" name="name" required><br><br><!-- Text box for name -->
	*Age:<br>
	<input type="text" name="age" required><br><br>
	*Weight (lbs):<br>
	<input type="text" name="weight" required><br><br>
	*Heart Rate (BPM):<br>
	<!-- The PHP code in here lets us autofill in the current heartrate -->
	<input type="text" name="heartrate" value="<?php echo $heartrate_input; ?>" required><br><br>
	*Gender:<br>
        <fieldset id="group1" class="radiobuttons" >	<!-- radio buttons for gender -->
	<input type="radio" name="gender" value="male" required> Male
        <input type="radio" name="gender" value="female" required> Female
	<input type="radio" name="gender" value="other" required> Other<br><br>
        </fieldset>
	*State:<br>
  	<fieldset id="group2" class="radiobuttons">
	<input type="radio" name="state" value="resting" required> Resting
  	<input type="radio" name="state" value="exercise" required> Exercise<br><br>
	</fieldset>
	<input type="submit" class="button_colors" value="Record" name="submit_demographics">
</form>

<!--This php enters the form_demographics into the collection data -->
<?php
	//If button with name 'submit_demographics' is pressed, do this PHP
	if (isset($_POST['submit_demographics'])) {
		$newData = array(
				'type' => 'user',
				'date' => date("Y-m-d H:i:s"), //gets current date and time
				//Ex: 27-11-2016 15:43:26
				'name' => $_POST['name'],
				'age' => $_POST['age'],
				'weight' => $_POST['weight'],
				'heartrate' => $_POST['heartrate'],
				'gender' => $_POST['gender'],
				'state' => $_POST['state']
		);
		
		//Inserts $newData as a new document in the collection
		$collection->insert($newData);

		//Creates objects for red, green, and blue LEDs
		//Sets their pin numbers and whether they are input or output
		//For red sets pin to 24 and makes it output		
		$red = new GPIO(24,"out");
		$green = new GPIO(23, "out");
		$blue = new GPIO(18, "out");
		
		//Defines these words as these numbers for healthy range of heartrates
		//REST_LOW = 60
		define("REST_LOW", "60");
		define("REST_HIGH", "100");
		define("ACTIVE_LOW", "100");
		define("ACTIVE_HIGH", "160");

		
		$heartrate = $_POST['heartrate'];	//Gets heartrate from form
		$state = $_POST['state'];		//Gets activity state from form

		//If the state is resting, do this
		if(strcmp($state, "resting") === 0){
			//If heartrate is less than REST_LOW, turn blue on, turn rest off
			if($heartrate < REST_LOW){
				$red->write(0);
				$green->write(0);
				$blue->write(1);
				echo "Your heartrate is lower than the healthy range.";
			}				
			//If heartrate is between REST_LOW and REST_HIGH, green on, rest off
			else if($heartrate < REST_HIGH){
				$red->write(0);
				$green->write(1);
				$blue->write(0);
				echo "Your have a healthy heartrate!!!<3";
			}
			//If heartrate is greater than REST_HIGH, red on, rest off
			else {
				$red->write(1);
				$green->write(0);
				$blue->write(0);
				echo "Your heartrate is higher than the healthy range.";
			}
		}
		//If state is not resting, therefore the state is exercise, do this
		//This is same as above, but ACTIVE_LOW and ACTIVE_HIGH instead fo RESTING
		else {
			if($heartrate < ACTIVE_LOW){
				$red->write(0);
				$green->write(0);
				$blue->write(1);
				echo "Your heartrate is lower than the healthy range.";
			}				
			else if($heartrate < ACTIVE_HIGH){
				$red->write(0);
				$green->write(1);
				$blue->write(0);
				echo "Your have a healthy heartrate!!!<3";
			}
			else {
				$red->write(1);
				$green->write(0);
				$blue->write(0);
				echo "Your heartrate is higher than the healthy range.";
			}
		}
		
		echo "<br>You have successfully submitted your data!<br>";
		//This confirms that their submission was successful
	}
?>
<br><br>


<h2>Deleting Instructions:</h2>

<!-- Instructions on how users can delete entries -->
<p>
Enter the name of the entry you wish to delete and it will delete the first entry that matches that name. WARNING: names are case sensitive.
</p>

<!-- Form with name and submit fields to delete entries matching that name -->
<form name="form_delete" method="post">
	*Name:<br>
	<input type="text" name="name" required><br><br>
	<input type="submit" name="submit_delete" value="Delete" class="button_colors">
</form>

<?php
	//If "submit_delete" button is pressed do this
	if (isset($_POST['submit_delete'])) {
		$query = array('name' => $_POST['name']);	//Search criteria
		$options = array("justOne" => true);		//Delete just one entry
		$collection->remove($query, $options);
		//Delte just one entry that has the same name
	}
?>
</div> <!-- END wrapper -->

</center> <!-- END center -->

</body>
</html>
