<?php // <--- do NOT put anything before this PHP tag
	include('Functions.php');
	$cookieMessage = getCookieMessage();
		$cookieUser = getCookieUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>SignIn</title>
	<link rel="stylesheet" type="text/css" href="styles.css"> 
	
</head>
<body>
	<div class="container">
		<div class="row" id="header">
			<h2>CSE4IFU - SignIn</h2>
		</div>
		<div class="row" id="nav">  
			<ul>
				<li><a href = "Homepage.php">Home</a></li>
				<li><a href = "Topics.php">Topics</a></li>
				<?php
					if ($cookieUser === ""){
						echo '<li><a href = "SignIn.php">Sign In</a></li>';
						echo '<li><a href = "SignUp.php">Sign Up</a></li>';
					} else{
						echo '<li><a href="LogOutUser.php">Sign Out</a></li><br>';
						echo '<span class="cookie-message">' . $cookieUser . '</span>';
					}
				?>
			</ul>
		</div>
		<div class="row" id="content">
			<?php
				echo $cookieMessage;
			?>
			<!-- Sign in form -->
			<form name="Sign-in-form" method = "POST" action = "LogInUser.php">
				<div class="form-row">
					<label for="username">Username:</label>
					<input type="text" name="Username" id="username">
				</div>
				<br/>
				<input type = "Submit" value = "Sign In" class="submit-button"/>
			</form>
		</div>
		<div class="row" id="footer">
			<h3>Abdur Rehman - 21452806 - CSE4IFU - 2023 Sem 1</h3>
		</div>
	</div>
</body>
</html>