<?php
#This file contains the major code needed to practice the sight words:
# first check for session data and bounce if none exists:

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
	
#Print a header and instuctions:
echo "<h1> Hi, {$_SESSION['firstName']}. Let's practice your reading now.</h1>";

#display the student's word record as a table for progress checks - this could be made more visual:

$dbq = "SELECT  `id` AS  `Word Number` ,  `word` AS  `Word` ,  `numberCorrect` AS  `Number of right answers`, `numberOfAttempts` AS `Times I've tried` 
FROM  `{$databaseVariable}`.`$studentallWordsTable` 
WHERE  `numberCorrect` <3
LIMIT 5";
$dbr = mysqli_query($dbc, $dbq) or die("Couldn't check your progress UPDATE. Please check with your teacher about this.");

if($dbr) {# if the query ran ok

	#get headers for table
	$headers = mysqli_num_fields($dbr);

	#output headers:
	?><table><?php echo "<h1>Here are today's words:</h1>";
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
 
?>

<fieldset>
	<form action = "practiceCycle.php">
			<p><input type = "submit"
							class = "button orange"
							name = "submit"
							value = "Let's Start!" /></p>
	</form>
</fieldset>

<?php
include_once 'footer.html';
?>
