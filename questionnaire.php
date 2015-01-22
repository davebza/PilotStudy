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

# Code goes here! 

$dbq = 	"SELECT COUNT( `questionNumber`)
FROM  `{$databaseVariable}`.`{$studentId}Questionnaire`;";
$dbr = mysqli_query($dbc, $dbq);

#check whether the student has finished the questionnaire:
@$resultArray = mysqli_fetch_array($dbr);
$checkFinished = $resultArray[0];

if($checkFinished == 19){
	
	echo "<p><h2> Congratulations! You have already finished all the questions!</h2></p>";
	exit("Please start the word training now.");	
} 

if(!isset($_POST['next'])){
	
	#Check that $_POST['langButton'] is set. If not:
	if (!isset ($_POST['langButton'])) {
		#Show a choice of language header:
		echo "<h1>Please choose your language below for the questions.</h1>";
	
	#the first time we come to the page we need a table to store student responses. This will create it by checking if the SESSSION['qCounter'] has been set:
	#if (!isset($_SESSION['qCounter'])){
	
	$dbq = "CREATE TABLE IF NOT EXISTS  `{$databaseVariable}`.`{$studentId}Questionnaire` (
	`questionNumber` TINYINT( 2 ) NOT NULL ,
	`response` INT( 1 ) NOT NULL ,
	PRIMARY KEY (  `questionNumber` )
	) ENGINE = MYISAM ;";
	$dbr = mysqli_query($dbc, $dbq) or die("There was a problem creating the student's questionnaire table");
	
			#initialize the question counter to get the correct question out of the database:
	$_SESSION['qCounter'] = 1;
	#}#end of not Isset qcounter Loop
	
	}#end of not isset $_POST['langButton'] loop
}#end of !isser POST[next] loop

#Do the answer insertion to the student's table if the button for the next question has been pushed before this step:

if (isset($_POST['next'])){

	#echo $_POST['qCounter'];
	
	#first check that we haven't come to the end of the questionnaire:
	if($_SESSION['qCounter'] < 19){
	
	#make variable for easy use and insertion of the student's response to the question:
	$studentResponse = $_POST['response'];
	
	$dbq = "INSERT INTO `{$databaseVariable}`.`{$studentId}Questionnaire` (`questionNumber`, `response`) VALUES ({$_SESSION['qCounter']}, {$studentResponse});";
	$dbr = mysqli_query($dbc, $dbq) or die("Couldnt' record the student response!");
	
	}else if($_SESSION['qCounter'] == 19){
		
		$studentResponse = $_POST['response'];
		
		$dbq = "INSERT INTO `{$databaseVariable}`.`{$studentId}Questionnaire` (`questionNumber`, `response`) VALUES ({$_SESSION['qCounter']}, {$studentResponse});";
		$dbr = mysqli_query($dbc, $dbq) or die("Couldnt' record the student response!");
		echo "<p><h2> Congratulations! You finished all the questions!</h2></p>";
		exit("Please start the word training now.");
	
	}else if($_SESSION['qCounter'] > 19){
		
		echo "<p><h2> You've finished the questionnaire!</h2></p>";
		exit("Please start the word training now.");
	}
}

#If the student has been to the page before, find how far into the questionnaire they were and serve the next question:
if (isset($_SESSION['qCounter'])){

	$dbq = 	"SELECT COUNT( `questionNumber`)
	FROM  `{$databaseVariable}`.`{$studentId}Questionnaire`;";
	$dbr = mysqli_query($dbc, $dbq) or die("couldn't count the rows in the student's questionnaire table");

	$resultArray = mysqli_fetch_array($dbr);
	$_SESSION['qCounter'] = $resultArray[0] + 1;
	
}# end of question number retrieval loop

#Check that $_POST['langButton'] is set. If it is, check whether the choice is Chinese or English and output appropriate heading for the page:
if (isset ($_POST['langButton']) && ($_POST['langButton'] == "English")) {
	
	echo "<h1> Questions about Reading</h1>";
	echo "<p>To answer these questions, think about what you read in English at home, in class, and even on the internet. There are no right or wrong answers to these questions.</p>";
	$_SESSION['language'] = "English";
	
	}else if (isset ($_POST['langButton']) && ($_POST['langButton'] == "中文")){
	
	echo "<h1>閱讀問卷:</h1>";
	echo "<p> 回答下列問題時，請細想你在家、在課堂，甚至上網時進行英語閱讀的情況。這些問題的答案沒有對或錯，但請你在毎道問題上只填寫一個答案。 </p>";
	$_SESSION['language'] = "中文";
	
}#End of language choice loop

#retrieve the question from the questionnaire database, first checking for language:

if (isset ($_SESSION['language']) && ($_SESSION['language'] == "English")){
	
	$dbq = 	"SELECT (`questionText`) FROM  `{$databaseVariable}`.`questionnaireQuestions` WHERE  `questionNumber` =  {$_SESSION['qCounter']} LIMIT 1";
	$dbr = mysqli_query($dbc, $dbq) or die("couldn't fetch the question text from the English set");
	
	$resultQuestionArray = mysqli_fetch_array($dbr);
	#make an easy variable for the text retrieved:
	$questionText = $resultQuestionArray[0];
	
}else if (isset ($_SESSION['language']) && ($_SESSION['language'] == "中文")){
	
	#set the charset to chinese friendly:
	mysqli_set_charset($dbc, 'utf8');
	$dbq = 	"SELECT (`questionText`) FROM  `{$databaseVariable}`.`questionnaireQuestionsChinese` WHERE  `questionNumber` =  {$_SESSION['qCounter']} LIMIT 1";
	$dbr = mysqli_query($dbc, $dbq) or die("couldn't fetch the question text from the Chinese set");
	
	$resultQuestionArray = mysqli_fetch_array($dbr);
	#make an easy variable for the text retrieved:
	$questionText = $resultQuestionArray[0];
	
}

?>

<fieldset>
	<form action = "questionnaire.php" method= "post">
	
	<?php 
#if this is the first time the page is loading - show buttons to choose language:	
if (!isset ($_SESSION['language'])) {

	?>
	<p>
	<input type="submit" class = "button orange" name="langButton" value="English" />
	<input type="submit" class = "button orange" name="langButton" value="中文" /> </p><?php
} ?>
	<!--this is where the rest of the page will appear: the questions and answer frame -->
	
<?php 	
#Check for English, and then deliver the English questions and radio buttons:
if (isset ($_SESSION['language']) && ($_SESSION['language'] == "English")) {
	
	#Give the question number
	echo "<p><h2>Question ".$_SESSION['qCounter']. ":</h2></p>";
	#give the question text:
	echo "<p><h2>".$questionText."</h2></p>";
?>
	<p>
	<ol>
		<li><input type="radio" name="response" value="1" > Strongly Disagree</li>
		<li><input type="radio" name="response" value="2"> Disagree</li>
		<li><input type="radio" name="response" value="3"> Neutral</li>
		<li><input type="radio" name="response" value="4"> Agree</li>
		<li><input type="radio" name="response" value="5"> Strongly Agree</li> 
	</ol>
	</p>
	
	<input type="hidden" name="qCounter" value="<?php echo $_SESSION['qCounter'];?>"/>
	<input type="submit" class = "button orange" name="next" value="Next" />
	<?php 

} else if(isset ($_SESSION['language']) && ($_SESSION['language'] == "中文")) {
	
	#Give the question number
	echo "<p><h2>Question ".$_SESSION['qCounter']. ":</h2></p>";
	#give the question text:
	echo "<p><h2>".$questionText."</h2></p>";
?>
	<p>
		<input type="radio" name="response" value="1" > 非常不同意
		<input type="radio" name="response" value="2"> 不同意
		<input type="radio" name="response" value="3"> 中立
		<input type="radio" name="response" value="4"> 同意
		<input type="radio" name="response" value="5"> 非常同意
	</p>
	
	<input type="hidden" name="qCounter" value="<?php echo $_SESSION['qCounter'];?>"/>
	<input type="submit" class = "button orange" name="next" value="Next" />
	<?php 
}
	
?>	
	</form>
</fieldset>


<?php
include_once 'footer.html';
?>