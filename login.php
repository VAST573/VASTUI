<!DOCTYPE html>
<?php
/**
 * This file is the login page. A user will enter thier email and passwrd to attempt to log in.
 * If the user has an account, they will be send to the home page/index,php.
 * if they don't they can clikc the create accoutn page which will link them to the
 * create account page. 
 *
 * @Author
 * @Version 2020.04.19
 */
session_start();
$message ="";# message showed on the screen if a feild is blank or an invalid email or password was entered.

if (!include('dbh.php')) {
	die('error finding connect file');
}
try {
	$dbh = ConnectDB(); //my sql connection. used to run querys
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // used to catch and show erros.

	/** if a user presses enter or clicks the login button. process their inoput.
	 *   first check if a feild is empty, if not run the query using their email
	 *   and password inputs. then check if the query returns a rwo count > 0.
	 *   row count greater than 0 means account exists else
	 *   account does not and prompt user.
	 *   If account esists, send them to the index.php page.
	 */   
	if(isset($_POST["Login"])){
		if(empty($_POST["user_email"]) || empty($_POST["user_password"])){
			$message = 'All feilds are requried';
		}else {
			$query = "SELECT * FROM User_Data WHERE Email = :email and Hashes = MD5(:password)";
			$stmt = $dbh->prepare($query);
			$stmt->execute(
				array(
				'email' => $_POST["user_email"],
				'password' => $_POST["user_password"]
			    )
		       );
		$count = $stmt->rowCount();
		if($count > 0){
			$_SESSION["user_email"] = $_POST["user_email"];
			$_SESSION["user_password"] =  $_POST["user_password"];
			header("location:index.php");
		} else {
			$message = 'Invalid email or password';
		  }
		
		}

        }

} catch(PDOException $error){
	$message = $error->getMassage();
}
?>

<html>
	<head>
	<title>Login Page</title>
	<link rel="stylesheet" href="cssFiles/loginStyles.css">
	</head>
	<body>
		<body style="background-color:darkgray; 
			     	background: url(cssFiles/output-onlinejpgtools.jpg) no-repeat center center fixed;  
				-webkit-background-size: cover;
  				-moz-background-size: cover;
  				-o-background-size: cover;
				background-size: cover;
				drop-shadow(32px 32px 0px black)
				opacity(30%)">
		<div class=center>
			<br>
			<?php
   			if(isset($message)){
				if($message == "All feilds are requried" || $message == "Invalid email or password"){
					echo "<script>alert('$message')</script>";
				}
   			}
			?>
			<span id="vast">V.A.S.T.</span>
			<br><br><br>
			<span id="loginTitle">Login To Account</span>
			<br><br><br>
			<form action = "" method = "post">
                  	  	<label>Email  :</label><input type = "text" name = "user_email" class = "box" id = "email" /><br /><br />
                  		<label>Password  :</label><input type = "password" name = "user_password" class = "box" id="password"/><br/><br />
                  		<input style ="background-color: #8e81ff;" type = "submit" value = " Login " id="login" name = "Login" /><br />
               		</form>
			<script>
			</script>
			<br><br>
			<a href="createAccount.php" class="button" id="create">Create Account</a>
		</div>
	</body>
</html>
