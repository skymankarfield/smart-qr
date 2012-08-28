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
<?php
$error="";
include("jsonbuilder.php");
include($rel_path_php."mysqlConnection.php");
include($rel_path_php."_misc/decrypt.php");
$urlPicture="";
$urlFile="";
?>
<!-- HERE I CAN INCLUDE ANY OTHER SPECIFIC LIBRARIES FOR THIS PAGE -->
<script src="<?php echo $rel_path_js; ?>jquery.js" type="text/javascript"></script>
<script type='text/javascript'>
    function validateFields(){
        var errorMsg = "";
        var disabled = false;
        if(document.getElementById("title").value.replace(/ /g, "") == ""){
            errorMsg += '<span style="color:#FFFF66; font-weight:bold;">- You must include a title.</span><br /><br>';
            disabled = true;
        }
        //if(document.getElementById("keywords").value.replace(/ /g, "") == ""){
        //    errorMsg += "You must include keywords for the poster.<br />";
         //   disabled = true;
       // }
        document.getElementById("errors").innerHTML = errorMsg;
        document.getElementById("edit").disabled = disabled;
        return disabled;
    }
    
    function submitForm(){
        var disabled = validateFields();
        if(!disabled){
            document.editform.submit();
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
<?php 
    if(isset($_POST['flag']) && $_POST['flag'] == "Edit" && $_GET['object_id']!="" && is_numeric($_GET['object_id'])){
        $break = false;
        if(str_replace(" ", "", $_POST['title']) == ""){
            $break = true;
			$error = '<span style="color:#FFFF66; font-weight:bold;">- You must include a title.</span><br /><br>';
        }
		
		if(!isset($_POST['deletepicture']))
		{
			$location="objects/images/grand";
			$idpicture=$_GET['object_id'];
			include($rel_path_php."_misc/uploadPicture.php");
		}else
		{
			$urlPicture=" ";
		}
		
		if(!isset($_POST['deletefile']))
		{			
			$location="objects/files/grand";
			$idpicture=$_GET['object_id'];
			include($rel_path_php."_misc/uploadFile.php");
		}else
		{
			$urlFile=" ";
		}
					
        if(!$break){
            //TODO: Update the database
            $link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);
			if(!$link)
			{	
				echo "Error connecting to MySQL";
				exit(0);
			}

			$upload = ($urlPicture!="") ? ",`URLPicture` = '".$urlPicture."'," : ",";
			$upload2 = ($urlFile!="") ? ",`URLFile` = '".$urlFile."'," : ",";
			
			$query="UPDATE `grand`.`tags` SET `fullName` = '".mysqli_real_escape_string($link,$_POST['title'])."'".$upload."`description` = '".mysqli_real_escape_string($link,str_replace("\r\n",'',$_POST['description']))."',
			`keywords` = '".mysqli_real_escape_string($link,$_POST['keywords'])."'".$upload2."`access` = '".mysqli_real_escape_string($link,$_POST['public'])."' WHERE `tags`.`tag_ID` =".decrypt($_GET['object_id']).";";
			@mysqli_real_query($link,$query);
            
			buildJSONPrivateObject(decrypt($_GET['object_id']),&$link);
			buildJSONListObjects($_SESSION['accountID'],$link);
			header("Location:object.php?object_id=".$_GET['object_id']."");
            exit(0);
            
        }
    }
	
	$thepicture="";
	$thefile="";
	
	if(isset($_POST['picture']))
	{
		if($_POST['picture']!=" " && $_POST['picture']!="")
		{
			$thepicture=$_POST['picture'];
		}
	}elseif(isset($_POST['picturepost']))
	{
		if($_POST['picturepost']!=" " && $_POST['picturepost']!="")
		{
			$thepicture=$_POST['picturepost'];
		}
	}
	
	if(isset($_POST['file']))
	{
		if($_POST['file']!=" " && $_POST['file']!="")
		{
			$thefile=$_POST['file'];
		}
	}elseif(isset($_POST['filepost']))
	{
		if($_POST['filepost']!=" " && $_POST['filepost']!="")
		{
			$thefile=$_POST['filepost'];
		}
	}
?>
    <br />
    <span class='title'>Editing Exhibit</span><br /><br>
    <div id='errors'>
    
    </div>
    <font color='#FF0000'>*</font> Indicates a required Field<br><br>
    <form enctype ="multipart/form-data" id='form' name='editform' action='edit.php?object_id=<?php echo $_GET['object_id']; ?>' method='post' onSubmit="if (!validateFields()) {return false;}">
        Current image:<br /><br><?php if($thepicture!="" && $thepicture!=" "){ ?><img width='128' alt='No Picture' src='<?php echo $thepicture; ?>' /><input type="checkbox" name="deletepicture" id="deletepicture" value="1" />[DELETE]<?php }else{ echo ""; } ?><br /><br>&nbsp;(200Kb max)&nbsp;<input class='replaceInput' type='file' name='picture' /><br /><br />
        Current PDF File:<br><br> <?php if($thefile!="" && $thefile!=" "){ ?><a href="<?php echo $thefile; ?>">Download PDF</a>&nbsp;&nbsp;<input type="checkbox" name="deletefile" id="deletefile" value="1" />[DELETE]<?php }else{ echo ""; }?><br /><br>&nbsp;(200Kb max)&nbsp;<input class='replaceInput' type='file' name='file' /><br /><br />
        <font color='#FF0000'></font> <font color='#FF0000'>*</font> Title:<br /><input id='title' onKeyUp="validateFields();" class='replaceInput' maxlength="100" size="20" type='text' name='title' value='<?php echo str_replace("'", "&#39;", $_POST['title']); ?>' /><br /><br />
        <font color='#FF0000'></font>Visibility:<br /><input type='radio' name='public' value='P' <?php if($_POST['public'] == "P"){echo "checked";} ?> />Public<br /><input type='radio' name='public' value='V' <?php if($_POST['public'] == "V"){echo "checked";} ?> />Private<br /><br />
        Description:<br /><textarea style='width:80%; height:100px;' class='replaceInput' onKeyUp="validateFields();" type='text' name='description'><?php echo str_replace("'", "&#39;", $_POST['description']); ?></textarea><br /><br />
        <font color='#FF0000'></font>Keywords:<br /><input id='keywords' class='replaceInput' onKeyUp="validateFields();" size="20" maxlength="100" type='text' name='keywords' value='<?php echo str_replace("'", "&#39;", $_POST['keywords']); ?>' /><br /><br />
		<input type='hidden' name="picturepost" id="picturepost" value="<?php if(isset($_POST['picture'])){ echo $_POST['picture']; }elseif(isset($_POST['picturepost'])){ echo $_POST['picturepost']; } ?>" />
		<input type='hidden' name="filepost" id="filepost" value="<?php if(isset($_POST['file'])){ echo $_POST['file']; }elseif(isset($_POST['filepost'])){ echo $_POST['filepost']; } ?>" />
        <input type='hidden' name='flag' value='Edit' />
        <input id='edit' style='font-size:18px;' onKeyUp="validateFields();" type='button' onClick="submitForm();" name='edit' value='Save Exhibit' /><br />
    </form>
</div>
<script>
    validateFields();
</script>
<!-- CONTENT-LEFT -->




<!-- CONTENT-RIGHT -->
<div id="rightcontent">
    <br />
    <span class='title'>Exhibit</span><br />
    <br />
    This is a exhibit you will be sharing with people who scan its QR code. You can edit it to provide as much information as you wish. If you set its visibility to "private" only the people who scan its QR code will have access to it; if you set its visibility to "public" all event participants will have access to it.<br />
    
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
<?php if($error!=''){ ?>
<script>
function displayError()

{
    var errorMsg = "";
	errorMsg += '<span style="color:#FFFF66; font-weight:bold;"><?php echo $error; ?></span><br /><br>';
	document.getElementById("errors").innerHTML = errorMsg;
}
displayError();
</script>
<?php } ?>
