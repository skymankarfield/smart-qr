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
$_SESSION['menu']="global";

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
<script src="<?php echo $rel_path_js; ?>jquery.js" type="text/javascript"></script>
<script src="<?php echo $rel_path_js; ?>tables/sorttable.js" type="text/javascript"></script>
<script src="<?php echo $rel_path_js; ?>tables/table.js" type="text/javascript"></script>
<script type="text/javascript">
    var table = new Table("t", Array({"title":"Statistic","type":NORMAL}));
    table.addRow(Array("<a onselect='return false' onmousedown='return false' class='cellLink' href='visualize.php?page=generalPTable'>Top People</a>"));
    table.addRow(Array("<a onselect='return false' onmousedown='return false' class='cellLink' href='visualize.php?page=popularPeople'>Most Popular People</a>"));
    table.addRow(Array("<a onselect='return false' onmousedown='return false' class='cellLink' href='visualize.php?page=popularPosters'>Most Popular Posters</a>"));
    table.addRow(Array("<a onselect='return false' onmousedown='return false' class='cellLink' href='visualize.php?page=likedPosters'>Most Liked Posters</a>"));
    table.addRow(Array("<a onselect='return false' onmousedown='return false' class='cellLink' href='visualize.php?page=controversialPosters'>Most Controversial Posters</a>"));
</script>
</head>
<body>

<?php include($rel_path_php."logo.php"); ?>

<!-- SPLIT DIV -->
<div id="splitdiv">

<?php include($rel_path_php."menu.php"); ?>

<!-- CONTENT-LEFT -->
<div id="leftcontent">
    <br />
    <div id="table">
        
    </div>
</div>
<!-- CONTENT-LEFT -->




<!-- CONTENT-RIGHT -->
<div id="rightcontent">

DESCRIPTION HERE

</div>
<!-- CONTENT-RIGHT -->


</div>
<!-- SPLIT DIV -->


<br>


<?php include($rel_path_php."footer.php"); ?>


</body>
</html>
