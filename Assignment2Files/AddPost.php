<?php // <--- do NOT put anything before this PHP tag

// this php file will have no HTML

	include('Functions.php');
	
	$dbh = connectToDatabase();

	$Post = trim($_POST['Post']);
	$Topic = trim($_GET['Topic']);

	$cookieUser = getCookieUser();

	$statement = $dbh->prepare('SELECT UserID FROM User WHERE UserName = ?');
	$statement->bindValue(1, $cookieUser);
	$statement->execute();
	$row = $statement->fetch(PDO::FETCH_ASSOC);
	$UserID = $row['UserID'];


	$statement = $dbh->prepare('SELECT TopicID FROM Topic WHERE Topic = ?');
	$statement->bindValue(1, $Topic);
	$statement->execute();
	$row = $statement->fetch(PDO::FETCH_ASSOC);
	$TopicID = $row['TopicID'];

	$statement2 = $dbh->prepare('INSERT INTO Post (UserID, Post, DateTime, TopicID) VALUES (?, ?, ?, ?);');
	date_default_timezone_set("Australia/Melbourne");
	$statement2->bindValue(1, $UserID);
	$statement2->bindValue(2, $Post);
	$statement2->bindValue(3, date("Y/m/d H:i:s"));
	$statement2->bindValue(4, $TopicID);

	$statement2->execute();

	redirect("Forum.php?Topic=$Topic");

?>
