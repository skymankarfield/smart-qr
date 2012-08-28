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
$_SESSION['menu']="support";
$_SESSION['submenu']="";

//This has to be in all the pages
$rel_path_img="_src/_img/";
$rel_path_js="_src/_js/";
$rel_path_css="_src/_css/";
$rel_path_php="_src/_php/";
$rel_path_menulinks="";
/////////////////////////////////
include($rel_path_php."header.php"); 
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
<span class="title">Support</span><br />
<br />
If you are having problems with the system, contact us at <a href='mailto:grand-qr-support@ssrg.cs.ualberta.ca'>grand-qr-support</a>.
<br /><br />
This website is mobile. Try it in your mobile device.
<br>
<br>
<hr class="page-splits"><br>
<a href="http://ssrg.cs.ualberta.ca"> http://ssrg.cs.ualberta.ca </a> - 2011

</div>
<!-- CONTENT-RIGHT -->


</div>
<!-- CONTENT-LEFT -->




<!-- CONTENT-RIGHT -->
<div id="rightcontent">


</div>
<!-- CONTENT-RIGHT -->


</div>
<!-- SPLIT DIV -->


<br>


<?php include($rel_path_php."footer.php"); ?>


</body>
</html>
