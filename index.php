<?php 
session_start();

//WE NEED THE FOLLOWING VARIABLES FOR THE SYSTEM TO WORK PROPERLY. THESE VALUES HAVE TO COME FROM THE LOGIN PAGE
/*$_SESSION['eventID']="";
$_SESSION['eventName']="2011 GRAND Conference";
$_SESSION['eventKey']="GRAND";
$_SESSION['accountID']="";
$_SESSION['userKey']="";
*/

//These are variables for the navigation flow, NOT for the functionality of the system
$_SESSION['menu']="home";
$_SESSION['submenu']="";

//This has to be in all the pages
$rel_path_img="_src/_img/";
$rel_path_js="_src/_js/";
$rel_path_css="_src/_css/";
$rel_path_php="_src/_php/";
$rel_path_menulinks="";
/////////////////////////////////
?>

<?php include($rel_path_php."header.php"); 
		include($rel_path_php."_misc/decrypt.php");
		include($rel_path_php."mysqlConnection.php");
		$display="";
?>

<?php if(isset($_POST['actionform']) && $_POST['actionform']!="" && $_POST['actionform']=="object" && isset($_SESSION['accountID']))
{
	$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);
	if(!$link)
	{	
		echo "Error connecting to MySQL";
		exit(0);
	}
	$query = "INSERT INTO actions VALUES('',".$_SESSION['accountID'].",".decrypt($_POST['tagid']).",'".mysqli_real_escape_string($link,$_POST['rate'])."','".mysqli_real_escape_string($link,$_POST['comments'])."','O',CURRENT_TIMESTAMP)";
	@mysqli_real_query($link,$query);	
	$display="thank you for participating. Continue scanning QR Codes!";
}elseif(isset($_POST['actionform']) && $_POST['actionform']!="" && $_POST['actionform']=="profile" && isset($_SESSION['accountID']))
{
	$display="thank you for participating. Continue scanning QR Codes!";
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

<?php 
if(isset($_SESSION['fullName']))
{
	if(isset($display) && $display!="")
	{
		echo '<span class="title">Hello </span>'.$_SESSION['fullName'].'!, '.$display."<br><br>";
	}else
	{
		echo '<span class="title">Hello </span>'.$_SESSION['fullName'].'!<br><br>';
	}
}
?>

</span>

<?php if($display==""){ ?>
<span class="title">Getting Started</span><br />
<br>
To get started during the conference, (1) scan your QR tag, (2) consent to participate and (3) activate your account through your email. Steps (2) and (3) only apply when you haven't consented to participate previous to the conference via email. After you have consented to participate, you have to scan your QR tag in your nametag to log into the system, edit your profile and, if you have exhibits at the event, scan the QR tag on each of them to associate them with your account. <strong>In order to associate them with your account, you have to enter the email address of the account where you received your first confirmation email.</strong> Then you can start scanning!
<br />
<br />
<span class="title">What are QR tags? How do they work?</span><br><br>
'QR' stands for 'quick response'. QR tags or codes are black-and-white pixelated images that are being increasingly deployed today to enable smartphone owners to flexibly access information and services within the context of their everyday activities. During the GRAND conference, QR tags are affixed (a) to nametags and (b) to posters and attendees can scan them to access the  information about the profiles of other attendees and to comment and rate  the posters.<br><br>
There are free QR-code reader apps for iPhones (NeoReader, Scan, 2DCodeMe, ScanLife) , Androids (Barcode Scanner, ScanLife, QuickMark) and recent Blackberries (ScanLife, Barcode Assistant, QR Code Scanner Pro). Download one of them from the marketplace and start scanning!
<br />
<br />
<hr class="page-splits"><br>
</div>
<!-- CONTENT-LEFT -->

<!-- CONTENT-RIGHT -->
<div id="rightcontent"><br>
<span class="title">About Smart-QR</span><br />
<br />
The purpose of the Smart-QR system is to support and analyze networking activities among professionals participating in meetings. By augmenting exhibits and participants' name-tags with QR tags, participants can exchange useful information during the meeting, in the context of their natural networking interactions. As researchers, we are interested in analyzing these exchanges to recognize interesting patterns and trends.
<br>
<br>
<hr class="page-splits"><br>
This website is mobile. Try it in your mobile device.
<?php } ?>
<br>
<br>
<a href="http://ssrg.cs.ualberta.ca"> http://ssrg.cs.ualberta.ca </a> - 2011
<br>
<br>
</div>
<!-- CONTENT-RIGHT -->


</div>
<!-- SPLIT DIV -->

<?php include($rel_path_php."footer.php"); ?>


</body>
</html>
