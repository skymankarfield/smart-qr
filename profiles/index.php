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
$_SESSION['submenu']="";
//$_SESSION['submenu']="scannedby";

//This has to be in all the pages
$rel_path_img="../_src/_img/";
$rel_path_js="../_src/_js/";
$rel_path_css="../_src/_css/";
$rel_path_php="../_src/_php/";
$rel_path_menulinks="../";
/////////////////////////////////
?>

<?php include($rel_path_php."header.php"); ?>
<?php //include("jsonbuilder.php");
    //buildJSONPrivateProfile(4);
?>
<!-- HERE I CAN INCLUDE ANY OTHER SPECIFIC LIBRARIES FOR THIS PAGE -->
<script src="<?php echo $rel_path_js; ?>jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    var json = eval("(" + '<?php
        $fileName = "./static/".$_SESSION['event']."/private".$_SESSION['accountID'];
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
        var public = "Private";
        if(json['public'] == 'P'){
            public = "Public";
        }
        html += "<b>Visibility:</b><br />" + public + "<br /><br />\n";
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
        
        html += "<form action='edit.php' method='post'>\n";
        html += "   <input type='hidden' name='fullName' value='" + json['fullName'].replace(/'/g, '&#39;') + "' />\n";
        html += "   <input type='hidden' name='picture' value='" + json['picture'] + "' />\n";
        html += "   <input type='hidden' name='email' value='" + json['email'].replace(/'/g, '&#39;') + "' />\n";
        html += "   <input type='hidden' name='address' value='" + json['address'].replace(/'/g, '&#39;') + "' />\n";
        html += "   <input type='hidden' name='affiliation' value='" + json['affiliation'].replace(/'/g, '&#39;') + "' />\n";
        html += "   <input type='hidden' name='title' value='" + json['title'].replace(/'/g, '&#39;') + "' />\n";
        html += "   <input type='hidden' name='url' value='" + json['url'].replace(/'/g, '&#39;') + "' />\n";
        html += "   <input type='hidden' name='keywords' value='" + json['keywords'].replace(/'/g, '&#39;') + "' />\n";
        html += "   <input type='hidden' name='phone' value='" + json['phone'].replace(/'/g, '&#39;') + "' />\n";
        html += "   <input type='hidden' name='public' value='" + json['public'].replace(/'/g, '&#39;') + "' />\n";
		html += "   <input type='hidden' name='tagid' value='" + json['tagid'].replace(/'/g, '&#39;') + "' />\n";
        html += "   <input style='font-size:18px;' type='submit' name='submit' value='Edit Profile' />\n";
        html += "</form>";
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
    <br />
    <span class='title'>Profile</span><br />
    <br />
    This is the profile you will be sharing with people who scan your nametag. You can edit it to provide as much information as you wish. If you set its visibility to "private" only the people who scan your nametag will have access to it; if you set its visibility to "public" all event participants will have access to it.<br />
   <br />
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
