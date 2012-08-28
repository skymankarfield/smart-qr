<?php 
session_start();

$rel_path_img="_src/_img/";
$rel_path_js="_src/_js/";
$rel_path_css="_src/_css/";
$rel_path_php="_src/_php/";
$rel_path_menulinks="";
/////////////////////////////////

$_SESSION['menu']="action";

include($rel_path_php."header.php"); 

include($rel_path_php."_misc/decrypt.php");
include($rel_path_php."_misc/encrypt.php");
include($rel_path_php."mysqlConnection.php");
include($rel_path_php."_session/addUser.php");

$tag_ID="";
$event_ID="";
$display="";
$message="";
$link=false;
$error_code="";
$email="";
$account_ID=0;

if(isset($_GET['tagid']) && isset($_GET['eventid']) && $_GET['tagid']!="" && $_GET['eventid']!="" && is_numeric($_GET['tagid']) && is_numeric($_GET['eventid']))
{
	$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);
	if(!$link)
	{	
		echo "Error connecting to MySQL";
		exit(0);
	}
	
	$query = "SELECT * FROM tags WHERE tag_ID=".mysqli_real_escape_string($link,decrypt($_GET['tagid']))." AND event_ID=".mysqli_real_escape_string($link,decrypt($_GET['eventid']))."";
	@mysqli_real_query($link,$query);
	$result = mysqli_store_result($link);
	if(mysqli_num_rows($result)==1)
	{
		$row = mysqli_fetch_assoc($result);
		if(trim($row['fullName'])=="")
		{
			echo "QR CODE ID= ".decrypt($_GET['tagid']);
			exit(0);
		}
	}else
	{
		//SOMETHING IS NOT OK, GET OUT OF HERE
		exit(0);
	}

}

if(isset($_SESSION['accountID']))
{
	if(isset($_GET['tagid']) && isset($_GET['eventid']) && $_GET['tagid']!="" && $_GET['eventid']!="" && !isset($_GET['actionform']) && is_numeric($_GET['tagid']) && is_numeric($_GET['eventid']))
	{
		$tag_ID=decrypt($_GET['tagid']);
		$event_ID=decrypt($_GET['eventid']);
	
		if($_SESSION['scan']==1)
		{
			/*$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);
			if(!$link)
			{	
				echo "Error connecting to MySQL";
				exit(0);
			}
			*/
			$query = "SELECT * FROM invitations WHERE invitations.event_ID=".mysqli_real_escape_string($link,$event_ID)." AND invitations.tag_ID=".mysqli_real_escape_string($link,$tag_ID)." AND status='1' AND account_ID!=".$_SESSION['accountID']." AND invitations.invitation_ID=(SELECT MAX(invitation_ID) FROM invitations WHERE invitations.tag_ID=".mysqli_real_escape_string($link,$tag_ID)." AND invitations.event_ID=".mysqli_real_escape_string($link,$event_ID).")";
			@mysqli_real_query($link,$query);
			$result = mysqli_store_result($link);
			if(mysqli_num_rows($result)==1)
			{
				$row=mysqli_fetch_assoc($result);
				if($row['scan']==1)
				{
					$display="profile";
					$account_ID=$row['account_ID'];
					$query = "INSERT INTO actions VALUES('',".$_SESSION['accountID'].",".mysqli_real_escape_string($link,$tag_ID).",'5','none','P',CURRENT_TIMESTAMP)";
					@mysqli_real_query($link,$query);
				}elseif($row['scan']==0)
				{
					$display="object";
				}
				
			}else
			{
				//$message="- This QR Code is currently not participating in the event.<br><br>Keep looking for other QR Codes to scan.";
				$message='- You can not access the information of this QR Code for either one of the following reasons:<br><br>a) This QR Code has not been activated yet. If this is your nametag/poster, please, <a href="session/login.php?logout=1"/>log out from your account</a> in order to active this QR Code, or contact grand-qr-support@ssrg.cs.ualberta.ca.<br><br>b) You can not decode your own QR Codes.<br><br>';
				$display="clarification";
			}
		}else
		{
			$message="- You do not have permissions to scan QR Codes. Please contact grand-qr-support@ssrg.cs.ualberta.ca.<br><br>";
			$display="clarification";
		}
	}
}else
{
	
	if(isset($_GET['tagid']) && isset($_GET['eventid']) && $_GET['tagid']!="" && $_GET['eventid']!="" && !isset($_GET['actionform']) && is_numeric($_GET['tagid']) && is_numeric($_GET['eventid']))
	{
		$tag_ID=decrypt($_GET['tagid']);
		$event_ID=decrypt($_GET['eventid']);
	
		if(!isset($_SESSION['accountID']))
		{
	
			/*$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);
			if(!$link)
			{	
				echo "Error connecting to MySQL";
				exit(0);
			}
			*/
			//mysqli_select_db($link,"grand");
			$query = "SELECT * FROM accounts, invitations, tags WHERE invitations.tag_ID=".mysqli_real_escape_string($link,$tag_ID)." AND invitations.event_ID=".mysqli_real_escape_string($link,$event_ID)." AND invitations.account_ID=accounts.account_ID AND tags.tag_ID=invitations.tag_ID AND invitations.invitation_ID=(SELECT MAX(invitation_ID) FROM invitations WHERE invitations.tag_ID=".mysqli_real_escape_string($link,$tag_ID)." AND invitations.event_ID=".$event_ID.")";
			@mysqli_real_query($link,$query);
			$result = mysqli_store_result($link);
			if(mysqli_num_rows($result)==1)
			{
				$row = mysqli_fetch_assoc($result);
				
				if($row['status']==0)
				{
					if($row['type']=="P")
					{
						//$message="<strong>Are you ".$row['fullName']."?</strong> Please consent the following terms to start participating in the social scanning experience.<br><br><strong>You are NOT ".$row['fullName']."?</strong> We suggest you leave this page immediately! Thank you";
						$message = "If you are <strong>".$row['fullName']."</strong>, please read the information below; if you agree to the terms of the consent form, please check the 'I agree' checkbox and provide the requested information to start participating in GRAND 2011 social-scanning activity.<br><br>If you are not <strong>".$row['fullName']."</strong>, please contact grand-qr-support@ssrg.cs.ualberta.ca.";
						$display="consentP";
						
					}elseif($row['type']=="O")
					{
						//$message="<strong>Is ".$row['fullName']." your poster?</strong> Please consent the following terms to start participating in the social scanning experience.<br><br><strong>Is ".$row['fullName']." NOT your poster?</strong> We suggest you leave this page immediately! Thank you";
						$message = "If <strong>".$row['fullName']."</strong> is your poster , please read the information below; if you agree to the terms of the consent form, please check the 'I agree' checkbox and provide the requested information to start participating in GRAND 2011 social-scanning activity.<br><br>If <strong>".$row['fullName']."</strong> is not your poster , please contact grand-qr-support@ssrg.cs.ualberta.ca.";
						$display="consentO";
					}
				
				}elseif($row['status']==1)
				{
					$query = "SELECT * FROM user WHERE userKey='".$row['userKey']."'";
					mysqli_select_db($link,"percom_profiles");
					@mysqli_real_query($link,$query);
					$result = mysqli_store_result($link);
					if(mysqli_num_rows($result)==1)
					{
						$row = mysqli_fetch_assoc($result);
						if($row['active']=="0")
						{
						
						$message="- This QR Code has not been activated yet. If you are the owner of this QR Code, a confirmation email message has been sent to your account. Please, follow the instructions in the message to activate your account or QR Code.<br><br";
						
							//$message="<strong>Are you ".$row['fullName']."?</strong><br><br>This account has not been activated yet.<br><br><strong>If you are ".$row['fullName']."</strong>, you should activate this account by logging in to your email account and clicking on the link included in the confirmation email that was sent to you when you consented to participate.<br><br><strong>If you are NOT ".$row['fullName']."</strong>, we suggest you leave this page immediately and scan your own nametag to participate in this social scanning experience.";
							$display="clarification";
						
						}elseif($row['active']=="1")
						{
							$display="login";
							$email=$row['userKey'];
						}
					}
				}
				
			}elseif(mysqli_num_rows($result)==0)
			{
				$query = "SELECT * FROM tags WHERE tag_ID=".mysqli_real_escape_string($link,$tag_ID)." AND event_ID=".mysqli_real_escape_string($link,$event_ID)."";
				@mysqli_real_query($link,$query);
				$result = mysqli_store_result($link);
				if(mysqli_num_rows($result)==1)
				{
					$row = mysqli_fetch_assoc($result);
					
					if($row['type']=="P")
					{
						//$message="<strong>Are you ".$row['fullName']."?</strong> Please consent the following terms to start participating in the social scanning experience with this QR Code.<br><br><strong>You are not ".$row['fullName']."?</strong> We suggest you leave this page immediately! Thank you";
						$message = "If you are <strong>".$row['fullName']."</strong>, please read the information below; if you agree to the terms of the consent form, please check the 'I agree' checkbox and provide the requested information to start participating in GRAND 2011 social-scanning activity.<br><br>If you are not <strong>".$row['fullName']."</strong>, please contact grand-qr-support@ssrg.cs.ualberta.ca.";
						$display="consentP";
						
					}elseif($row['type']=="O")
					{
						//$message="<strong>Is ".$row['fullName']." your poster?</strong> Please consent the following terms to start participating in the social scanning experience with this QR Code.<br><br><strong>Is ".$row['fullName']." NOT your poster?</strong> We suggest you leave this page immediately! Thank you";
						$message = "If <strong>".$row['fullName']."</strong> is your poster , please read the information below; if you agree to the terms of the consent form, please check the 'I agree' below and provide the requested information to start participating in GRAND 2011 social-scanning activity.<br><br>If <strong>".$row['fullName']."</strong> is not your poster , please contact grand-qr-support@ssrg.cs.ualberta.ca.";
						$display="consentO";
					}
					
				}elseif(mysqli_num_rows($result)==0)
				{
					$message="- This QR Code is not participating. Please contact grand-qr-support.";
					$display="clarification";
				}
			}
		}
	
	}elseif(isset($_POST['actionform']) && ($_POST['actionform']=="consentP" || $_POST['actionform']=="consentO") && isset($_POST['tagid']) && isset($_POST['eventid']) && trim($_POST['tagid'])!="" && trim($_POST['eventid'])!="" && is_numeric($_POST['tagid']) && is_numeric($_POST['eventid']) && !isset($_SESSION['entered']))
	{
		$tag_ID=decrypt($_POST['tagid']);
		$event_ID=decrypt($_POST['eventid']);
		$realEmail="";
		
		if(isset($_POST['accept']) && $_POST['accept']=="1")
		{
			$formhandle="0";
			$code_response=0;
			$scan=0;
			
			if($_POST['actionform']=="consentP")
			{
				$formhandle=$_SESSION['consentIDProfile'];
				$scan=1;
			
			}elseif($_POST['actionform']=="consentO")
			{
				$formhandle=$_SESSION['consentIDObject'];	
			}
			
			$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);
			if(!$link)
			{	
				echo "Error connecting to MySQL";
				exit(0);
			}
			
			$query = "SELECT * FROM tags WHERE tag_ID=".mysqli_real_escape_string($link,$tag_ID)." AND event_ID=".mysqli_real_escape_string($link,$event_ID)."";
			@mysqli_real_query($link,$query);
			$result = mysqli_store_result($link);
			if(mysqli_num_rows($result)==1)
			{
				$row = mysqli_fetch_assoc($result);
				if(trim($row['contactEmail'])=="")
				{
					$realEmail = trim($_POST['email']);
				}else
				{
					$realEmail = trim($row['contactEmail']);
				}
			}			
			
			mysqli_select_db($link,"percom_profiles");
			
			$code_response=enterUser(trim($_POST['fullName']),trim($_POST['email']),trim($realEmail),trim($_POST['password1']),trim($_POST['password2']),"4",$formhandle,"1",&$link,&$error_code);
			if($code_response>0)
			{
				$_SESSION['entered']=1;
				$message="- A confirmation email message has been sent to your account. It could take up to 5min for the email to be sent. Please, follow the instructions in the message to activate your account/QR Code.<br><br>Please, close this window once you have read this message. Thank you.<br><br>";
				$display="clarification";
				$account_ID=0;
				$json="null";
				
				mysqli_select_db($link,"grand");
				if($code_response==1)
				{
					$query = "SELECT * FROM accounts WHERE userKey='".mysqli_real_escape_string($link,trim($_POST['email']))."'";
					@mysqli_real_query($link,$query);
					$result = mysqli_store_result($link);
					if(mysqli_num_rows($result)==1)
					{
						$row = mysqli_fetch_assoc($result);
						$account_ID=$row['account_ID'];
					}
					
					$query = "INSERT INTO invitations VALUES('',".$account_ID.",".mysqli_real_escape_string($link,$event_ID).",".mysqli_real_escape_string($link,$tag_ID).",".$error_code.",'1','".$scan."')";
					@mysqli_real_query($link,$query);
					
					$query = "UPDATE tags SET contactEmail='".mysqli_real_escape_string($link,trim($_POST['email']))."' WHERE tags.tag_ID=".$tag_ID."";
					@mysqli_real_query($link,$query);
					
				}elseif($code_response==2)
				{
					$query = "INSERT INTO accounts VALUES('','".mysqli_real_escape_string($link,trim($_POST['email']))."')";
					@mysqli_real_query($link,$query);
					$account_ID=mysqli_insert_id($link);
					
					$query = "INSERT INTO invitations VALUES('',".$account_ID.",".mysqli_real_escape_string($link,$event_ID).",".mysqli_real_escape_string($link,$tag_ID).",".$error_code.",'1','".$scan."')";
					@mysqli_real_query($link,$query);
					
					$query = "UPDATE tags SET contactEmail='".mysqli_real_escape_string($link,trim($_POST['email']))."' WHERE tags.tag_ID=".mysqli_real_escape_string($link,$tag_ID)."";
					@mysqli_real_query($link,$query);
				}
				
				if($_POST['actionform']=="consentP")
				{
					$query = "SELECT * FROM tags WHERE tag_ID=".mysqli_real_escape_string($link,$tag_ID)." AND event_ID=".mysqli_real_escape_string($link,$event_ID)."";
					@mysqli_real_query($link,$query);
					$result = mysqli_store_result($link);
					if(mysqli_num_rows($result)==1)
					{
						$row = mysqli_fetch_assoc($result);
						$a = array();
	
						//Required
						$a['fullName'] = $row['fullName'];
						$a['email'] = $row['contactEmail'];
						$a['tagid'] = encrypt($tag_ID);
						
						//Optional
						$a['picture'] = $row['URLPicture'];
						$a['address'] = $row['address'];
						$a['affiliation'] = $row['affiliation'];
						$a['url'] = $row['homepage'];
						$a['phone'] = $row['phoneNo'];
						$a['title'] = $row['titlePerson'];
						$a['keywords'] = $row['keywords'];
						$a['public'] = $row['access'];
						$a['project'] = $row['project'];
						
						$json = addslashes(json_encode($a));
						$fileHandler = fopen("profiles/static/".$_SESSION['event']."/private".$account_ID, 'w');  //accountID FROM DATABASE
						fwrite($fileHandler, $json);
						fclose($fileHandler);
					}
					
				}elseif($_POST['actionform']=="consentO")
				{
					$query = "SELECT * FROM tags WHERE tag_ID=".mysqli_real_escape_string($link,$tag_ID)." AND event_ID=".mysqli_real_escape_string($link,$event_ID)."";
					@mysqli_real_query($link,$query);
					$result = mysqli_store_result($link);
					if(mysqli_num_rows($result)==1)
					{
						$row = mysqli_fetch_assoc($result);
						$a = array();
	
	
						//OBJECT DETAILS
		
						//Required
						$a = array();
	
						$a['picture'] = $row['URLPicture'];
						$a['file'] = $row['URLFile'];
						$a['title'] = $row['fullName'];
						$a['public'] = $row['access'];
						$a['description'] = $row['description'];
						$a['project'] = $row['project'];
						$a['keywords'] = $row['keywords'];
	
						$json = addslashes(json_encode($a));
						$fileHandler = fopen("objects/static/".$_SESSION['event']."/private".$tag_ID, 'w');
						fwrite($fileHandler, $json);
						fclose($fileHandler);
						
						
						//COMMENTS
						//$fileHandler = fopen("objects/static/".$_SESSION['event']."/comments".$tag_ID, 'w');
						//fwrite($fileHandler, "null");
						//fclose($fileHandler);
						
					}	
	
	
					//LIST OF OBJECTS
	
					$query = "SELECT * FROM invitations, tags WHERE invitations.account_ID=".mysqli_real_escape_string($link,$account_ID)." AND invitations.event_ID=".mysqli_real_escape_string($link,$event_ID)." AND invitations.tag_ID=tags.tag_ID AND tags.type='O' AND invitations.status='1'";
					@mysqli_real_query($link,$query);
					$result = mysqli_store_result($link);
					$i=0;
					$a = array();
					if(mysqli_num_rows($result)>0)
					{
						$row = mysqli_fetch_assoc($result);
						do{
							$a[$i]['title']=$row['fullName'];
							$a[$i]['id']=encrypt($row['tag_ID']);
							$i++;
						}while($row=mysqli_fetch_assoc($result));
						
						$json = addslashes(json_encode($a));
						$fileHandler = fopen("objects/static/".$_SESSION['event']."/list".$account_ID, 'w');
						fwrite($fileHandler, $json);
						fclose($fileHandler);
					}
				}
				
			}elseif($code_response<0)
			{
				if($code_response==-1)
				{
					$message = '<span style="color:#FFFF66; font-weight:bold;">'.$error_code.'</span>';
					//$error_code = $message;
					//$message="";
					$error_code="";
					$display = $_POST['actionform'];
					
				}elseif($code_response==-2)
				{
					$message= '<span style="color:#FFFF66; font-weight:bold;">- An unexpected error has occurred. We apologize for any inconvenience. We are currently working to fix this issue.</span><br><br>'.$error_code;
			
					$display="clarification";
				}
			}
					 
		}else
		{
				
			$message='<span style="color:#FFFF66; font-weight:bold;">'."- Please check the 'I agree' checkbox and provide the requested information to start participating in GRAND 2011 social-scanning activity</span>";
			//$error_code=$message;
			//$message="";
			$error_code="";
			$display=$_POST['actionform'];
		}
		
	}elseif(isset($_GET['unlock']) && $_GET['unlock']==1 && isset($_GET['accountID']) && $_GET['accountID']!="" && is_numeric($_GET['accountID']))
	{
	
		$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);
		if(!$link)
		{	
			echo "Error connecting to MySQL";
			exit(0);
		}
		
		mysqli_select_db($link,"percom_profiles");
		$query = "UPDATE user SET active='1' WHERE user_ID=".mysqli_real_escape_string($link,decrypt($_GET['accountID']))."";
		@mysqli_real_query($link,$query);
		//echo "Rows= ".mysqli_affected_rows($link);
		if(mysqli_affected_rows($link)==1)
		{
			
			$query = "SELECT * FROM user WHERE user_ID=".mysqli_real_escape_string($link,decrypt($_GET['accountID']))."";
			mysqli_select_db($link,"percom_profiles");
			@mysqli_real_query($link,$query);
			$result = mysqli_store_result($link);
			if(mysqli_num_rows($result)==1)
			{
				$row = mysqli_fetch_assoc($result);
				$email=$row['userKey'];
				$display="login";
			}
			
		}else
		{
			$query = "SELECT * FROM user WHERE user_ID=".mysqli_real_escape_string($link,decrypt($_GET['accountID']))."";
			mysqli_select_db($link,"percom_profiles");
			@mysqli_real_query($link,$query);
			$result = mysqli_store_result($link);
			if(mysqli_num_rows($result)==1)
			{
				$row = mysqli_fetch_assoc($result);
				$email=$row['userKey'];
				$display="login";
			}else
			{
				$display="login";
			}
		}
			
	}

}
?>
<!-- HERE I CAN INCLUDE ANY OTHER SPECIFIC LIBRARIES FOR THIS PAGE -->
<script type='text/javascript' src="<?php echo $rel_path_js; ?>flexcroll.js"></script>

<?php if($display=="profile"){ ?>
<script src="<?php echo $rel_path_js; ?>jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    var json = eval("(" + '<?php
        $fileName = "profiles/static/".$_SESSION['event']."/private".$account_ID;
        if(file_exists($fileName)){
            $fileHandler = fopen($fileName, 'r');
            $data = fread($fileHandler, filesize($fileName));
            if($data != "[]" && $data != "null"){
                echo $data;
            }
            else{
                echo "undefined";
            }
            fclose($fileHandler);
        }
        else{
            echo "undefined";
        }
    ?>' + ")");
    
    $(document).ready(function(){
        var html = "";
        if(json['picture'] != "" && json['picture'] != " "){
            html += "<img width='128' style='float:left; margin-right:5px;' src='" + json['picture'] + "' /><br />\n";
        }
        html += "<span class='title'>" + json['fullName'] + "</span><br />\n";
        var email = json['email'];
        if(json['email'].length > 30){
            email = json['email'].substring(0, 30) + "...";
        }
        html += "<div style='padding-left:5px; padding-top:15px; float:left;'><b>Email:</b><br /><a href='mailto:" + json['email'] + "'>" + email + "</a><br /><br />\n";
        if(json['title'] != ""){
            html += "<b>Title:</b><br />" + json['title'] + "<br /><br />\n";
        }
        if(json['project'] != ""){
            html += "<b>Projects:</b><br />" + json['project'] + "<br /><br />\n";
        }
        if(json['affiliation'] != ""){
            html += "<b>Affiliation:</b><br />" + json['affiliation'] + "<br /><br />\n";
        }
        if(json['address'] != ""){
            html += "<b>Address:</b><br />" + json['address'] + "<br /><br />\n";
        }
        if(json['url'] != ""){
            if(json['url'].indexOf("http") !== 0){
                json['url'] = "http://" + json['url'];
            }
            html += "<b>Home&nbsp;Page:</b><br /><a target='_blank' href='" + json['url'] + "'>Visit My Website</a><br /><br />\n";
        }
        if(json['phone'] != ""){
            html += "<b>Phone:</b><br />" + json['phone'] + "<br /><br />\n";
        }
        if(json['keywords'] != ""){
            html += "<b>Keywords:</b><br />" + json['keywords'] + "<br /><br />\n";
        }
        html += "</div>\n";
        $("#table").html(html);
    });
</script>

<?php }elseif($display=="object"){ ?>

<script src="<?php echo $rel_path_js; ?>jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    var json = eval("(" + '<?php
        $fileName = "objects/static/".$_SESSION['event']."/private".$tag_ID;
        if(file_exists($fileName)){
            $fileHandler = fopen($fileName, 'r');
            $data = fread($fileHandler, filesize($fileName));
            if($data != "[]" && $data != "null"){
                echo $data;
            }
            else{
                echo "undefined";
            }
            fclose($fileHandler);
        }
        else{
            echo "undefined";
        }
    ?>' + ")");
   
    $(document).ready(function(){
        var html = "";
        if(json['picture'] != "" && json['picture'] != " "){
            html += "<img width='128' style='float:left; margin-right:5px;' src='" + json['picture'] + "' /><br />\n";
        }
        html += "<span class='title'>" + json['title'] + "</span><br />\n";
        html += "<div style='padding-left: 5px; padding-top:15px; float:left;'>";
        if(json['project'] != ""){
            html += "<b>Projects:</b><br />" + json['project'] + "<br /><br />\n";
        }
        if(json['file'] != "" && json['file'] != " "){
            html += "<b>File:</b><br /><a href='" + json['file'] + "'>Download PDF</a><br /><br />\n";
        }
        if(json['description'] != ""){
            html += "<b>Description:</b><br />" + json['description'] + "<br /><br />\n";
        }
        if(json['keywords'] != ""){
            html += "<b>Keywords:</b><br />" + json['keywords'] + "<br /><br />\n";
        }
        html += "</div>\n";
        $("#table").html(html);
    });
</script>

<?php } ?>
</head>
<body>

<?php include($rel_path_php."logo.php"); ?>

<!-- SPLIT DIV -->
<div id="splitdiv">

<?php switch($display){


case "clarification": ?>
<!-- CONTENT-LEFT -->
<div id="leftcontent">

<br>

<!-- PARAGRAPH 1 -->
<span class="title">

IMPORTANT<br>

</span>

<br>
<span class="sub-title">
<?php echo $message; ?>
</span>
<hr class="page-splits">

<br>

</div>
<!-- CONTENT-LEFT -->




<!-- CONTENT-RIGHT -->
<div id="rightcontent">

<br>

</div>
<!-- CONTENT-RIGHT -->
<?php break;

case "login": ?>

<!-- CONTENT-LEFT -->
<div id="leftcontent">

<br>

<span class="sub-title">
<?php echo $message; ?>
</span>
<br>
<!-- PARAGRAPH 1 -->
<span class="title">

Log In<br>

</span>
<?php if($error_code!=""){ echo "<br><I>".$error_code."</I><br><br>"; } ?>

<form action="session/login.php" name="login" method="POST">
<br>Email:</I><br>
<input class="replaceInput" name="email" type="text" value="<?php if(isset($email) && $email!=""){ echo $email; } else{ echo ""; } ?>" id="email" size="100" maxlength="100" /><br />
Password:<br>
<input  class="replaceInput" name="password" type="password" value="" id="password" size="20" maxlength="20" /><br />
<input type="hidden" name="event" id="event" value="<?php echo $_SESSION['event']; ?>" />
<input style="font-size:18px;" name="logIn" type="submit" value="Log In" />
</form>

<br>

<hr class="page-splits"><br>

</div>
<!-- CONTENT-LEFT -->




<!-- CONTENT-RIGHT -->
<div id="rightcontent">

<br>

</div>
<!-- CONTENT-RIGHT -->


<?php break;

case "consentP": ?>

<!-- CONTENT-LEFT -->
<div id="leftcontent">



<br>

<?php echo $message; ?>

<br><br>

<div id="consent" class='flexcroll'>
<br />
We would like to include you in a research project conducted by M.Sc. student Lucio Gutierrez and Drs. Eleni Stroulia and Ioanis Nikolaidis. The objective of this study is to analyze social-networking activities during meetings of members of a community of practice. In order to participate, you will first have to provide some information about yourself. Then, during the event, you will be able to introduce yourself with this information to other participants who scan your nametag. You can also scan the QR codes affixed on the event exhibits to access further information about these exhibits, rate them and comment on them. <br /><br />

Throughout the event, running totals about the activities of the participants will be calculated and may be projected on screens around the event space. These aggregate statistics will include (but are not limited to):<br />
•	how many nametags have been scanned,<br />
•	who the most prolific commentator on exhibits is,<br />
•	who the most active person is (person who has scanned the most number of nametags and exhibits),<br />
•	who the most popular person is (person whose nametag has been scan the most), and<br />
•	what the social graph of the participants looks like (visualization of a network of people who have scanned each other’s nametags).<br /><br />

For each of your profile elements:<br />
•	picture (image)<br />
•	first name,<br />
•	last name,<br />
•	affiliation,<br />
•	email<br />
•	address,<br />
•	personal URL,<br />
•	phone number, and<br />
•	keywords of research areas of interest;<br />
you will be able to specify the degree to which you are willing to share it: namely you may choose to share it (a) with the people you connect with (scan your nametag) during the event, or (b) with all the event participants through information disseminated during the event (social graphs, virtual worlds, statistics). <br /><br />

During the event and time using the mobile web application, the system will record the communication and interactions among you and other players, including:<br />
a)	What you do in the real world, including scanning QR tags of other people and exhibits,<br />
b)	What you do in the web site, including checking your statistics about your virtual profile (how many have scanned your profile, for instance), and looking at other’s profile with whom you exchange virtual profiles,<br />
c)	What is your GPS location in the real world (when outdoors) and what virtual-world locations you visit, and<br />
d)	What messages you leave to any of the exhibits,<br />
e)	How you evaluate each of the exhibits,<br />
f)	How many exhibits you visit, and<br />
g)	Where the various QR codes are decoded (and possibly who decodes those QRs).<br /><br />

At the end of the event, you will be asked to fill out an online questionnaire about your experience with the system and your opinion of the usability and (social and fun) value of the mobile web application and virtual world(s).<br /><br />

Data collected through this activity can give us insights on many interesting research problems, including (a) patterns in how people interact and network in these events, (b) models of pedestrian mobility and (c) the longer-term impact of such events to the extent that you choose to use your QR tag after the event. The fact that we are asking you to fill out personal information about yourself in the mobile web application is merely for purposes of sharing info data with other participants and to make the experience more enjoyable to everyone involved. It is up to you to decide whether you want to share this data with others or not as stated above. Your personal information will be used only for statistic purposes, and will not be publicly available to anyone, unless you decide to do so through the mobile web application during the virtual profile exchange experience. These data will be kept for a minimum of 5 years following completion of the research study and will remain in secure storage inside the firewall of the Computing Science department. You are entitled to a copy of the final report of this study. Results from this research study may be used for research articles, presentations, and teaching purposes. For all uses, data will be fully anonymized and handled in compliance with the University Standards. Other research assistants may have access to the anonymized data for analysis purposes.<br /><br />

There are several very clear rights that you are entitled as a participant in any research conducted by a researcher from the University of Alberta. You have the right:<br />
•	To not participate.<br />
•	To withdraw at any time without prejudice to pre-existing entitlements, and to continuing and meaningful opportunities for deciding whether or not to continue to participate.<br />
•	To opt out without penalty and any collected data withdrawn from the database and not included in the study. 
•	To privacy, anonymity and confidentiality.<br />
•	To safeguards for security of data (data are to be kept for a minimum of 5 years following completion of research).<br />
•	To disclosure of the presence of any apparent or actual conflict of interest on the part of the researcher(s).<br />
•	To a copy of any final report that may be a result of the collected data.<br /><br />

The plan for this study has been reviewed for its adherence to ethical guidelines and approved by the Arts, Science & Law Research Ethics Board (ASL REB) at the University of Alberta. For questions regarding participant rights and ethical conduct of research, contact the Chair of the ASL REB c/o (780) 492-2614.<br /><br />

Feel free to contact Dr. Eleni Stroulia (stroulia@ualberta.ca) of the Department of Computing Science and M.Sc. student Lucio Gutierrez at 780-716-7592 or at (lucio@ualberta.ca) if you have any questions or comments.<br /><br />

By providing your full name, your email address and a password, and clicking on the box below labeled 'I agree to participate', you indicate that you have understood the information regarding participation in the research project and agree to participate.  In no way does this waive your legal rights nor release the investigators, sponsors, or involved institutions from their legal and professional responsibilities.  Your continued participation should be as informed as your initial consent, so you should feel free to ask for clarification or new information throughout your participation.<br /><br />
-----------------------------------<br />
</div> 

</div>
<!-- CONTENT-LEFT -->

<!-- CONTENT-RIGHT -->
<div id="rightcontent">

<br>

<!-- PARAGRAPH 1 -->
<span class="subtitle">
<?php 
if($error_code!="")
{
	echo $error_code."<br>";
}
?>
<br>
</span>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="login" method="POST"><br>
<input type="checkbox" id="accept" name="accept" value="1" <?php if(isset($_POST['accept'])){ echo "checked=checked"; }?>>'I Agree'<br><br>
Full name:
<input class="replaceInput" name="fullName" type="text" value="<?php if(isset($_POST['fullName'])){ echo $_POST['fullName']; } else{ echo ""; } ?>" id="fullName" size="20" maxlength="100" /><br />
Email(must be the same as the one you used to register for GRAND 2011):<br>
<input  class="replaceInput" name="email" type="text" value="<?php if(isset($_POST['email'])){ echo $_POST['email']; } else{ echo ""; } ?>" id="email" size="20" maxlength="100" /><br />
Password:<br>
<input type="password" class="replaceInput" name="password1" type="password" value="" id="password1" size="20" maxlength="20" /><br \>
Repeat Password:
<input  class="replaceInput" name="password2" type="password" value="" id="password2" size="20" maxlength="20" />
<input type="hidden" name="eventid" id="eventid" value="<?php echo encrypt($event_ID); ?>" />
<input type="hidden" name="tagid" id="tagid" value="<?php echo encrypt($tag_ID); ?>" />
<input type="hidden" name="actionform" id="actionform" value="consentP" />
<input style="font-size:18px;" name="logIn" type="submit" value="Sign Up" />
</form>
<?php if(isset($_SESSION['entered']))
	  {
		unset($_SESSION['entered']);
	  }
?>
</div>
<!-- CONTENT-RIGHT -->
<?php break; 

case "consentO":
?>
<!-- CONTENT-LEFT -->
<div id="leftcontent">



<br>

<?php echo $message; ?>

<br><br>

<div id="consent" class='flexcroll'>
<br />
We would like to include you in a research project conducted by M.Sc. student Lucio Gutierrez and Drs. Eleni Stroulia and Ioanis Nikolaidis. The objective of this study is to analyze social-networking activities during meetings of members of a community of practice. In order to participate, you will have to provide our application with some information about your exhibit and attach a QR code your exhibit. Then, during the event, participants will be able to scan the QR code to rate and comment on your exhibit.<br /><br />

Throughout the event, running totals about the exhibits will be calculated and may be projected on screens around the event space. These aggregate statistics will include (but are not limited to):<br />
•	how many exhibits have been visited,<br />
•	what the most controversial exhibit is (number of likes and dislikes are similar),<br />
•	what the most visited exhibit is, <br />
•	what the favorite exhibit is, and<br />
•	what the social graph of a particular exhibit looks like.<br /><br />

The information that you will provide about your exhibit includes:<br />
•	an image of the exhibit<br />
•	a file about the exhibit that can be downloaded by interested parties,<br />
•	title,<br />
•	description, and<br />
•	keywords related to the exhibit.<br />
For each of these pieces of information, you will be able to specify the degree to which you are willing to share it: namely you may choose to share it (a) with the people who scan the QR code affixed to the exhibit during the event, or (b) with all the event participants through information disseminated during the event (social graphs, virtual worlds, statistics). <br /><br />

During the event and time using the mobile web application, the system will record the communication and interactions among the exhibit and other players, including:<br />
a)	Who visited the exhibit,<br />
b)	What comments are left to the exhibit,<br />
c)	Rate evaluations of the exhibit, and<br />
d)	Total number of visits to the exhibit.<br /><br />

At the end of the event, you will be asked to fill out an online questionnaire about your experience with the system and your opinion of the usability and (social and fun) value of the mobile web application.<br />

Data collected through this activity can give us insights on many interesting research problems, including (a) patterns in how people interact and network in these events, (b) models of pedestrian mobility and (c) the longer-term impact of such events to the extent that you adopt the application to generate QR codes that can be affixed to other exhibits in the future. These data will be kept for a minimum of 5 years following completion of the research study and will remain in secure storage inside the firewall of the Computing Science department. You are entitled to a copy of the final report of this study. Results from this research study may be used for research articles, presentations, and teaching purposes. For all uses, data will be fully anonymized and handled in compliance with the University Standards. Other research assistants may have access to the anonymized data for analysis purposes.<br /><br />

There are several very clear rights that you are entitled as a participant in any research conducted by a researcher from the University of Alberta. You have the right:<br />
•	To not participate.<br />
•	To withdraw at any time without prejudice to pre-existing entitlements, and to continuing and meaningful opportunities for deciding whether or not to continue to participate.<br />
•	To opt out without penalty and any collected data withdrawn from the database and not included in the study. <br />
•	To privacy, anonymity and confidentiality.<br />
•	To safeguards for security of data (data are to be kept for a minimum of 5 years following completion of research).<br />
•	To disclosure of the presence of any apparent or actual conflict of interest on the part of the researcher(s).<br />
•	To a copy of any final report that may be a result of the collected data.<br /><br />

The plan for this study has been reviewed for its adherence to ethical guidelines and approved by the Arts, Science & Law Research Ethics Board (ASL REB) at the University of Alberta. For questions regarding participant rights and ethical conduct of research, contact the Chair of the ASL REB c/o (780) 492-2614.<br /><br />

Feel free to contact Dr. Eleni Stroulia (stroulia@ualberta.ca) of the Department of Computing Science and M.Sc. student Lucio Gutierrez at 780-716-7592 or at (lucio@ualberta.ca) if you have any questions or comments.<br /><br />

By providing your full name, your email address and a password, and clicking on the box below labeled 'I agree to participate', you indicate that you have understood the information regarding participation in the research project and agree to participate with your exhibit.  In no way does this waive your legal rights nor release the investigators, sponsors, or involved institutions from their legal and professional responsibilities.  Your continued participation should be as informed as your initial consent, so you should feel free to ask for clarification or new information throughout your participatio.<br /><br />
-----------------------------------<br />
</div> 

</div>
<!-- CONTENT-LEFT -->




<!-- CONTENT-RIGHT -->
<div id="rightcontent">



<br>



<!-- PARAGRAPH 1 -->
<span class="subtitle">
<?php 
if($error_code!="")
{
	echo $error_code."<br>";
}
?>
<br>
</span>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="login" method="POST"><br>
<input type="checkbox" id="accept" name="accept" value="1" <?php if(isset($_POST['accept'])){ echo "checked=checked"; }?>>'I Agree'<br><br>
Full name:
<input class="replaceInput" name="fullName" type="text" value="<?php if(isset($_POST['fullName'])){ echo $_POST['fullName']; } else{ echo ""; } ?>" id="fullName" size="20" maxlength="100" /><br />
Email(must be the same as the one you used to register for GRAND 2011):<br>
<input  class="replaceInput" name="email" type="text" value="<?php if(isset($_POST['email'])){ echo $_POST['email']; } else{ echo ""; } ?>" id="email" size="20" maxlength="100" /><br />
Password:<br>
<input type="password" class="replaceInput" name="password1" type="password" value="" id="password1" size="20" maxlength="20" /><br \>
Repeat Password:
<input  class="replaceInput" name="password2" type="password" value="" id="password2" size="20" maxlength="20" />
<input type="hidden" name="eventid" id="eventid" value="<?php echo encrypt($event_ID); ?>" />
<input type="hidden" name="tagid" id="tagid" value="<?php echo encrypt($tag_ID); ?>" />
<input type="hidden" name="actionform" id="actionform" value="consentO" />
<input style="font-size:18px;" name="logIn" type="submit" value="Sign Up" />
</form>
<?php if(isset($_SESSION['entered']))
	  {
		unset($_SESSION['entered']);
	  }
?>
</div>
<!-- CONTENT-RIGHT -->

<?php break;

case "profile": ?>

<!-- CONTENT-LEFT -->
<div id="leftcontent">
    <br />
    <div id="table">
        
    </div>
</div>
<!-- CONTENT-LEFT -->


<!-- CONTENT-RIGHT -->
<div id="rightcontent">
<hr />
    <br />
    <form action="index.php" name="login" method="POST">
	<input type="hidden" name="actionform" id="actionform" value="profile" />
	<center><input type="submit" name="formbutton" value="Done" id="formbutton" style="font-size:18px;" /></center>
	</form>
</div>
<!-- CONTENT-RIGHT -->

<?php break; ?>

<?php 

case "object":

?>

<!-- CONTENT-LEFT -->
<div id="leftcontent">
    <br />
    <div id="table">
        
    </div>
</div>
<!-- CONTENT-LEFT -->

<!-- CONTENT-RIGHT -->
<div id="rightcontent">
<hr />
    <br />
    <span class="title"><center>Please, rate this poster!</center></span><br />
    <form action="index.php" name="login" method="POST">
	<center><input style='width:25px; height:25px;' type="radio" value="5" name="rate" checked="checked"/><img src='_src/_img/thumb-up.png' /><br><br>
	<input style='width:25px; height:25px;' type="radio" value="1" name="rate"/><img src='_src/_img/thumb-down.png' /><br><br></center>
	Leave a comment(not required):<br>
	<textarea rows="5" cols="30" name="comments" id="comments" style="float:left; font-size:18px; width:100%"></textarea>
	<input type="hidden" name="eventid" id="eventid" value="<?php echo encrypt($event_ID); ?>" />
	<input type="hidden" name="tagid" id="tagid" value="<?php echo encrypt($tag_ID); ?>" />
	<input type="hidden" name="actionform" id="actionform" value="object" />
	<br><br>
	<center><input type="submit" name="formbutton" value="Send" id="formbutton" style="font-size:18px;"/></center>
</div>
<!-- CONTENT-RIGHT -->


<?php 

break;

} ?>
</div>
<!-- SPLIT DIV -->


<br>


<?php include($rel_path_php."footer.php"); ?>


</body>
</html>
