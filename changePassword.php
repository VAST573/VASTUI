<!DOCTYPE html>
<?php
/** This file is the change password page. It contain php code that handles the user actions and html code
 * that is the user interface of the web page. The only purpose of this page is to change an existsing accounts
 * password. 
 *
 *@Author Isaiah Doyle
 *@Version 2020.04.21
 */
session_start();
if(isset($_SESSION["user_email"])){

}else{
	header("Location:login.php");
}

$message ="";
$userid =0;
if (!include('dbh.php')) {
	die('error finding connect file');
}
try{
	if(isset($_POST["Change"])){
		//check if old passwerd matches the one on file
		$uspas = $_SESSION['user_password'];
		if($_POST['old_password'] != $uspas){
			$message = "The password in the Old Password field does not match the one associated with this account";
		}	
		elseif($_POST['new_password'] != $_POST['confirm_new_password']){
			$message = 'new Password and Confirm new Password do not match';
		}	//check is password is valid
		elseif (strlen($_POST['new_password']) > 20 || strlen($_POST['new_password']) < 5) {
			$message = 'Password must be between 5 and 20 characters long!';
		} 
		else {

			 $dbh = ConnectDB();
			 $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // used to catch and show erros.
			 
			 /** grab the users id */
			 $query ="SELECT * FROM User_Data WHERE Email = :email";
			 $stmt = $dbh->prepare($query);
			 $stmt->execute(
				 array(
					 'email' => $_SESSION['user_email']
				 )
			 );
			 $result = $stmt->fetch();
			 $userid = $result['user_id'];


				 /** now grab and cheack if new password is indeed a new one */
			 	$checkPassquery="SELECT * FROM User_Data WHERE Hashes = MD5(:password) AND Email = :email";
			 	$stmt = $dbh->prepare($checkPassquery);
			 	$stmt->execute(
					 array(
						 'password' => $_POST['new_password'],
					 	'email' => $_SESSION['user_email']
                                 	)
				);
			 	$count = $stmt->rowCount();
			 	if($count > 0){
					 $message = "New password is the same as the old password.";
			 	}
			 
			 	/**password is a new one, update their password to their desired passwrd*/
			 	else{
					$updateQuery ="UPDATE User_Data SET Hashes = MD5(:password) WHERE user_id= $userid";
				 	$stmt = $dbh->prepare($updateQuery);
				 	$stmt->execute(
						 array(
							 'password' =>  $_POST['new_password']
					 	)
				 	);
				 	$_SESSION["user_id"] = $userid;
				 	header("location:index.php");
				 	echo '<script language="javascript">';
                                 	echo 'alert("Password was successfully chanegd")';
                                 	echo '</script>';
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
			<button class="button" onclick="window.location.href = 'index.php';">Back to Homepage</button>
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
			<h1>Change Password</h1>
			<form action="changePassword.php" method="post" autocomplete="off">
				<label for="ca_email" style="background-color: #8e81ff;">
					<i class="fas fa-envelope"></i>
				</label>
				<input type="password" name="old_password" placeholder="Old Password" id="old_passwordID" required>
				<label for="ca_password" style="background-color: #8e81ff;">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="new_password" placeholder="New Password" id="new_PasswordID" required>
				<label for="con_ca_password" style="background-color: #8e81ff;">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="confirm_new_password" placeholder="Confirm New Password" id="con_new_passwordID" required>
				<input type="submit" value="Save Changes" name ="Change" style="background-color: #8e81ff;">
			</form>
	        </div>
	</body>
</html>

