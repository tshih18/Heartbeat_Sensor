<!DOCTYPE html>
<html>
<head>
<title>Heart Rate Data</title>
</head>

<body>
<header>
	<h1>Heart Rate Data</h1>
<?php
	include "GLOBAL.php";
?>

</header>

<div class="table"> <!-- div for the table -->
	<table> <!-- table for all the heart rate data -->
	<!-- caption gives the table a title that appears above it and centered -->
	<caption class="table_caption">Heart Rate Data for Everyone</caption>

	<tr id="table_headers"> <!-- First row of the table that contains the labels -->
		<td>Date</td>		<!-- label for column 1 -->
		<td>Name</td>		<!-- label for column 2 -->
		<td>Age</td>		<!-- label for column 3 -->
		<td>Weight (lbs)</td>	<!-- label for column 4 -->
		<td>Gender</td>		<!-- label for column 5 -->
		<td>State</td>		<!-- label for column 6 -->
		<td>Heart Rate (BPM)</td> <!-- label for column 7 -->
	</tr> <!-- END #table_headers row -->

	<?php
		$db = connectMongo();			// Connect to MongoDB in mLab
		$collection = $db->data;		// Connect to 'data' collection
		$query = array('type' => 'user');	// What entries I am looking for
		$cursor = $collection->find($query);	// Find all docs in 'data' that match
							// the query.
		foreach ($cursor as $doc) {		// For each matching doc do this
	?>

	<tr>						<!-- echoes all the data for each doc in a row -->
		<td><?php echo $doc['date']; ?></td>
		<td><?php echo $doc['name']; ?></td>
		<td><?php echo $doc['age']; ?></td>
		<td><?php echo $doc['weight']; ?></td>
		<td><?php echo $doc['gender']; ?></td>
		<td><?php echo $doc['state']; ?></td>
		<td><?php echo $doc['heartrate']; ?></td>

	</tr>						<!-- ends the row for this data -->

	<?php
		} //ends the foreach code block
	?>

	</table><br><br> <!-- END table -->


<!-- LINE GRAPH -------------------------------------------------------------->

	<h3>Heart Rate vs. Date</h3><br> <!-- title of line graph -->
	<!-- Creates a box for the line-graph to go -->
	<canvas id="line_graph" width="800" height="225"></canvas>
<?php	
	// Configure data for line graph
	$cursor = $collection->find(array('type' => 'user'));	//Find all docs of type user
	$cursor = $cursor->sort(array('date' => -1));		//Sort by date ascending
	$limCursor = $cursor->limit(24);			//Limits entries to 24
	$line_lables = "";					//Creates empty variable
	$line_data = "";					//Creates empty variable

	//For each document, take the date and heartrate value
	//And add each to a list in line_labels and line_data respectively
	foreach ($limCursor as $doc) {
		//Gets date and splits it by " ", also chooses the second half of the split.
		//In an array the 1st is [0] and 2nd is [1] and so on: 6th is [5]
		$date = split(" ", $doc['date'], 2)[1];	//27-11-2016 14:37:46 -> 14:37:46
		$val = $doc['heartrate'];		//Gets heartrate
		$line_labels = '"'. $date.		//Appends date to start of line_label
			       '",' . $line_labels;	//Ex: "date","date-1","date-2"
		$line_data = $val . "," . $line_data;	//Appends heartrate to line_data
							//Ex: heartrate,heartrate-1,heartrate
	}
?>
<script>
	//We input all the data we gathered in PHP and import it into javascript
	var line_data = {
		labels : [<?php echo $line_labels; ?>],	//Sets labels to $line_labels
		datasets : [
		{
			fillColor : "rgba(244,66,66,0.4)", //under graph red with 40% opacity
			strokeColor : "red",		   //line is red
			pointColor : "black",		   //points are black 
			pointStrokeColor : "black",	   //outline of points are black
			data : [<?php echo $line_data; ?>] //insert heart rate data
		}

		]
	}
	//Finds the canvas we created earlier for the line graph, and tells it that it is 2D
	var line_graph = document.getElementById('line_graph').getContext('2d');

	//Creates new chart that is a line graph, using the canvas and line_data we gathered
	new Chart(line_graph).Line(line_data);
</script>

</body>
</html>
	
