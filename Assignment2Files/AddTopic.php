<?php // <--- do NOT put anything before this PHP tag

// this php file will have no HTML

	include('Functions.php');

	if (!(isset($_POST['Topic']) && !empty($_POST['Topic']))) {
		setCookieMessage("Please provide the topic");
		redirect("Topics.php");
		exit;
	}
	
	$dbh = connectToDatabase();
	
	$Topic = trim($_POST['Topic']);
	
	$statement = $dbh->prepare('SELECT * FROM Topic WHERE Topic = ? COLLATE NOCASE');
	$statement->bindValue(1, $Topic);
	$statement->execute();
	
	if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
		setCookieMessage("The topic already exists, enter a different topic.");
		redirect("Topics.php");
	} else {
		$cookieUser = getCookieUser(); // Assuming the function exists in Functions.php
	
		$statement = $dbh->prepare('SELECT UserID FROM User WHERE UserName = ?');
		$statement->bindValue(1, $cookieUser);
		$statement->execute();
	
		if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
			$userID = $row['UserID'];
		} else {
			// This handles the user not found case i.e. in case a user who has not signed in tries to enter a topic.
			redirect("Topics.php");
			exit;
		}
	
		date_default_timezone_set("Australia/Melbourne");
	
		$statement = $dbh->prepare('INSERT INTO Topic (UserID, DateTime, Topic) VALUES (?, ?, ?)');
		$statement->bindValue(1, $userID);
		$statement->bindValue(2, date("Y/m/d H:i:s"));
		$statement->bindValue(3, $Topic);
	
		$statement->execute();
	
		redirect("Topics.php");
	}
?>