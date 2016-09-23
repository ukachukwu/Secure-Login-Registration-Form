<?php
require("myclasses.php");

if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['token']) && !empty($_GET['token'])){
    // Verify data
    $db = new dbLink('databaseportfolio');
    $link= $db->getlink();
    $username = htmlentities(mysqli_escape_string($link,$_GET['email'])); // Set email variable
    $hash = htmlentities(mysqli_escape_string($link,$_GET['token'])); // Set hash variable
    $search = $db->query("SELECT username, hash, active FROM users WHERE username='".$username."' AND hash='".$hash."' AND active='0'");
    $match  = mysqli_num_rows($search);

    if($match > 0){
        // We have a match, activate the account
      $activate= $db->query("UPDATE users SET active='1' WHERE username='".$username."' AND hash='".$hash."' AND active='0'") or die(mysql_error());
      header("Location: login.php?verified=".$username);
    }else{
        // No match -> invalid url or account has already been activated.
        $error = rand(0,99);
        header("Location: login.php?error=".$error );
        //echo $match;
    }
}else{
    header("Location: login.php");
}
?>
