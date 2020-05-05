<!DOCTYPE html>
<?php
/**
 * This file is the create account page. It contains php code that handles the user creation of the account
 * and html code that is the user interface of the webpage.
 * A user can enter their email and a password for the account. If they have a valid email and 
 * a valid password, the account will be made, stored in the database, and will redirect them to
 * the index.php/ home page.
 *
 *@Author Isaiah Doyle
 *@Version 2020.04.21
 */
session_start();

$message ="";
if (!include('dbh.php')) {
	die('error finding connect file');
}
try{
	/**grabs the user information from the web page whne a user clicks the creat account button on presses enter */
	if(isset($_POST["Create_Account"])){
		// check if both password entries are the same
	       	if($_POST['create_account_password'] != $_POST['confirm_password']){
			$message = 'Password and Confirm Password do not match';
		}
		//checks is the email they entered is a valid email
		elseif (!filter_var($_POST['create_account_email'], FILTER_VALIDATE_EMAIL)) {
		       	$message = 'Email is not valid!';
		}
		//checks is password is a valid password
         	elseif (strlen($_POST['create_account_password']) > 20 || strlen($_POST['create_account_password']) < 5) {
                	 $message = 'Password must be between 5 and 20 characters long!';
		}
		//create their account
		else{

			 $dbh = ConnectDB();
			 $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // used to catch and show erros.
			 $query ="SELECT Email, Hashes FROM User_Data WHERE Email = :email";
			 $stmt = $dbh->prepare($query);
			 $stmt->execute(
				 array(
					 'email' => $_POST['create_account_email']
				 )
			 );
			 $count = $stmt->rowCount();
			 if($count > 0){
				 //user already exists
				 $message = " Account with that Email already exists.";
			 }
			 else{
				 date_default_timezone_set("America/New_York");
				 $dateObject = date("Y-m-d");
				 $strDate = strval($dateObject);
				 $strDate .= " 00:00:00";
				 $freq = 2;
				 //insert new account
				 $insertQuery ="INSERT INTO User_Data (Email, Hashes, Frequency, Time_Received) Values (:email, MD5(:hashes), $freq, '$strDate')";
				 $stmt = $dbh->prepare($insertQuery);
				 $stmt->execute(
					 array(
						 'email' =>  $_POST['create_account_email'],
						 'hashes' => $_POST['create_account_password']
					 )
				 );
				 $_SESSION["user_email"] = $_POST["create_account_email"];
				 header("location:newAccountFilters.php");
			 }
	            }
	}
 } catch(PDOException $error){
	 $message = $error->getMassage();
 	}

?>
<html>
	<head>
		<meta charset="utf-8">
		<title>Register</title>
		<link rel="stylesheet" href="/cssFiles/createAccountStyle.css">
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

		<div align="right">
			<button class="button" onclick="window.location.href = 'login.php';">Login to Account</button>
				<style>
       		  			.button {
        					background-color: #8e81ff;
         					border: none;
         					color: white;
         					padding: 20px 34px;
         					text-align: center;
         					text-decoration: none;
         					display: inline-block;
         					font-size: 20px;
         					margin: 4px 2px;
         					cursor: pointer;
         					}
      			       </style>	
		</div>
		<div class="register">
			<br>
			<?php
			if(isset($message)){
				if(!empty($message)){
					echo "<script>alert('$message')</script>";
				}
			}
			?>
			<h1>Register</h1>
			<form action="createAccount.php" method="post" autocomplete="off">
				<label for="ca_email" style="background-color: #8e81ff;">
					<i class="fas fa-envelope"></i>
				</label>
				<input type="text" name="create_account_email" placeholder="Email" id="ca_email" required>
				<label for="ca_password" style="background-color: #8e81ff;">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="create_account_password" placeholder="Password" id="ca_password" required>
				<label for="con_ca_password" style="background-color: #8e81ff;">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="confirm_password" placeholder="Confirm Password" id="con_ca_password" required>
				<input type="submit" value="Create Account" name ="Create_Account" style="background-color: #8e81ff;">
			</form>
	        </div>
	</body>
</html>
