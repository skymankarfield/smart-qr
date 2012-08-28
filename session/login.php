<?php 
session_start();

//These are variables for the navigation flow, NOT for the functionality of the system
$_SESSION['menu']="session";
$_SESSION['submenu']="";

//This has to be in all the pages
$rel_path_img="../_src/_img/";
$rel_path_js="../_src/_js/";
$rel_path_css="../_src/_css/";
$rel_path_php="../_src/_php/";
$rel_path_menulinks="../";
/////////////////////////////////

include($rel_path_php."header.php");
include($rel_path_php."_session/checkUser.php");
include($rel_path_php."mysqlConnection.php");


//WE NEED THE FOLLOWING VARIABLES FOR THE SYSTEM TO WORK PROPERLY. THESE VALUES HAVE TO COME FROM THE LOGIN PAGE
/*$_SESSION['eventID']="";
$_SESSION['eventName']="2011 GRAND Conference";
$_SESSION['eventKey']="GRAND";
$_SESSION['accountID']="";
$_SESSION['userKey']="";
*/

$error_code="";
$link=false;

if(isset($_GET['logout']) && $_GET['logout']==1 && isset($_SESSION['accountID']))
{
	unset($_SESSION['eventID']);
	unset($_SESSION['accountID']);
	unset($_SESSION['userKey']);
	unset($_SESSION['scan']);
	unset($_SESSION['fullName']);
	unset($_SESSION['profile']);
	unset($_SESSION['objects']);
}

if(isset($_POST['email']) && isset($_POST['password']))
{

	
	$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);
	if(!$link)
	{	
		echo "Error connecting to MySQL";
		exit(0);
	}
	
	$userKey="";
	mysqli_select_db($link,"percom_profiles");
	if($userKey=checkUser(trim($_POST['email']),trim($_POST['password']),&$link,&$error_code))
	{
		mysqli_select_db($link,"grand");
		$query = "SELECT * FROM accounts, invitations, events WHERE accounts.userKey='".$userKey."' AND invitations.account_ID=accounts.account_ID AND invitations.event_ID=events.event_ID AND invitations.status='1' AND events.eventKey='".$_SESSION['event']."'";
		@mysqli_real_query($link,$query);
		$result = mysqli_store_result($link);
		if(mysqli_num_rows($result)>=1)
		{
			$row = mysqli_fetch_assoc($result);
			$_SESSION['accountID'] = $row['account_ID'];
			$_SESSION['userKey']=$userKey;
			$_SESSION['fullName']=$error_code;
			$_SESSION['eventID'] = $row['event_ID'];
			$_SESSION['profile'] = 0;
			$_SESSION['objects'] = 0;
			$_SESSION['scan'] = 0;
			do{
				if($row['scan']==1)
				{
					$_SESSION['profile'] = 1;
					$_SESSION['scan'] = 1;
				}
				
				if($row['scan']==0)
				{
					$_SESSION['objects'] = 1;
				}
				
			}while($row = mysqli_fetch_assoc($result));
			
			header("Location:../index.php");
			mysqli_close($link);
			exit(0);
		}else
		{
			$error_code= "- This account does not exist in the system.";
		}
	}
	mysqli_close($link);
}

if(isset($_SESSION['accountID']))
{
	header("Location:../index.php");
	exit(0);
}

?>

<!-- HERE I CAN INCLUDE ANY OTHER SPECIFIC LIBRARIES FOR THIS PAGE -->
</head>
<body>

<?php include($rel_path_php."logo.php"); ?>

<!-- SPLIT DIV -->
<div id="splitdiv">

<?php include($rel_path_php."menu.php"); ?>

<!-- CONTENT-LEFT -->
<div id="leftcontent">



<br>



<!-- PARAGRAPH 1 -->
<span class="title">

Log In<br>

</span>
<?php if($error_code!=""){ echo "<br><I>".$error_code."</I><br><br>"; } ?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="login" method="POST">
<br>Email:</I><br>
<input class="replaceInput" name="email" type="text" value="<?php if(isset($_POST['email'])){ echo $_POST['email']; } else{ echo ""; } ?>" id="email" size="20" maxlength="100" /><br />
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


</div>
<!-- SPLIT DIV -->


<br>


<?php include($rel_path_php."footer.php"); ?>


</body>
</html>