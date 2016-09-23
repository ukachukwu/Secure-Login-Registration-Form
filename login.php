<?php
/*
CREATE TABLE `users` (
`username` varchar(100) NOT NULL,
`password` blob NOT NULL,
`role` enum('user','admin') NOT NULL,
`passwordHint` varchar(100) DEFAULT NULL,
`hash` varchar(32) NOT NULL,
`active` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
*/

require('myclasses.php');
$flag      = false;
$error     = '';
$regerror  = '';
$ferror    = '';
$lastvalue = '';
$check     = 0;
//login submit button check
if (isset($_POST['submit'])) {

    //INSERT INTO `users`(`username`, `password`, `role`, `passwordHint`) VALUES ('whats2info@gmail.com','whG7GmuIsdHSo','user','user-pass')
    $username = filter_input(INPUT_POST, $_POST['username'], FILTER_VALIDATE_EMAIL);
    $username = htmlentities(strtolower((isset($_POST['username']) ? $_POST['username'] : null)));
    $password = (isset($_POST['password']) ? $_POST['password'] : null);


    if (empty($username)) {
        $flag  = true;
        $error = "Please enter your username";
    } else if (empty($password)) {
        $flag  = true;
        $error = "Please enter your password";
    } else {

        $db       = new dbLink('databaseportfolio');
        $link     = $db->getlink();
        $username = mysqli_real_escape_string($link, $username);
        $result   = $db->query("SELECT * from users where username = '" . $username . "'");

        if (mysqli_num_rows($result) > 0) {

            while ($row = mysqli_fetch_assoc($result)) {
                //  echo "Database entry -> " ;print $row['username'];
                //  echo " This is input -> " . $username . "<br />";
                if ($row['active'] == 1) {
                    if ($row['username'] == $username) {

                        if (crypt($password, $row['password']) == $row['password']) {
                            // create session
                            session_start();
                            $_SESSION['loggedIn'] = true;
                            $_SESSION['username'] = $username;


                            header('Location: protectedstuff.php'); // redirect to protectedstuff page

                        } else {
                            $flag  = true;
                            $error = 'Invalid login credentials';
                            break;
                        }

                    } // username == ends
                } else {
                    $flag  = true;
                    $error = 'Your account is not activated.';
                    break;
                }

            } // while ends
        } else {
            $flag  = true;
            $error = "You are not registered. Please register to log in.";
        }

        /*
        echo "The last value - > ". $lastvalue . "<br />";
        if($check == 1){
        if($lastvalue != $username ){
        $flag = true;
        $error = "You are not registered. Please register to log in.";

        } // lastvalue check ends
        }// if check ends */

    } //empty password ends;
} // login submit ends

?>

 <!DOCTYPE html>
 <html >
   <head>
     <meta charset="UTF-8">
     <title>Login</title>
       <link rel="stylesheet" href="style.css">
       <style>
       .form .register-form {
         display: none;
       }
       .form .forgot_pass{
         display: none;
       }
       </style>
   </head>

   <body>
     <a href="http://arjunvegda.com" >
<img id="siteonlogin" src="http://arjunvegda.com/logo.png" alt="AV" height="120" width="120"></a>

     <h1> Demo Login page</h1>
     <?php
if (isset($_GET['verify'])) {
    $verify = htmlentities($_GET['verify']);
    echo "<h3>Verification link sent to <span class=\"success\"> $verify</span> !<h3>\n";
}
if (isset($_GET['sent'])) {
    $usrnm = htmlentities($_GET['sent']);
    echo "<h3>Email successfully sent to <span class=\"success\"> $usrnm</span> !<h3>\n";
}
if (isset($_GET['error'])) {
    $error = htmlentities($_GET['error']);
    if (is_numeric($error)) {
        echo "<h4>The url you are trying to verify is either invalid or you already have activated your account.<h4>\n";
}
}

if(isset($_GET['verified'])){
  $username = htmlentities($_GET['verified']);
  echo "<h3>Successfully verified<span class=\"success\"> $username</span>.<h3>\n";
}

if (isset($_POST['forgot'])) {

    $username = htmlentities(strtolower(trim((isset($_POST['username']) ? $_POST['username'] : null))));
    if (empty($username)) {
        echo "<h3>Username field was empty. Please input credentials or Sign up !<h3>\n";
    } else {
        $db       = new dbLink('databaseportfolio');
        $link     = $db->getlink();
        $username = mysqli_real_escape_string($link, $username);
        $result   = $db->query("SELECT DISTINCT username, passwordHint FROM users WHERE username='" . $username . "'");
        if ($db->emptyResult($result)) {
            $row = mysqli_fetch_assoc($result);
            if ($row["username"] == $username) {
                $to      = $username;
                $subject = 'Password recovery ';
                $message = 'From : Demo login page at _______' . "\r\n";
                $message .= 'Your password hint is: ' . $row['passwordHint'];
                $message = wordwrap($message, 70, "\r\n");
                mail($to, $subject, $message, $headers);


                echo "<h3>Email successfully sent to <span class=\"success\"> $username</span> !<h3>\n";
                //  echo "<h3>". $row['passwordHint'] . "<h3>";

            } else {
                echo "<h3>Unable to find <span class=\"success\"> $username</span> in our database !<h3>\n";
            }

        } else {
            echo "<h3>Unable to find <span class=\"success\"> $username</span> in our database !<h3>\n";
        }


    }
}

?>

     <div class="login-page">
   <div class="form">
     <form class="register-form" method="post" id="register" action="register.php">
       <input type="email" name="username" placeholder="Username (typically your email)"/>
       <input type="password" name="password" placeholder="password"/>
       <input type="text" name="hint" placeholder="password hint"/>
       <button type="submit" form="register" name="register" >Sign Up</button>
       <p class="error"><?php
echo ($GLOBALS['flag']) ? $error : null;
?></p>
       <p class="message">Already registered? <a href="#">Sign In</a></p>
     </form>
     <form class="login-form" method="post" id="login">
       <input type="email" name="username" placeholder="Username (typically your email)" value="<?php
echo $username = htmlentities((isset($_POST['username']) ? $_POST['username'] : null));
?>"/>
       <input type="password" name="password" placeholder="password"/>
       <button type="submit" form="login" name="submit" >login</button>
       <p class="error"><?php
echo ($GLOBALS['flag']) ? $error : null;
?></p>
       <p class="forgot">Forgot your password? <a href="login.php#forgot" id="dspforget" >  Hint</a></p>
       <p class="message">Not registered? <a href="#">Create an account</a></p>
     </form>
     <form class="forgot_pass" method="post" id="forgot">
       <input type="email" name="username" placeholder="Username (typically your email)" value="<?php
echo $username = htmlentities((isset($_POST['username']) ? $_POST['username'] : null));
?>"/>
       <button type="submit" form="forgot" name="forgot">Send Email</button>
       <p class="error"><?php
echo ($GLOBALS['flag']) ? $ferror : null;
?></p>
       <p class="message">Already registered? <a href="#" id="sgnin">Sign In</a></p>
       <p class="message">Not registered? <a href="#" id="crt">Create an account</a></p>
     </form>
   </div>
 </div>
     <script src='jquery.js'></script>
     <script>

     $('.message a').click(function(){
        $('.register-form, .login-form').animate({height: "toggle", opacity: "toggle"}, "slow");
     });
     $('#dspforget').click(function(){
        $('.forgot_pass').animate({height: "toggle", opacity: "toggle"}, "slow");
        $(".login-form").hide();
     });

     $( "#sgnin" ).click(function() {
       $( ".login-form" ).show( "slow" );
       $(".forgot_pass").hide();
        $(".register-form").hide();
       });

       $( "#crt" ).click(function() {
         $( ".register-form" ).show( "slow" );
         $(".forgot_pass").hide();
          $(".login-form").hide();
         });

     </script>
   </body>
 </html>
