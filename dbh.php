<?php
/** 
 * This file makes a connection to our mysql database on the ubuntu server.
 *
 *@Author Isaiah Doyle and Jack Myers
 *@Version 2020.04.21
 */
ConnectDB();

//This function makes the connection to the database and returns a data base handeler 
function ConnectDB() {

   /*** mysql server info ***/
    $hostname = '127.0.0.1';  // Local host, i.e. running on elvis
    $username = 'cyberuser';           // Your MySQL Username goes here
    $password = 'cyber';           // Your MySQL Password goes here
    $dbname   = 'VAST';           // Repeat your MySQL Username here

   try {
       $dbh = new PDO("mysql:host=$hostname;dbname=$dbname",
                      $username, $password);
    }
    catch(PDOException $e) {
        die ('PDO error in "ConnectDB()": ' . $e->getMessage() );
    }

    return $dbh; 
}

?>
