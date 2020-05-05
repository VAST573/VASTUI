<?php
        session_start();
if(isset($_SESSION["user_email"])){
        echo '<h3> ------------------------'.$_SESSION["user_email"].'</h3>';
}else{
//      header("Location:login.php");
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

	if($Arrlength < 1 || $freq < 1 ){
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
					// echo "<script>alert('$userid');</script>";
					 $insertQuery = "INSERT INTO User_Keyword (user_id, keyword_id) VALUES ($userid, $keywordID)";
					 $instmt = $dbh->prepare($insertQuery);
					 $instmt->execute();
				 }
			 }
			 $_SESSION['userHasFilters'] = 1;
			 header("Location:index.php"); 
		} catch(PDOException $error){
			$message = $error->getMessage();
		}
	}
}

?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="cssFiles/checkBoxStyles.css">
<body style="background-color:lightgray">
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
         <fieldset style="width:250px">
		<h1>Account Filters</h1>
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


</head>


<body class="theme-light">
<box1>
<td colspan="5" rowspan="6" nowrap="nowrap" autoflow="auto"><fieldset>
<div style="overflow: scroll; width: 800px; height:335px; white-space: inherit;">
<div class="container">
    <div id="treeview"></div>
    <br />
    <p><span id="checkedCount"></span></p>
</div>
<script type="text/javascript">
function showChecked(){
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
	//grab the selected frequency
	var freq = document.getElementById("alerts").value;

	//now pass the array and its length  to php 
	window.location.href ="checkBox.php?userFilterArr=" + finalArr +  "&alertFrequency=" + freq;	
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

<div class"helpMessage" style = "position:relative; margin-left:20%; z-index: -1;">
<p>Please select your desired filters in the box above along with a alert frequecy option in the top right. After selection click the "Sumbit Filters" button below. You must choose at least one filter and a frequncy option to complete. Alerts will be sent via email associated with this account.  </p>
</div>
<div style = "position: relative; margin-left: 20%;">
<button onclick="showChecked()">Submit Filters</button>
</div>

<div class="sidenav">
<a href="index.php">Home Page</a>
<a href="changePassword.php">Change Password</a>
<!--<a href="checkBox.php">Account Filters</a>-->
<a href="about-us.php">About</a>
</div>

</body>

</html>

