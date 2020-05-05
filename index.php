<?php
        session_start();
if(isset($_SESSION["user_email"])){
        echo '<h3> ------------------------Welcome - '.$_SESSION["user_email"].'</h3>';
}else{
      header("Location:login.php");
}

?>
<!DOCTYPE html>
<html>
	<head>
		 <body style="background-color:darkgray;">
                <style>
                        .sidenav {
                                height: 100%;
                                width: 160px;
                                position: fixed;
                                top: 0;
                                left: 0;
                                background-color: #8e81ff;
                                overflow-x: hidden;
				padding-top: 20px;
                        }

                        .sidenav a {
                          padding: 6px 8px 6px 16px;
                          text-decoration: underline;
                          font-size: 17px;
                          color: blue;
                          display: block;
                        }

                        .sidenav a:hover {
                          color: white;
                        }

                        .vast {
                                color: black;
                                left: 50%;
                                top: 10%;
                                text-align: center;
                                font-size: 35px;
                                position: relative;
                                border: 1px solid;
                                width: 150px;
                        }

                        .topRight {
                                right: 2%;
                                position: fixed;
                        }

                        .topRight a {
                                padding-right: 20px;
                                text-decoration: underline;
                                color: blue;
                        }

                        .topRight a:hover {
                                color: grey;
                        }

                        .topRight button {
                                padding-right: 10px;
                        }
                        
                        .twitter_feed {
                            margin: auto;
                            width: 30%;
			    padding: 10px;
			    position: absolute;
			    left: 40%;
			    top: 25%;
                        }
                </style>
        </head>

        <body>
                <div class="sidenav">
			<a href="index.php">Home</a>
			<!--<a href="login.php">Login Page</a>-->
                        <a href="changePassword.php">Change Password</a>
                        <a href="editFilters.php">Edit Filters</a>
			<a href="about-us.php">About</a>
                </div>
                <div class="vast">V.A.S.T.</div>
                
                 <div class="twitter_feed">
                    <a class="twitter-timeline"
                      data-width="800" 
                      data-theme="dark" 
                      href="https://twitter.com/CVEnew?ref_src=twsrc%5Etfw">Tweets by CVEnew</a> <script 
                      async src="https://platform.twitter.com/widgets.js" 
                      charset="utf-8">
		    </script>
		</div>

                <div class="topRight">
			<a href="logout.php">Log out</a>
			<a href="deleteAccount.php" onclick="return confirm('Are you sure, you want to delete your account? Deleting your account will remove you from our system')">Delete Account </a>
                </div>
        </body>
 </html>		

