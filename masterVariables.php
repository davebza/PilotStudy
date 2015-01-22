<?php
#This page will store all the variables related to the student's info and the tablenames for the database queries and worksheet names,
#allowing this to be dynamically generated easily.
#This page will be invisible

#Start the session, to import the student info: 
session_start();
include ('mysqliConnectfluencyStudy.php');

$studentId = $_SESSION['class'].$_SESSION['classNumber'];

$databaseVariable = "daveb_fluencyStudy";

#The Table variables:
$studentallWordsTable = $studentId."allWords";
$studentworkingWordsTable = $studentId."workingWords";

#The function to build an array of sight words for the student:
function makeSightArray($dbc, $studentallWordsTable){
	
	$dbq = "SELECT  `id` ,  `word`
	FROM  `daveb_fluencyStudy`.`$studentallWordsTable`
	WHERE  `numberCorrect` < 3
	LIMIT 5;";
	
	$dbr = mysqli_query($dbc, $dbq) or die("Couldn't check your progress. Please check with your teacher about this. Technical data: masterVariables line 27.");
	
	#Make a numerical array out of the words for practice:
	$sightArray = array();
	while($row = mysqli_fetch_array($dbr)){
		$sightArray[] = trim($row['word']);
	} return $sightArray;
}# end of makeSightArray function

#the function to update the number of attempts column in the student's allwords table:
function update_numberOfAttempts($dbc, $studentallWordsTable, $currentWord){
	
	$dbq = "UPDATE `daveb_fluencyStudy`.`$studentallWordsTable`
	SET `numberOfAttempts`=`numberOfAttempts` + 1
	WHERE `word` = '$currentWord'
	LIMIT 1;";
	
	$dbr = mysqli_query($dbc, $dbq) or die(" we had a problem with attempt update function. Technical details: masterVariables line 44");
}#end of update_numberOfAttempts function

#the function to check whether the word is correct maybe should go here:

#The function to sanitize input and make it lowercase without any spaces:

function cleanUp($studentAttempt){
	
	$studentAttempt = trim($studentAttempt);
	$studentAttempt = strtolower($studentAttempt);
	return $studentAttempt;
}



?>


