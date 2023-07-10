<?php // <--- do NOT put anything before this PHP tag

// this php file will have no HTML

	include('Functions.php');

	deleteCookie('CookieUser');
	deleteCookie('CookieMessage');

	redirect("Homepage.php");
	exit(); //terminate code execution
?>
