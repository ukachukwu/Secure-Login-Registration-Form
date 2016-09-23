<?php ob_start(); ?>
<?php
require('myclasses.php');
$flag=false;
$error='';
$regerror='';
$ferror='';
if(isset($_POST['register'])){

$username = htmlentities(strtolower((isset($_POST['username']) ? $_POST['username']: null)));
$password = (isset($_POST['password']) ? $_POST['password']: null);
$hint = htmlentities((isset($_POST['hint']) ? $_POST['hint']: null));
if(!empty($username))
	{

		if(!empty($password)){
      $db = new dbLink('databaseportfolio');
			$link= $db->getlink();
		  $username = mysqli_real_escape_string($link,$username);
      $result = $db->query("SELECT username FROM users WHERE username='" . $username . "'");

      if($db->emptyResult($result)){
      						//User already exists
      						$flag = true;
      						$regerror = 'Username already exists!';
      }else{
        $encryptedpass = crypt($password, $username);
			  $hint = mysqli_real_escape_string($link,$hint);
				$hash = md5( rand(0,100000) );
        $query= "INSERT INTO `users`(`username`, `password`, `role`, `passwordHint`,`hash`) VALUES ('$username','$encryptedpass','user','$hint','$hash') ";
				$result = $db->query($query);

				$to = $username;
				$subject = 'Signup | Verification';
				$message = 'From : Demo login page at https://arjunvegda.com/portfolio/demologin/login.php'. "\r\n";
				$message .= 'Thanks for signing up !

Your account has been created. You will be able to login after verifying your email.

------------------------
Username: '.$username.'
------------------------

Please click this link to activate your account:
https://arjunvegda.com/portfolio/demologin/verify.php?email='.$username.'&token='.$hash.'';
				//$message = wordwrap($message,70,"\r\n");
				$headers = "From: Arjun Vegda <arjun@arjunvegda.com> \r\nReply-to: arjun@arjunvegda.com";
				mail($to,$subject,$message);
			//	echo ($message);
				header("Location: login.php?verify=".$username);
			}


    }else{
			$flag=true;
    $regerror="Please enter a password";
    }
  }else {
			$flag=true;
    $regerror="Please enter an username";
  }

}// register submit ends
 ?>
 <!DOCTYPE html>
 <html >
   <head>
     <meta charset="UTF-8">
     <title>Register</title>
       <link rel="stylesheet" href="style.css">
			 <style>
       .form .login-form {
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
     <h1> Demo registration page </h1>
		 <?php if(isset($_POST['forgot'])){

       $username= htmlentities(trtolower(trim((isset($_POST['username']) ? $_POST['username']:null))));
       if(empty($username)){
				 echo "<h3>Username field was empty. Please input data or Sign up !<h3>\n";
       }else{
          $db = new dbLink('databaseportfolio');
					$link= $db->getlink();
				  $username = mysqli_real_escape_string($link,$username);
          $result = $db->query("SELECT DISTINCT username, passwordHint FROM users WHERE username='" . $username . "'");
          if($db->emptyResult($result)){
            $row = mysqli_fetch_assoc($result);
            if($row["username"] == $username)
            {
                  $to = $username;
									$subject = 'Password recovery ';
				          $message = 'From : Demo login page at https://arjunvegda.com/portfolio/demologin/login.php'. "\r\n";
                  $message .= 'Your password hint is: ' . $row['passwordHint'];
				          $message = wordwrap($message,70,"\r\n");
                  $headers = "From: Arjun Vegda <arjun@arjunvegda.com> \r\nReply-to: arjun@arjunvegda.com";
				          mail($to,$subject,$message);
									header("Location: login.php?sent=".$username);

            //  echo "<h3>". $row['passwordHint'] . "<h3>";

            }else{
              echo "<h3>Unable to find <span class=\"success\"> $username</span> in our database ! <br>Sign up !<h3>\n";
            }

          }else{
              echo "<h3>Unable to find <span class=\"success\"> $username</span> in our database ! <br>Sign up !<h3>\n";
          }


       }
     }

     ?>
     <div class="login-page">
   <div class="form">
     <form class="register-form" method="post" action="register.php" id="register">
       <input type="email" name="username" placeholder="Username (typically your email)" value="<?php echo $username = htmlentities((isset($_POST['username']) ? $_POST['username']: null)) ;?>"/>
       <input type="password" name="password" placeholder="password"/>
       <input type="text" name="hint" placeholder="password hint"/>
       <button type="submit" form="register" name="register" >Sign Up</button>
       <p class="error"><?php echo ($GLOBALS['flag']) ? $regerror: null; ?></p>
       <p class="message">Already registered? <a href="#">Sign In</a></p>
     </form>
     <form class="login-form" method="post" action="login.php" id="login">
       <input type="text" name="username" placeholder="Username (typically your email)"/>
       <input type="password" name="password" placeholder="password"/>
       <button type="submit" form="login" name="submit">login</button>
       <p class="error"><?php echo ($GLOBALS['flag']) ? $error: null; ?></p>
			 <p class="forgot">Forgot your password? <a href="#" id="dspforget" >  Hint</a></p>
			 <p class="message">Not registered? <a href="#">Create an account</a></p>
     </form>
		 <form class="forgot_pass" method="post" id="forgot">
       <input type="text" name="username"  placeholder="Username (typically your email)" value="<?php echo $username = htmlentities((isset($_POST['username']) ? $_POST['username']: null)) ;?>"/>
       <button type="submit" form="forgot"  name="forgot">Send Email</button>
       <p class="error"><?php echo ($GLOBALS['flag']) ? $ferror: null; ?></p>
       <p class="message">Already registered? <a href="#" id="sgnin">Sign In</a></p>
       <p class="message">Not registered? <a href="#" id="crt">Create an account</a></p>
     </form>
   </div>
 </div>
     <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
		 <script>
 		$('.message a').click(function(){
 			 $('.register-form, .login-form').animate({height: "toggle", opacity: "toggle"}, "slow");
 		});
 		$('#dspforget').click(function(){
 			 $('.login-form, .forgot_pass').animate({height: "toggle", opacity: "toggle"}, "slow");
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
