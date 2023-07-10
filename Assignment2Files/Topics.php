<?php // <--- do NOT put anything before this PHP tag

include('Functions.php');
$cookieMessage = getCookieMessage();
$cookieUser = getCookieUser();
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date'; // Find the current sort option

$dbh = connectToDatabase();

$countStatement = $dbh->prepare('SELECT COUNT(TopicID) AS TopicCount FROM Topic');
$countStatement->execute();
$topicCount = $countStatement->fetch(PDO::FETCH_ASSOC)['TopicCount'];

$sortColumn = ($sort === 'name') ? 'Topic.Topic ASC' : 'Topic.DateTime DESC'; //If sort is by name, arrange topics in ascending order otherwise arrange by dates such as the most recent topic appears first.

$pageLimit = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Find the current page number from the query string.
$offset = ($page - 1) * $pageLimit; //Calculates the number of entries to skip in the database to retrieve the relevant topics.

//Joins the entries from topic table and user table based on user ID and ordered by sort column variable defined above for the sorting function.
$statement = $dbh->prepare('SELECT * FROM Topic INNER JOIN User ON Topic.UserID = User.UserID ORDER BY ' . $sortColumn . ' LIMIT :limit OFFSET :offset');
$statement->bindValue(':limit', $pageLimit, PDO::PARAM_INT); //ensures value is treated as integer and bound to page limit.
$statement->bindValue(':offset', $offset, PDO::PARAM_INT); //Calculates and binds offset value while ensuring it is an integer.
$statement->execute();

$totalPages = ceil($topicCount / $pageLimit); // Calculate the total number of pages
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" type="text/css" href="styles.css">
	<meta charset="UTF-8">		<!-- For emojis -->
	<title>Topics</title>
</head>
<body>
	<div class="container">
		<div class="row" id="header">
			<h2>CSE4IFU - Topics</h2>
		</div>
		<div class="row" id="nav">  
			<ul>
				<li><a href="Homepage.php">Home</a></li>
				<li><a href="Topics.php">Topics</a></li>
				<?php
					if ($cookieUser === "") {
						echo '<li><a href="SignIn.php">Sign In</a></li>';
						echo '<li><a href="SignUp.php">Sign Up</a></li>';
					} else {
						echo '<li><a href="LogOutUser.php">Sign Out</a></li><br>';
						echo '<span class="cookie-message">' . $cookieUser . '</span>';
					}
				?>
			</ul>
		</div>
		<div class="row" id="content">
			<h3>Topics</h3>
			<table>
				<tr>
					<th>Created by User</th>
					<th><a href="Topics.php?sort=<?php echo ($sort === 'name') ? 'date' : 'name'; ?>">Topic Name</a></th> <!-- Href links for the sorting function -->
					<th><a href="Topics.php?sort=<?php echo ($sort === 'date') ? 'name' : 'date'; ?>">Date Created</a></th> <!-- Checks how the topics are currently sorted in the URL -->
				</tr>
				<?php
					while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
						$userName = $row['UserName'];
						$dateTime = $row['DateTime'];
						$topic = $row['Topic'];
						
						echo "<tr>";
						echo "<td>$userName</td>";
						echo "<td><a href=\"Forum.php?Topic=$topic\">$topic</a></td>";
						echo "<td>$dateTime</td>";
						echo "</tr>";
					}
				?>
			</table>
			<div class="pages">
				<?php
					if ($page > 1) {
						echo "<a href='Topics.php?page=" . ($page - 1) . "&sort=$sort'>&laquo; Previous</a>"; //echoes the previous page tag if page numbers is greater than 1.
					}
					for ($i = 1; $i <= $totalPages; $i++) {
						if ($i == $page) {
							echo "<a class='active' href='Topics.php?page=$i&sort=$sort'>$i</a>";  //anchor tag for current page
						} else {
							echo "<a href='Topics.php?page=$i&sort=$sort'>$i</a>"; //anchor tag for non-active page
						}
					}
					if ($page < $totalPages) {
						echo "<a href='Topics.php?page=" . ($page + 1) . "&sort=$sort'>Next &raquo;</a>"; //echoes the next page tag if page numbers is greater than total pages.
					}
				?>
			</div>
			<h2>Create a new topic</h2>
			<?php
				echo $cookieMessage;
				if ($cookieUser === "") {
					echo 'You must be logged in to create a topic.';
				} else {
					echo '<form name="topic-form" method="POST" action="AddTopic.php">';
					echo '<div class="form-row">';
					echo '<label for="Topic">Topic:</label>';
					echo '<input type="text" name="Topic" id="Topic">';
					echo '</div>';
					echo '<br/>';
					echo '<input type="submit" value="Add Topic" class="submit-button">';
					echo '</form>';
				}
			?>
		</div>
		<div class="row" id="footer">
			<h3>Abdur Rehman - 21452806 - CSE4IFU - 2023 Sem 1</h3>
		</div>
	</div>
</body>
</html>
