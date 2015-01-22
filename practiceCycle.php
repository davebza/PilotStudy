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
	 ?><script = "text/javascriptÓ>
	 	self.location="wordPractice.php";
	 	</script><?php
}

#Set the page title and include the html header:
$PageTitle = "Hello {$_SESSION['firstName']}!";

include_once ('header.php');
include('masterVariables.php'); 
 
echo "<h1> Hi, {$_SESSION['firstName']}. Let's learn today's new words.</h1>";

#make an array to store the five words, then randomly display and play the sound file. Work on the css to make it more like a flashcard.
#make array from function makeSightArray in masterVariables:
$sightArray = makeSightArray($dbc, $studentallWordsTable);

#Display the words one by one and have the sound recorder play it:
$currentWord = $sightArray[$_SESSION['counter']];
echo "<h3>".$currentWord."</h3><p>";

#this file variable links to the available sound files, and needs to be changed to allow for different words:
$file = "soundFiles\\$currentWord.mp3";

?>

<fieldset>
	<form>
		<audio controls autoplay>
 			<source src=<?php echo "\"$file\" "; ?> type="audio/mpeg"\>
			Your browser does not support the audio element.
			</source>
		</audio>
	</form>
</fieldset>

<fieldset>
	<form action = "practiceCycle.php" method = "post">				
		<input type="hidden" name="counter" value="<?php echo $_SESSION['counter'];?>"/>
    		<input type="submit" class = "button orange" name="button" value="Next Word" />
    		<!-- <br/><?php echo $_SESSION['counter']; ?> -->
	</form>			
</fieldset>

<?php

include_once 'footer.html';
?>