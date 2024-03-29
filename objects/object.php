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
$_SESSION['menu']="objects";
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
<?php include($rel_path_php."_misc/decrypt.php"); ?>
<!-- HERE I CAN INCLUDE ANY OTHER SPECIFIC LIBRARIES FOR THIS PAGE -->
<script src="<?php echo $rel_path_js; ?>jquery.js" type="text/javascript"></script>
<script src="<?php echo $rel_path_js; ?>flexcroll.js" type='text/javascript'></script>
<script type="text/javascript">
    var json = eval("(" + '<?php
        $fileName = "./static/".$_SESSION['event']."/private".decrypt($_GET['object_id']);
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
    
    var json2 = eval("(" + '<?php
        $fileName = "./static/".$_SESSION['event']."/comments".decrypt($_GET['object_id']);
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
        var public = "Private";
        if(json['public'] == 'P'){
            public = "Public";
        }
        html += "<b>Visibility:</b><br />" + public + "<br /><br />\n";
        if(json['description'] != ""){
            html += "<b>Description:</b><br />" + json['description'] + "<br /><br />\n";
        }
        if(json['keywords'] != ""){
            html += "<b>Keywords:</b><br />" + json['keywords'] + "<br /><br />\n";
        }
        html += "<form action='edit.php?object_id=<?php echo $_GET['object_id']; ?>' method='post'>\n";
        html += "   <input type='hidden' name='picture' value='" + json['picture'] + "' />\n";
        html += "   <input type='hidden' name='file' value='" + json['file'] + "' />\n";
        html += "   <input type='hidden' name='title' value='" + json['title'].replace(/'/g, '&#39;') + "' />\n";
        html += "   <input type='hidden' name='public' value='" + json['public'] + "' />\n";
        html += "   <input type='hidden' name='description' value='" + json['description'].replace(/'/g, '&#39;') + "' />\n";
        html += "   <input type='hidden' name='keywords' value='" + json['keywords'].replace(/'/g, '&#39;') + "' />\n";
        html += "   <input type='submit' name='submit' value='Edit Exhibit' style='font-size:18px;' />";
        html += "</form>";
        html += "</div>\n";
        $("#table").html(html);
        
        var html2 = "There have been no comments.";
        if(typeof json2 != "undefined"){
            for(var i = 0; i < json2.length; i++){
                if(html2 == "There have been no comments."){
                    html2 = "";
                }
                var rating = "<font color='#ff0000'>Dislike</font>";
                if(json2[i]['rating'] == "5"){
                    rating = "<font color='#00FF00'>Like</font>";
                }   
                html2 += "<br/>- Rating: " + rating + "<br />- Comment: ";
                if(typeof json2[i]['comment'] != 'undefined'){
                    html2 += json2[i]['comment'] + "";
                }
                html2 += "<br /><br /><hr class='page-splits-small' />";
            }
			
			/*var rating = "<font color='#ff0000'>Dislike</font>";
            html2 += "<br/>- Rating: " + rating + "<br />- Comment: ";
            html2 += "This is really ugly!";
            html2 += "<br /><br /><hr class='page-splits-small' />";
			
			var rating = "<font color='#00FF00'>Like</font>";
            html2 += "<br/>- Rating: " + rating + "<br />- Comment: ";
            html2 += "Good Work!";
            html2 += "<br /><br /><hr class='page-splits-small' />";
			
			var rating = "<font color='#00FF00'>Like</font>";
            html2 += "<br/>- Rating: " + rating + "<br />- Comment: ";
            html2 += "It seems reasonable!";
            html2 += "<br /><br /><hr class='page-splits-small' />";*/
			
        }
        $("#comments").html(html2);
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
    <span class='title'>Comments</span><br />
    <br />
    <div id="comments" class='flexcroll' style='height:200px; background:#629E4E; padding:6px;'>
    
    </div>
</div>
<!-- CONTENT-RIGHT -->


</div>
<!-- SPLIT DIV -->


<br>


<?php include($rel_path_php."footer.php"); ?>


</body>
</html>
