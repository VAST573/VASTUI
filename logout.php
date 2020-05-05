<?php
/** This file allowas a user to log out and sends them back to the login page when they click the log out button
 *
 *@Author Isaiah Doyle 
 *@Version 2020.04.19
 */
session_start(); # starts tthe session 
session_destroy(); # ends the seession
header("location:login.php"); # slinks to the login page
?>
