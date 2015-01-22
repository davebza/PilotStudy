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
include ('mysqliConnectfluencyStudy.php');

#Check the status of the student's reading questionnaire:

$dbq = "SELECT COUNT( * ) 
FROM  `{$databaseVariable}`.`{$studentId}Questionnaire`;";
$dbr = @mysqli_query($dbc, $dbq);

if ($dbr !==FALSE){
	
	
	$questionnaireProgressArray = mysqli_fetch_array($dbr);
	$questionnaireProgress = $questionnaireProgressArray[0];
	$questionnaireMessage = "You have finished up to question ".$questionnaireProgress.".";
	#echo "You've got a table! It is ".$questionnaireProgress. " rows long";
	
}else if ($dbr === FALSE){
	
	#echo "No table to be found";
	$questionnaireMessage = "You need to do the questionnaire!";
}

#Print a customized message:
		
echo "<h1> Welcome, {$_SESSION['firstName']}.</h1>
		
<p><h2>Here is your information: </h2></p>
		
<p>
	<ul>
		<li> Class: {$_SESSION['class']} Number: {$_SESSION['classNumber']}</li>
		<li> Name: {$_SESSION['firstName']} {$_SESSION['lastName']}</li>
		<li> Questionnaire:  {$questionnaireMessage}</li>
	</ul>
</p>";

#first check if the student has a table:

$dbq = "SELECT COUNT( * )
FROM  `{$databaseVariable}`.`{$studentId}allWords`;";
$dbr = @mysqli_query($dbc, $dbq);

if ($dbr === False) {
	
	#create the student's word record table in MYSQL if it doesn't already exist:
	$createTable= "CREATE TABLE IF NOT EXISTS `{$databaseVariable}`.`$studentallWordsTable` SELECT * FROM `{$databaseVariable}`.`allWords` LIMIT 133;";
	
	$createQRun = mysqli_query($dbc, $createTable);
	
	if(!$createQRun){
		echo "There has been an error creating a records table. No AllWords table exists for this user";
	}
}

#display the student's word record as a table for progress checks - this could be made more visual:

$dbq = "SELECT `id` AS `Word Number`, `word` AS `Word`, `numberCorrect` AS  `Number of right answers`, `numberOfAttempts` AS `Times I've tried`  FROM  `{$databaseVariable}`.`$studentallWordsTable`";
$dbr = mysqli_query($dbc, $dbq) or die("Couldn't check your progress. Please check with your teacher about this - technical data: loggedIn.php line 78.");

if($dbr) {# if the query ran ok

	#get headers for table
	$headers = mysqli_num_fields($dbr);

	#output headers:
	?><table><?php echo "<h1>Here's your progress:</h1>";
						?><tr><?php 	
							for($i=0; $i<$headers; $i++){
										
								$field = mysqli_fetch_field($dbr);
								echo "<th><a href = '#'>{$field->name}</a></th>";
							}
							echo "</tr>\n";
							#output row data:	
							while($row = mysqli_fetch_row($dbr)){
							    
								echo "<tr>";
							
							    // $row is array... foreach( .. ) puts every element
							    // of $row to $cell variable
							    foreach($row as $cell){
							        echo "<td>$cell</td>";
							    }
							    echo "</tr>\n";
							}
				?></table><?php					
							mysqli_free_result($dbr);
							
						}#end if result condition and end making the table

include_once 'footer.html';
?>