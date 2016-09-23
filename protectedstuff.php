<?php ob_start(); ?>
<?php
session_start();

if($_SESSION['loggedIn']){
	echo "<h1>You are logged in!</h1>\n";
}else{
	header('Location: login.php');
}
?>
<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Login</title>
      <link rel="stylesheet" href="style.css">
  </head>
<body>
	<button class="lgout"  onclick="window.location.href='logout.php'">Logout</button>
</body>
