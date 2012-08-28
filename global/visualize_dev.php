<?php

//PLEASE ADD THE GLOBAL GRAPHS AND TABLES HERE. USE A SWITCH CASE TO VISUZALIZE DIFFERENT ONES AT A TIME DEPENDING ON 
//A GET PARAMETER COMING FROM THE GENERAL TABLE IN INDEX.PHP IN THIS SAME FOLDER
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

<?php include("/var/www/vhosts/smart-qr.com/subdomains/grand/html/_src/_php/header.php");
header("Cache-Control: no-cache must-revalidate");
?>
<?php include("/var/www/vhosts/smart-qr.com/subdomains/grand/html/_src/_php/mysqlConnection.php");
    include("/var/www/vhosts/smart-qr.com/subdomains/grand/html/_src/_php/_misc/encrypt.php");
    $link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);
?>
<link href="<?php echo $rel_path_css; ?>main.css" rel="stylesheet" type="text/css" />
<!--[if lt IE 9]>
<style>
    #canvases {
      position:relative;
      top:-194px;
      right:0;
      bottom:0;
    }
    
    #graph {
      top:194px;
      bottom:0;
    }
    
    #splitdiv {
        height:100%;
    }
    
    #leftcontent {
        top:0;
        width:70%;
        height:100%;
        padding:0;
    }

    #rightcontent {
        width:30%;
        height:100%;
        top:194px;
        padding:0;
        padding-bottom:20px;
    }
</style>
<![endif]-->
<?php
    switch($_GET['page']){
        case "graph":
            $choices = array("popularPeople", "popularPosters", "likedPosters", "controversialPosters");
            $_GET['page'] = $choices[array_rand($choices)];
            /*
            switch ($_GET['page']){
                case "popularPeople" :
                    getPopularPeople();
                    break;
                case "popularPosters" :
                    getPopularPosters();
                    break;
                case "likedPosters" :
                    getLikedPosters();
                    break;
                case "controversialPosters" :
                    getControversialPosters();
                    break;
            }
            */
            break;
        case "table":
            $choices = array("generalPTable", "generalOTable");
            $_GET['page'] = $choices[array_rand($choices)];
            /*
            switch ($_GET['page']){
                case "generalPTable" :
                    getGeneralPTable();
                    break;
                case "generalOTable" :
                    getGeneralOTable();
                    break;
            }
            */
            break;
        /*
        case "popularPeople" :
            getPopularPeople();
            break;
        case "popularPosters" :
            getPopularPosters();
            break;
        case "likedPosters" :
            getLikedPosters();
            break;
        case "controversialPosters" :
            getControversialPosters();
            break;
        case "generalPTable" :
            getGeneralPTable();
            break;
        case "generalOTable" :
            getGeneralOTable();
            break;
        */
    }
?>

<!-- HERE I CAN INCLUDE ANY OTHER SPECIFIC LIBRARIES FOR THIS PAGE -->
<script src="<?php echo $rel_path_js; ?>jquery.js" type="text/javascript"></script>
<script src="<?php echo $rel_path_js; ?>tables/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?php echo $rel_path_js; ?>tables/excanvas.js" type="text/javascript"></script>
<script src="<?php echo $rel_path_js; ?>tables/jsPlumb-1.2.5-all.js" type="text/javascript"></script>
<script src="<?php echo $rel_path_js; ?>tables/sorttable.js" type="text/javascript"></script>
<script src="<?php echo $rel_path_js; ?>tables/table.js" type="text/javascript"></script>
<script src="<?php echo $rel_path_js; ?>tables/main.js" type="text/javascript"></script>
<script type="text/javascript">
    var json = eval("(" + '<?php
        $title = "";
        $fileName = "";
        $nScans = "";
        switch($_GET['page']){
            case "popularPeople":
                $fileName = "./static/".$_SESSION['event']."/popularPeople";
                $title = "Most&nbsp;Popular&nbsp;People";
                $nScans = "Number&nbsp;of&nbsp;Scans";
                break;
            case "popularPosters":
                $fileName = "./static/".$_SESSION['event']."/popularPosters";
                $title = "Most&nbsp;Popular&nbsp;Posters";
                $nScans = "Number&nbsp;of&nbsp;Scans";
                break;
            case "likedPosters":
                $fileName = "./static/".$_SESSION['event']."/likedPosters";
                $title = "Most&nbsp;Liked&nbsp;Posters";
                $nScans = "Number&nbsp;of&nbsp;Likes";
                break;
            case "controversialPosters":
                $fileName = "./static/".$_SESSION['event']."/controversialPosters";
                $title = "Most&nbsp;Controversial&nbsp;Posters";
                $nScans = "Likes&nbsp;-&nbsp;Dislikes";
                break;
            case "generalPTable":
                $fileName = "./static/".$_SESSION['event']."/generalPTable";
                $title = "Top&nbsp;10&nbsp;People";
                break;
            case "generalOTable":
                $fileName = "./static/".$_SESSION['event']."/generalOTable";
                $title = "Top&nbsp;10&nbsp;Posters";
                break;
        }
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
    <?php if($_GET['page'] == "generalPTable"){ ?>
        $(document).ready(function(){
            $("#leftcontent").css("width", 0);
            $("#rightcontent").css("width", "100%");
            $("#tableTitle").html("<?php echo $title; ?>");
        });
        if(typeof json != 'undefined'){
            var table = new Table("t", Array({"title":"Rank","type":SMALL},
                                             {"title":"Name","type":NORMAL},
                                             {"title":"Number&nbsp;of&nbsp;Scans","type":SMALL},
                                             {"title":"Number&nbsp;of&nbsp;People&nbsp;I've&nbsp;Scanned","type":SMALL},
                                             {"title":"Number&nbsp;of&nbsp;my&nbsp;Posters&nbsp;Scanned","type":SMALL},
                                             {"title":"Number&nbsp;of&nbsp;Posters&nbsp;I've&nbsp;Scanned","type":SMALL}));
            for(var i = 0; json[i] != undefined && i < 10; i++){
                table.addRow(Array(i+1,
                                   "<span class='cellLink'>" + json[i]['nametitle'] + "</span>",
                                   "<span class='cellLink'>" + json[i]['nScans'] + "</span>",
                                   "<span class='cellLink'>" + json[i]['nOtherScans'] + "</span>",
                                   "<span class='cellLink'>" + json[i]['nScans1'] + "</span>",
                                   "<span class='cellLink'>" + json[i]['nOtherScans1'] + "</span>"));
            }
        }
    <?php } else if($_GET['page'] == "generalOTable"){ ?>
        $(document).ready(function(){
            $("#leftcontent").css("width", 0);
            $("#rightcontent").css("width", "100%");
            $("#tableTitle").html("<?php echo $title; ?>");
        });
        
        if(typeof json != 'undefined'){
            var table = new Table("t", Array({"title":"Rank","type":SMALL},
                                             {"title":"Title","type":NORMAL},
                                             {"title":"Number&nbsp;of&nbsp;Scans","type":SMALL},
                                             {"title":"Number&nbsp;of&nbsp;Comments","type":SMALL}));
            for(var i = 0; json[i] != undefined && i < 10; i++){
                table.addRow(Array(i+1,
                                   "<span class='cellLink'>" + json[i]['nametitle'] + "</span>",
                                   "<span class='cellLink'>" + json[i]['nScans'] + "</span>",
                                   "<span class='cellLink'>" + json[i]['nComments'] + "</span>"));
            }
            
        }
    <?php } else { ?>
        if(typeof json != 'undefined'){
            $(document).ready(function(){
                var table = new Table("t", Array({"title":"Rank","type":SMALL},
                                             {"title":"<?php echo $title; ?>","type":NORMAL},
                                             {"title":"<?php echo $nScans; ?>","type":SMALL}));
                for(var i = 0; json[i] != undefined && i < 3; i++){
                    table.addRow(Array(i+1, 
                                       "<span class='cellLink'>" + json[i]['nametitle'] + "</span>",
                                       "<span class='cellLink'>" + json[i]['nScans'] + "</span>"));
                    addNewNode(json[i]['id'], null, null);
                }
                for(var i = 0; i < tables.length; i++){
                    tables[i].printTableIn("table");
                }
                if(i == 0){
                    $("#table").html("There have been no scans");
                }
            });
        }
    <?php } ?>
</script>
</head>
<body onunload="jsPlumb.unload();">

<!-- SPLIT DIV -->
<div id="splitdiv">

<!-- CONTENT-LEFT -->
<div id="leftcontent">
    <div id="graph" class='redraw'>
        <div id="canvases" class="redraw"></div>
    </div>
</div>
<!-- CONTENT-LEFT -->

<!-- CONTENT-RIGHT -->
<div id="rightcontent">
    <center><span id="tableTitle" class="title"></span></center><br />
    <div id="table">
            
    </div>
    Statistics are updated every 5 minutes.
</div>
<!-- CONTENT-RIGHT -->


</div>
<!-- SPLIT DIV -->
<?php include($rel_path_php."logo.php"); ?>

<?php include($rel_path_php."footer.php"); ?>


</body>
</html>
