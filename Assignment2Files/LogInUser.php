<?php // <--- do NOT put anything before this PHP tag

// this php file will have no HTML

	include('Functions.php');

	//Check if the username is given using isset.
	if (!(isset($_POST['Username']) && !empty($_POST['Username']))) {
		setCookieMessage("Please provide the Username");
		redirect("SignIn.php");
		exit;
	}
	
	$dbh = connectToDatabase();
	
	$UserName = trim($_POST['Username']);
	
	$statement = $dbh->prepare('SELECT * FROM User WHERE UserName = ? COLLATE NOCASE');
	$statement->bindValue(1, $UserName);
	$statement->execute();
	
	//If the username is fetched redirect to homepage and return the cookie message and if it doesn't exist redirect to the Signin page with cookie message.
	if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
		setCookieUser($UserName);
		setCookieMessage("Hello $UserName, great to have you back");
		redirect("Homepage.php");
	} else {
		setCookieMessage("The username does not exist");
		redirect("SignIn.php");
	}
?>
