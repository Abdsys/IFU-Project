<?php
	include('Functions.php');
	//not empty is used as per the data types specified in the SQL table. If we do not apply this the user will be able to submit an empty form or a form without username, firstname or lastname
	if (!(isset($_POST['Username'], $_POST['Firstname'], $_POST['Surname'], $_POST['Shorttag']) && !empty($_POST['Username']) && !empty($_POST['Firstname']) && !empty($_POST['Surname']))) {
		setCookieMessage("Please fill in all the required fields");
		redirect("SignUp.php");
		exit;
	}
	
	$dbh = connectToDatabase();
	
	//Trim inputs and store in php variables
	$UserName = trim($_POST['Username']);
	$FirstName = trim($_POST['Firstname']);
	$Surname = trim($_POST['Surname']);
	$ShortTag = trim($_POST['Shorttag']);
	
	$statement = $dbh->prepare('SELECT * FROM User WHERE UserName = ? COLLATE NOCASE');
	$statement->bindValue(1, $UserName);
	$statement->execute();
	
	//Check if data is fetched from the SQL statement executed above otherwise add the new entry to the database.
	if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
		setCookieMessage("Username already exists");
		redirect("SignUp.php");
	} else {
		$statement2 = $dbh->prepare('INSERT INTO User (UserName, FirstName, SurName, Tag) VALUES (?, ?, ?, ?)');
		$statement2->bindValue(1, $UserName);
		$statement2->bindValue(2, $FirstName);
		$statement2->bindValue(3, $Surname);
		$statement2->bindValue(4, $ShortTag);
		
		$statement2->execute();
		
		setCookieMessage("The new user $UserName has been added. Please sign in to start posting.");
		redirect("Homepage.php");
	}
?>
