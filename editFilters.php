<?php
session_start();
if(isset($_SESSION["user_email"])){
        echo '<h3> -------------------------'.$_SESSION["user_email"].'</h3>';
}else{    
	header("Location:login.php");
}
if (!include('dbh.php')) {
        die('error finding connect file');
}

$message = "";
$listofKeywords = ['Apple','Google','kill-port-process','Microsoft','IDM','IBM','Cisco','Debian','Redhat','Oracle','Adobe','WordPress','Drupal','FluxBB','UseBB','Canonical','Amazon','Linux','Mozilla','Wireshark','SUSE','Apache','Mcafee','PHP','Windows','Firefox','iPadOS','Netgear','iOS','macOS'];

if(isset($_GET['userFilterArr']) && isset($_GET["alertFrequency"])) {
	
	$userFil = $_GET['userFilterArr'];
	$userfreq = $_GET["alertFrequency"];
	$freq = intval($userfreq);

	$userFilArr = explode(',',$userFil);
	$Arrlength = count($userFilArr);

	if($Arrlength < 2 || $freq < 1 ){
		$message = "Make sure you have at least one filter selected and have an alert frequency selected in the top right as well";
	}else{//user has filters, add them to database.
		try{
			$dbh = ConnectDB(); //my sql connection. used to run querys
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

			 /** update the users frequency */
			$upquery = "UPDATE User_Data SET Frequency =$freq WHERE user_id =$userid";
			$upstmt = $dbh->prepare($upquery);
			$upstmt->execute();

		       /** now lopp through the arrays and add each to the database */
			 foreach($userFilArr as $value){
				 if(in_array($value,$listofKeywords)){
					 $keywordID = array_search($value, $listofKeywords) + 1;
 					 // check if user already has that filter selected already 
					 $selectQuery = "SELECT * FROM User_Keyword WHERE user_id =$userid AND keyword_id =$keywordID";
					 $selectstmt = $dbh->prepare($selectQuery);
					 $selectstmt->execute();
					 $count = $selectstmt->rowCount();
					 
					 if($count < 1){
						 $insertQuery = "INSERT INTO User_Keyword (user_id, keyword_id) VALUES ($userid, $keywordID)";
					 	 $instmt = $dbh->prepare($insertQuery);
					 	 $instmt->execute();
					 }
				 }
			 }
			 $_SESSION['userHasFilters'] = 1;
			 header("Location:index.php"); 
		} catch(PDOException $error){
			$message = $error->getMessage();
		}
	}
}
if(isset($_GET['removeFilterArr']) && isset($_GET["removeFrequency"])) {
	$userFil = $_GET['removeFilterArr'];
        $userfreq = $_GET["removeFrequency"];
	$freq = intval($userfreq);

	//var_dump($userFilArr);
        $userFilArr = explode(',',$userFil);
	$Arrlength = count($userFilArr);

        if($Arrlength < 2 ){
                $message = "Make sure you have at least one filter selected to be removed.";
	}else{//user has filters, add them to database.
		try{
			 $dbh = ConnectDB(); //my sql connection. used to run querys
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

			 /** now delete filters from the database */
			 foreach($userFilArr as $value){
                                 if(in_array($value,$listofKeywords)){
					 $keywordID = array_search($value, $listofKeywords) + 1;

					 // check if user already has that filter selected already
                                         $selectQuery = "SELECT * FROM User_Keyword WHERE user_id =$userid AND keyword_id =$keywordID";
                                         $selectstmt = $dbh->prepare($selectQuery);
                                         $selectstmt->execute();
					 $incount = $selectstmt->rowCount();
					 
					 if($incount > 0){
						 $delQuery="DELETE FROM User_Keyword WHERE user_id =$userid AND keyword_id =$keywordID";
						 $delstmt = $dbh->prepare($delQuery);
						 $delstmt->execute();
					 }

				 }
			 }
			  $_SESSION['userHasFilters'] = 2;
			  header("Location:index.php");
		}catch(PDOException $error){
			 $message = $error->getMessage();
		}

	}
}
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="cssFiles/checkBoxStyles.css">
<body style="background-color:darkgray">
<head>
<?php
if(isset($message)){
	$e1 = "Make sure you have at least one filter selected and have an alert frequency selected in the top right as well";
	if($message == $e1 || !empty($message)){
		echo "<script>alert('$message')</script>";
	}
}

?>
<div style = "position:relative; left:180px; top:2px;">
         <fieldset style="width:250px; border:0;">
		<h1>Edit Filters</h1>
		</fieldset>
      </div>

    <link id="themecss" rel="stylesheet" type="text/css" href="//www.shieldui.com/shared/components/latest/css/light/all.min.css" />
    <script type="text/javascript" src="//www.shieldui.com/shared/components/latest/js/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="//www.shieldui.com/shared/components/latest/js/shieldui-all.min.js"></script>



<div style = "position:fixed; right:50px; top:30px;">
<select id="alerts">
  <option value="0">Alert Frequency</option>
  <option value="2">2 Hours</option>
  <option value="4">4 Hours</option>
  <option value="8">8 Hours</option>
  <option value="12">12 Hours</option>
   <option value="24">24 Hours</option>
</select>

</div>
<div style = "position:fixed; right:200px; top:30px;">
<h4> Your Selected Filters </h4>
<?php
try{
	$dbh = ConnectDB(); //my sql connection. used to run querys
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // used to catch and show erros.
	/** grab the users id*/
        $query ="SELECT * FROM User_Data WHERE Email = :email";
        $stmt = $dbh->prepare($query);
        $stmt->execute(
                array(
                        'email' => $_SESSION['user_email']
                )
        );
        $result = $stmt->fetch();
        $userid = $result['user_id'];

        $grabukQuery = "SELECT * FROM User_Keyword WHERE user_id =$userid";
        $grabukstmt = $dbh->prepare($grabukQuery);
        $grabukstmt->execute();

	        $grabukQuery = "SELECT * FROM User_Keyword WHERE user_id =$userid";
        $grabukstmt = $dbh->prepare($grabukQuery);
        $grabukstmt->execute();

        foreach($grabukstmt->fetchall() as $userKeywordData){
                $keyquery ="SELECT * FROM Keyword WHERE keyword_id = :key";
                $keystmt = $dbh->prepare($keyquery);
                $keystmt->execute(
                        array(
                                'key' => $userKeywordData['keyword_id']
                        )
                );
                $output = $keystmt->fetch();
                $key = $output['keys'];
                echo " <li>". $key . "</li>\n";
        }
}catch(PDOException $error){
        echo $error->getMessage();
}
?>
</div>


</head>


<body class="theme-light">
<box1>
<td colspan="5" rowspan="6" nowrap="nowrap" autoflow="auto"> <fieldset style="border:0;">
<div style="overflow: scroll; width: 800px; height:335px; white-space: inherit; border:0;">
<div class="container">
    <div id="treeview"></div>
    <br />
    <p><span id="checkedCount"></span></p>
</div>
<script type="text/javascript">
function addSelection(){
	 // find all LI elements in the treeview and determine how many are checked
        var checkedCount = $("#treeview").swidget("TreeView").element.find("li").filter(function () {
                return $("#treeview").swidget("TreeView").checked($(this));
        }).length;

        //now make a arry and stores the values of the checked check boxes in a tmp array
        var tmp = [];
        $("#treeview").swidget("TreeView").element.find("li").each(function() {
                if($("#treeview").swidget("TreeView").checked($(this))){
                        tmp.push($(this).first().text())
                }
        });

        //now take out the dups out of the array
        let userSet = new Set(tmp);
        var finalArr = [];
        for (elem of userSet){
                finalArr.push(elem);
        }
        var freq = document.getElementById("alerts").value;

	//now pass the array and freq  to php 
	window.location.href =" editFilters.php?userFilterArr=" + finalArr +  "&alertFrequency=" + freq;	
}
function removeSelection(){
	 // find all LI elements in the treeview and determine how many are checked
        var checkedCount = $("#treeview").swidget("TreeView").element.find("li").filter(function () {
                return $("#treeview").swidget("TreeView").checked($(this));
        }).length;

        //now make a arry and stores the values of the checked check boxes in a tmp array
        var tmp = [];
        $("#treeview").swidget("TreeView").element.find("li").each(function() {
                if($("#treeview").swidget("TreeView").checked($(this))){
                        tmp.push($(this).first().text())
                }
        });

        //now take out the dups out of the array
        let userSet = new Set(tmp);
        var finalArr = [];
        for (elem of userSet){
                finalArr.push(elem);
        }
	var freq = document.getElementById("alerts").value;
	//now pass the array and freq to php 
        window.location.href =" editFilters.php?removeFilterArr=" + finalArr +  "&removeFrequency=" + freq;    
}
    jQuery(function ($) {
        function onCheck() {
		// find all LI elements in the treeview and determine how many are checked
        	var checkedCount = $("#treeview").swidget("TreeView").element.find("li").filter(function () {
                	return $("#treeview").swidget("TreeView").checked($(this));
		}).length;

	   	$("#checkedCount").html(checkedCount + " items checked");
	}
        $("#treeview").shieldTreeView({
            checkboxes: {
                enabled: true,
                children: true
            },
            events: {
                check: onCheck
            },
            dataSource: {
                data: [
                    {
                        text: "All",
                        expanded: true,
                        items: [
                         {
                                text: "Adobe",
                                
                            },
                            
                            {
                                text: "Amazon",
                                
                            },
                           
                            {
                                text: "Apple",

                                expanded: true,
                                items: [
                                    {
                                        text: "iOS",

                                    },
                                    {
                                        text: "iPadOS",

                                    },
                                    {
                                        text: "macOS",

                                    }
                                ]
                            },
                            
                            {
                                text: "Cisco",

                            },
                            
                            
                            {
                                text: "Google",

                            },
                            
                            
                            {
                                text: "IBM",
                            },
                            
                            
                       {
                                text: "Linux",

                                expanded: true,
                                items: [
                                    {
                                        text: "Debian",

                                    },
                                    {
                                        text: "Redhat",

                                    },
                                      {
                                        text: "Suse",

                                    },
                                ]
                            }, 
                            
                            {
                                text: "McAfee",
                            },
                            
                             {
                                text: "Microsoft",
                                expanded: true,
                                items: [
                                {
                                	text: "Windows",
                                },
                                {
                                	text: "IDM",
                                 }
                               ]
                            },
                            
                            {
                                text: "Mozilla",

                                expanded: true,
                                items: [
                                    {
                                        text: "Firefox",


                                    },
                                ]
                            },     
                            
                             {
                                text: "Netgear",
                            },
                            
                            {
                                text: "Oracle",
                            },
                            
                              {
                                text: "Other",

                                expanded: true,
                                items: [
                                    {
                                        text: "Apache",

                                    },
                                    {
                                        text: "Canonical",

                                    },
                                      {
                                        text: "Drupal",

                                    },
                                                                          
                                    {
                                        text: "FluxBB",

                                    },
                                                                          {
                                        text: "kill-port-process",

                                    },
                                    {
                                        text: "Php",

                                    },
                                    {
                                        text: "UseBB",

                                    },
                                                                          {
                                        text: "Wireshark",

                                    },
                                                                          {
                                        text: "WordPress",

                                    },
                                ]
                            }, 
                            
                            
                            
                            
                            
                            
                            
                            
                        ]
                    }
                ]
            }
        });
        onCheck();
    });
</script>
</div>
</fieldset></td>
</box1>

<div class ="helpUserMessage" style = "position= relative; margin-left: 20%;">
<p>This page allows you to remove selected filters and update more filters along with your frequency. To add more filters, First select the filters you want to add. Next click your same alert frequency or a new one in the top right. Finally, click the "Add Selected" button. Follow this same process for 
removing filters but click the "Remove Selected" button instead. After clicking one of the buttons you will be redirected back to the home page if everything is done correctly.</p>

<button onclick="addSelection()">Add Selected</button>
<button onclick="removeSelection()">Remove Selected</button>
</div>

<div class="sidenav">
<a href="index.php">Home Page</a>
<a href="changePassword.php">Change Password</a>
<a href="editFilters.php">Edit Filters</a>
<a href="about-us.php">About</a>
</div>

</body>

</html>

