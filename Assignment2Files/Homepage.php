<?php // <--- do NOT put anything before this PHP tag
	include('Functions.php');
	$cookieMessage = getCookieMessage();
	$cookieUser = getCookieUser();

	//To add the most popular post on the home page.
	$dbh = connectToDatabase();
	$statement = $dbh->prepare('SELECT Post.*, User.UserName, User.FirstName, User.SurName FROM Post INNER JOIN User ON User.UserID = Post.UserID
                           		ORDER BY Post.Likes DESC LIMIT 1');
	$statement->execute();
	$popPost = $statement->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Home page</title>
	<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
	<div class="container">
		<div class="row" id="header">
			<h2>CSE4IFU - Home</h2>
		</div>
		<div class="row" id="nav">  
			<ul>
				<li><a href = "Homepage.php">Home</a></li>
				<li><a href = "Topics.php">Topics</a></li>
				<?php
					if ($cookieUser === ""){
						echo '<li><a href = "SignIn.php">Sign In</a></li>'; //No user is logged in
						echo '<li><a href = "SignUp.php">Sign Up</a></li>';
					} else{
						echo '<li><a href = "LogOutUser.php">Sign Out</a></li><br>';
						echo '<span class="cookie-message">' . $cookieUser . '</span>'; //User is logged in, echoes Username.
					}
				?>
			</ul>
		</div>
		<div class="row" id="content">
			<?php
				echo $cookieMessage; //echoes cookie message when the user logs in.
			?>
			<h4>Welcome to the CSE4IFU Discussion Forum</h4>
			<p>Join the vibrant community of students, teachers and IT professionals. Here you can ask questions, participate in discussions and interact with fellow members</p>
			<p>Discover a wide range of topics including programming languages, databases, algorithms, networking, artificial intelligence and much more!</p>
			<p><a href = "SignUp.php">SignUp</a> now and become a part of the community. Happy posting and discussions!</p>
			<img src = "picture.jpg" alt = "image" height = "300" width = "500">
			
			<h3>Most Popular Post</h3>
			<?php if ($popPost) { ?> 
				<table>
					<tr>
						<th>User</th>
						<th>Date</th>
						<th>Post</th>
						<th>Likes</th>
					</tr>
					<tr>
						<td><?php echo $popPost['UserName']; ?></td>
						<td><?php echo $popPost['DateTime']; ?></td>
						<td><?php echo $popPost['Post']; ?></td>
						<td><?php echo $popPost['Likes']; ?></td>
					</tr>
				</table>
			<?php } else { ?>
				<p>No popular post found.</p>
			<?php } ?>
		</div>
		<div class="row" id="footer">
			<h3>Abdur Rehman - 21452806 - CSE4IFU - 2023 Sem 1</h3>
		</div>
	</div>
</body>
</html>