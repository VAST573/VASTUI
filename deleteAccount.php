<?php
/** This file, per user request and confirmation, on the homepage, will delete the users accoutn from our system.
 *
 *@Author Isaiah Doyle 
 *@Version 2020.04.19
 */
session_start(); # starts tthe session 
if(isset($_SESSION["user_email"])){
       // echo '<h3> ------------------------Welcome - '.$_SESSION["user_email"].'</h3>';
}else{
	header("Location:index.php");
}

if (!include('dbh.php')) {
        die('error finding connect file');
}
try{
	$dbh = ConnectDB(); //my sql connection. used to run querys
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

	/** delete from user_keyword table */
	$delQuery = "DELETE FROM User_Keyword WHERE user_id =$userid";
	$delstmt = $dbh->prepare($delQuery);
	$delstmt->execute();

	/** now delete from User_data table*/
	$finalDelQuery ="DELETE FROM User_Data where user_id=$userid";
	$finalDelstmt = $dbh->prepare($finalDelQuery);
	$finalDelstmt->execute();
	
	/** account is deleted, redirect to log in page */
	header("location:logout.php");

}catch(PDOException $error){
	echo $error->getMessage();

}
?>

