<?php // <--- do NOT put anything before this PHP tag
// this php file will have no HTML

	include('Functions.php');

	$cookieMessage = getCookieMessage();
	$cookieUser = getCookieUser();
	
	//Checks the post relevant to a topic with PostID and assigns it to the following variables.
	if ($cookieUser != "" && isset($_GET['Topic']) && isset($_GET['PostID'])) {
		$thisTopic = $_GET['Topic'];
		$postID = $_GET['PostID'];
	
		// Increase the number of likes for the post in the database
		$dbh = connectToDatabase();
		$statement = $dbh->prepare('UPDATE Post SET Likes = Likes + 1 WHERE PostID = ?');
		$statement->bindValue(1, $postID);
		$statement->execute();
	
		// Redirect back to the forum page with the correct Topic parameter
		redirect("Forum.php?Topic=" . urlencode($thisTopic)); //ensures special characters are encoded before representation in the URL
		exit();
	}
?>