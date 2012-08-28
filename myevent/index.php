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
$_SESSION['menu']="myevent";
$_SESSION['submenu']="";

//This has to be in all the pages
$rel_path_img="../_src/_img/";
$rel_path_js="../_src/_js/";
$rel_path_css="../_src/_css/";
$rel_path_php="../_src/_php/";
$rel_path_menulinks="../";
/////////////////////////////////
?>

<?php include($rel_path_php."header.php"); ?>
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
<span>
Click on "Participants" to see the list of people whose nametags you scanned.<br><br>
Click on "Exhibits" to see the list of exhibits you scanned.
<br>

</span>
<br>

<hr class="page-splits">
</div>
<!-- CONTENT-LEFT -->




<!-- CONTENT-RIGHT -->
<div id="rightcontent">
<br />
    <span class='title'>My Scans</span><br />
    <br />
These lists are updated every 5min. Check this section frequently to see how "social" you are during the conference.
<br>
<br>
<hr class="page-splits">
<br>
<a href="http://ssrg.cs.ualberta.ca"> http://ssrg.cs.ualberta.ca </a> - 2011

</div>
<!-- CONTENT-RIGHT -->


</div>
<!-- SPLIT DIV -->


<br>


<?php include($rel_path_php."footer.php"); ?>


</body>
</html>
