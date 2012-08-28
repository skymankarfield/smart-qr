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
<?php include($rel_path_php."_misc/decrypt.php"); ?>
<!-- HERE I CAN INCLUDE ANY OTHER SPECIFIC LIBRARIES FOR THIS PAGE -->
<script src="<?php echo $rel_path_js; ?>jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    var json = eval("(" + '<?php
        $fileName = "../../profiles/static/".$_SESSION['event']."/private".decrypt($_GET['account_id']);
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
<br>
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
