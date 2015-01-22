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

#Set the page title and include the html header:
$PageTitle = "Hello {$_SESSION['firstName']}!";
include_once ('header.php');
include('masterVariables.php');

#Start checking if they have the correct answer:
#first, make sure they came from the input page, in order not to record false errors:

if (isset ($_SERVER['HTTP_REFERER']) && ($_SERVER['HTTP_REFERER'] === "http://davidbrownhk.com/sightwords/wordInput.php")) {
	
	#set simple variables for correct answer and student input, sanitize input, convert to lower case and trim the ends of the input:
	#echo "you came from the input page!";
	$correctWord = strtolower($_SESSION['correctWord']);
	$studentAttempt = $_POST['studentAttempt'];
	$studentAttempt = cleanUp($studentAttempt);
	#echo "Correct is ".$correctWord."and student word is ".$studentAttempt;
	
	#if the student has just made an attempt at this word (coming from the wordInput.php page, run the correct check:
	#if correct, update the student's table for this particular word:
	if ($correctWord == $studentAttempt){
	
		$columnWord = $correctWord;
		#echo "The words match";
		#the update query will go here:
		$dbq = "UPDATE `{$databaseVariable}`.`$studentallWordsTable` SET `numberCorrect`= `numberCorrect` + 1
		WHERE `word` = '$columnWord' LIMIT 1;";
	
		$dbr = mysqli_query($dbc, $dbq) or die("Problem updating the numberCorrect column for this user");
	
	}else if ($correctWord != $studentAttempt){
	
		#this should output some kind of "you didn't get it" message to the students
		#and create a table for the word, and add the incorrect response to the table
		
		$dbq = "CREATE TABLE  IF NOT EXISTS `{$databaseVariable}`.`$studentId$correctWord` (
`number` INT( 4 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`mistake` TEXT NOT NULL
) ENGINE = MYISAM ;
	";
		$dbr = mysqli_query($dbc, $dbq) or die("Couldn't create the wrong word table");
		
		$dbq = "INSERT INTO `{$databaseVariable}`.`$studentId$correctWord` (`number`, `mistake`) VALUES (NULL, '$studentAttempt');";
		
		$dbr = mysqli_query($dbc, $dbq) or die("Couldn't insert the incorrect attempt into the word table");
	
	}# end of correct check and update
}elseif ($_SERVER['HTTP_REFERER'] != "http://davidbrownhk.com/sightwords/wordInput.php" || "http://davidbrownhk.com/sightwords/wordTest.php") {
	
	#set the counter to 0 in case there will be any confusion about where in the list the student will start:
	#echo "You didn't come here from the Input page! Initializing word counter to zero...";	
	$_SESSION['counter'] = 0;
	
}# end of correct word check loop - this part closes the $_SERVER checks

# check if the counter is intialized and tryCount and set it if not:
if(!isset($_SESSION['counter'])) {
	$_SESSION['counter'] = 0;
	#echo "Initialized word counter to zero as it wasn't set";
}

#check if the tryCount variable is set, and set it if not:
if(!isset($_SESSION['tryCount'])){
	$_SESSION['tryCount'] = 1;
	#echo "inside the initializing if: Trycount = ". $_SESSION['tryCount'];
}

#echo " Outside the init if: Trycount = ". $_SESSION['tryCount'];
// if button is pressed, increment counter
if(isset($_POST['button'])) {
	++$_SESSION['counter'];
}

// reset counter when five words are done, so we can do them again if needed.
if($_SESSION['counter'] > 4) {
	$_SESSION['counter'] = 0;
	#echo "the word counter has been reset";
	$_SESSION['tryCount'] ++;

}

#check the tryCount number, and if there are three goes done, pop a javascript alert telling the student they'r edone for homework for that day:

if($_SESSION['tryCount'] == 4){
	
	#echo "doing the trycount reset conditional";
	 
	#echo "tryCOunt is over 3!";
	
	#reset the variable to zero:
	$_SESSION['tryCount'] = 1;
    #bounce out of the page and back to wordTry.php
	/**?><script = "text/javascript">
		window.location.href= 'http://localhost/sightWordsStudy/wordTry.php';
	</script><?php**/ 
	?><script = "text/javascript">
	
	alert("You've finished today's homework. You can carry on, or stop here.");
	window.location.href = "wordTry.php";
	 
		</script><?php

}


#make the array of words by calling the function makeSightArray in masterVariables:
$sightArray = makeSightArray($dbc, $studentallWordsTable);

#choose the current word to work with:
$currentWord = $sightArray[$_SESSION['counter']];

#Check if the student has come here from the wordInput.php page and has made an attempt at the word:
if (isset($_POST['studentAttempt'])){
	#update the table by calling a function from masterVariables:
	update_numberOfAttempts($dbc, $studentallWordsTable, $currentWord);
}

# Make the page heading: this stuff is the beginning of the visible material for the user:
echo "<h1> Hi, {$_SESSION['firstName']}. Let's practice your reading now.</h1>";

#Display the words one by one and have the sound recorder play it:
echo "<h3>".$currentWord."</h3><p>";

#Keep this word as the correctWord i.e (the word taken from the database of correct words) by adding it to a session variable:
$_SESSION['correctWord'] = $currentWord;
#make a length for the text input that's coming up:
$_SESSION['inputLength'] = strlen($_SESSION['correctWord']);

#this file variable links to the available sound files, and needs to be changed to allow for different words:
$file = "soundFiles\\$currentWord.mp3";
$_SESSION['file'] = $file;

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


<script = "text/javascript">
setTimeout(function () {
	   window.location.href= 'http://davidbrownhk.com/sightwords/wordInput.php'; // the redirect goes here 
	},3500); // times a thousand for seconds 
</script>

<?php
include_once 'footer.html';
?>