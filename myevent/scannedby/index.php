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
$_SESSION['menu']="profiles";
$_SESSION['submenu']="scannedby";

//This has to be in all the pages
$rel_path_img="../../_src/_img/";
$rel_path_js="../../_src/_js/";
$rel_path_css="../../_src/_css/";
$rel_path_php="../../_src/_php/";
$rel_path_menulinks="../../";
/////////////////////////////////
?>

<?php include($rel_path_php."header.php"); ?>
<!-- HERE I CAN INCLUDE ANY OTHER SPECIFIC LIBRARIES FOR THIS PAGE -->
<script src="<?php echo $rel_path_js; ?>jquery.js" type="text/javascript"></script>
<script src="<?php echo $rel_path_js; ?>tables/sorttable.js" type="text/javascript"></script>
<script src="<?php echo $rel_path_js; ?>tables/table.js" type="text/javascript"></script>
<script type="text/javascript">
    var json = eval("(" + '<?php
        $fileName = "./static/".$_SESSION['event']."/json".$_SESSION['accountID'];
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
    if(typeof json != 'undefined'){
        var table = new Table("t", Array({"title":"Name","type":NORMAL}));
        for(var i = 0; json[i] != undefined; i++){
            table.addRow(Array("<a onselect='return false' onmousedown='return false'  class='cellLink' href='person.php?account_id="+ json[i]['userID'] +"'>" + json[i]['nametitle'] + "</a>"));
        }
    } 
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
	<br>
<hr class="page-splits"><br>

</div>
<!-- CONTENT-LEFT -->




<!-- CONTENT-RIGHT -->
<div id="rightcontent">
<br />
<span class='title'>Scanned Me</span><br />
<br />
This is the list of participants who have scanned you. This section is updated every 5 minutes.  You can click on the username to bring you to their profile.
<br><br>
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
