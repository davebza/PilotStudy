<?php
#The user is redirected here from login.php
session_start();

#if there is no session set, redirect to loginIndex.php
if (!isset($_SESSION['agent']) OR ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'] ) )) {
	
	#need the functions to create an absolute URL:
	require_once ('loginFunctions.php');
	$url = absoluteUrl();
	header("Location: $url");
	exit();#exit the script
}

# check if the counter is intialized and init if not:
if(!isset($_SESSION['counter'])) {
	$_SESSION['counter'] = 0;
}

// if button is pressed, increment counter
if(isset($_POST['button'])) {
	++$_SESSION['counter'];
}
// reset counter when five words are done, so we can do them again if needed.
if($_SESSION['counter'] > 4) {
	$_SESSION['counter'] = 0;
}

#Set the page title and include the html header:
$PageTitle = "Hello {$_SESSION['firstName']}!";

include_once ('header.php');
include('masterVariables.php');

# Code goes here! 
echo "<h1>Now write the word you just saw and heard!</h1>";

$file = $_SESSION['file'];

?>
<p>
<fieldset>
	<form>
	<h2>Listen to the word again:</h2>
		<audio controls autoplay>
 			<source src=<?php echo "\"$file\" "; ?> type="audio/mpeg"\>
			Your browser does not support the audio element.
			</source>
		</audio>
	</form>
</fieldset>
</p>

<p>
<fieldset>
	<form action = "wordTest.php" method = "post">
		<h1>Write the word here: <input type = "text" 
		name = "studentAttempt"
		id = "studentAttempt" 
		autocomplete="off" 
		autofocus
		 /></h1>
		 
		<input type="hidden" name="counter" value="<?php echo $_SESSION['counter'];?>"/>
    		<input type="submit" class = "button orange" name="button" value="Next Word" />
    		<!-- <br/><?php echo $_SESSION['counter']; ?> -->
    		
	</form>			
</fieldset>
</p>

<?php
include_once 'footer.html';
?>