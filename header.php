<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/11">
	<title><?php echo htmlspecialchars($PageTitle); ?></title>	
	<link rel="shortcut icon" href="image/favicon.ico" />
	<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="en-gb" />
	<meta http-equiv="imagetoolbar" content="false" />
	<meta name="author" content="Christopher Robinson" />
	<meta name="copyright" content="Copyright (c) Christopher Robinson 2005 - 2007" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />	
	<meta name="last-modified" content="Sat, 01 Jan 2007 00:00:00 GMT" />
	<meta name="mssmarttagspreventparsing" content="true" />	
	<meta name="robots" content="index, follow, noarchive" />
	<meta name="revisit-after" content="7 days" />
</head>
<?php

#if not a teacher, here is the student header links

	
?>
<body>
	<div id="header">
		<h1><?php echo "$PageTitle"; ?></h1>
		<h2>Sight Words Study</h2>
	</div>
	<div id="navigation">
		<ul>
			<li><a href="loggedIn.php" class= "button orange">Home</a></li>
			<li><a href="wordPractice.php" class= "button orange" >Learn</a></li>
			<li><a href="wordTry.php" class= "button orange" >Try</a></li>
			<li><a href="questionnaire.php" class= "button orange" >Questions</a></li>
			<li><?php # create a login/logout link:

				if ( (isset($_SESSION['studentId'])) && (!strpos($_SERVER['PHP_SELF'], 'logout.php')) ) {
				echo '<a href="logOut.php" class= "button orange">Log out</a>';

				}else {

				echo '<a href="login.php" class= "button orange">Log in</a>';

				}
		?></li>
		</ul>
	</div>
	<div id="content">
	<!--  This is the beginning of the page-specific content div -->
<?php	
?>
