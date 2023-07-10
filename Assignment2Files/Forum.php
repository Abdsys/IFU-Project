<?php
// <--- do NOT put anything before this PHP tag
include('Functions.php');

$cookieMessage = getCookieMessage();
$cookieUser = getCookieUser();
//Checks if Topic is set, if it is set assign it to a variable $thisTopic i.e. the topic which is selected.
if (isset($_GET['Topic'])){
    $thisTopic = $_GET['Topic'];
} else {
    redirect("Topics.php");
    exit();
}

if ($cookieUser != "" && isset($_GET['PostID'])) {
    $postID = $_GET['PostID'];

    // Increase the number of likes for the post in the database
    $dbh = connectToDatabase();
    $statement = $dbh->prepare('UPDATE Post SET Likes = Likes + 1 WHERE PostID = ?');
    $statement->bindValue(1, $postID);
    $statement->execute();

    // Redirect back to the forum page
    redirect("Forum.php?Topic=$thisTopic");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <meta charset="UTF-8"> <!-- For emojis -->
</head>
<body>
<div class="container">
    <div class="row" id="header">
        <h2>CSE4IFU - Forum</h2>
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
					echo '<li><a href = "LogOutUser.php">Sign Out</a></li><br>';
					echo '<span class="cookie-message">' . $cookieUser . '</span>';
				}
			?>
		</ul>
    </div>
    <div class="row" id="content">
        <h3><?php echo $thisTopic?></h3>
		<!--Adding table to the forum page -->
        <table>
            <tr>
                <th>User</th>
                <th>Post</th>
                <th>Date</th>
                <th>Likes</th>
            </tr>
            <?php
            $dbh = connectToDatabase();

            // Obtain TopicID based on the name of the topic.
            $statement = $dbh->prepare('SELECT TopicID FROM Topic WHERE Topic = ?; ');
            $statement->bindValue(1, $thisTopic);
            $statement->execute();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            $topicID = $row['TopicID'];

            // Count the number of posts in that particular topic.
            $statement = $dbh->prepare('SELECT COUNT(Post.PostID) FROM Post WHERE Post.TopicID = ?; ');
            $statement->bindValue(1, $topicID);
            $statement->execute();
            $count = $statement->fetchColumn();

            // Get the posts for the topic.
            $postStatement = $dbh->prepare('SELECT * FROM Post INNER JOIN User ON User.UserID = Post.UserID WHERE Post.TopicID = ? ORDER BY Post.PostID DESC');
            $postStatement->bindValue(1, $topicID);
            $postStatement->execute();

			$pageLimit = 10; // Number of posts per page
			$page = isset($_GET['page']) ? $_GET['page'] : 1; // Get the current page number
			$offset = ($page - 1) * $pageLimit; // Calculate the offset
			
			$postStatement = $dbh->prepare('SELECT * FROM Post INNER JOIN User ON User.UserID = Post.UserID WHERE Post.TopicID = ? ORDER BY Post.PostID DESC LIMIT :limit OFFSET :offset');
			$postStatement->bindValue(1, $topicID);
			$postStatement->bindValue(':limit', $pageLimit, PDO::PARAM_INT);
			$postStatement->bindValue(':offset', $offset, PDO::PARAM_INT);
			$postStatement->execute(); //Explained in topics page.

            while ($row = $postStatement->fetch(PDO::FETCH_ASSOC)) {
                $username = $row['UserName'];
                $fName = $row['FirstName'];
                $sName = $row['SurName'];
                $dateTime = $row['DateTime'];
                $text = $row['Post'];
                $tag = $row['Tag'];
                $likes = $row['Likes'];
                $postID = $row['PostID'];

                echo "<tr>";
                echo "<td>$username<br><sup>$fName $sName</sup></td>"; //sup is used for adding first and last names in superscript
                echo "<td><p id=\"post\">$text</p><br>
				<p id=\"tagDisplay\">$tag</p></td>";
                echo "<td>$dateTime</td>";
				echo "<td>";
                if ($cookieUser != "") {
					echo "<span id=\"likes_$postID\">$likes</span>"; //Only logged in users can like a post.
					echo "<a href = \"postLike.php?Topic=" . urlencode($thisTopic) . "&PostID=$postID\">";
					echo "&#x1F64C;</a>"; //For emoji
				} else {
					echo $likes;
				}
				echo "<td>";
                echo "</tr>";
            }

			$totalPages = ceil($count / $pageLimit); // Calculate the total number of pages
            ?>
        </table>
		<?php
			echo "<div class='pages'>";
				if ($page > 1) {
					echo "<a href='Forum.php?Topic=$thisTopic&page=" . ($page - 1) . "'>&laquo; Previous</a>";
				}
				for ($i = 1; $i <= $totalPages; $i++) {
					if ($i == $page) {
						echo "<a class='active' href='Forum.php?Topic=$thisTopic&page=$i'>$i</a>";
					} else {
						echo "<a href='Forum.php?Topic=$thisTopic&page=$i'>$i</a>";
					}
				}
				if ($page < $totalPages) {
					echo "<a href='Forum.php?Topic=$thisTopic&page=" . ($page + 1) . "'>Next &raquo;</a>";
				}
			echo "</div>";
		?>
        <h2>Create a new post</h2>
        <?php
        echo $cookieMessage;
        if ($cookieUser === "") {
            echo 'You must be logged in to create a topic.';
        } else {
            ?>
            <form method="POST" action="AddPost.php?Topic=<?php echo $thisTopic; ?>">
				<div class="form-row">
					<label for="Post">Post:</label>
                	<input type="text" name="Post" id="Post">
				</div>
                <br>
                <input type="submit" value="AddPost" class="submit-button"/>
            </form>
            <?php
        }
        ?>
    </div>
    <div class="row" id="footer">
        <h3>Abdur Rehman - 21452806 - CSE4IFU - 2023 Sem 1</h3>
    </div>
</div>
</body>
</html>
