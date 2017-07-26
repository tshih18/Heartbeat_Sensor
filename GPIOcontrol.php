<!DOCTYPE html>
<html>

<head>
<title>Control Center</title>
</head>

<body>
<header>
	<h1>Control Center</h1>
<?php
	include "GLOBAL.php";
?>



</header>
<?php
//Set pin numbers to colors.
$LED_red = 24;
$LED_green = 23;
$LED_blue = 18;

//Create GPIO objects for each color.
$red = new GPIO($LED_red, "out");
$green = new GPIO($LED_green, "out");
$blue = new GPIO($LED_blue, "out");

//If red/green/blue button is pressed turn them on/off.
if(isset($_POST['LEDRed'])) { 
	$red->toggle();
}
if(isset($_POST['LEDGreen'])) {
	$green->toggle();
}
if(isset($_POST['LEDBlue'])) {
	$blue->toggle();
}
//If LEDOff is button is pressed turn all LEDs off.
if(isset($_POST['LEDOff'])) {
	$red->write(0);
	$green->write(0);
	$blue->write(0);
}

?>

<!-- Instructions for how to interact with the LEDS -->
<p>
The buttons below control each color LEDs on the breadboard, Red, Green, Blue. Pressing each one will toggle the respective LED on and off. Feel free to play with the buttons below!!
</p>

<center> <!-- centers everything on the page -->

<!-- Create a whole form for each LED/button -->
<form method="post"> <!-- red form -->
	<!-- PHP here changes the color of the button depending on whether the LED is ON or OFF -->
	<input type="submit" class="button_colors" id="red" name="LEDRed" value="Red"
	 style="background-color:<?php echo ($red->read()?"red":"grey"); ?>">
</form>
<form method="post"> <!-- green form -->
	<input type="submit" class="button_colors" id="green" name="LEDGreen" value="Green"
	style="background-color:<?php echo ($green->read()?"green ":"grey"); ?>">
</form>
<form method="post"> <!-- blue form -->
	<input type="submit" class="button_colors" id="blue" name="LEDBlue" value="Blue"
	style="background-color:<?php echo ($blue->read()?"blue ":"grey"); ?>">
</form>
<form method="post"> <!-- LEDOff form -->
	<input type="submit" class="button_colors" id="colors" name="LEDOff" value="Turn All LED OFF">
</form> <br> <br>

</center> <!-- END center -->

</body>
</html>
