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
$urlPicture="";
include("jsonbuilder.php");
include($rel_path_php."mysqlConnection.php");
include($rel_path_php."_misc/decrypt.php");

?>
<!-- HERE I CAN INCLUDE ANY OTHER SPECIFIC LIBRARIES FOR THIS PAGE -->
<script src="<?php echo $rel_path_js; ?>jquery.js" type="text/javascript"></script>
<script type='text/javascript'>
    function validateFields(){
        var errorMsg = "";
        var disabled = false;
        if(document.getElementById("fullName").value.replace(/ /g, "") == ""){
            errorMsg += '<span style="color:#FFFF66; font-weight:bold;">- You must include your full name.</span><br /><br>';
            disabled = true;
        }
        if(document.getElementById("email").value.replace(/ /g, "") == ""){
            errorMsg += '<span style="color:#FFFF66; font-weight:bold;">- You must include an email.</span><br /><br>';
            disabled = true;
        }
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
    if(isset($_POST['flag']) && $_POST['flag'] == "Edit" && $_POST['tagid']!="" && is_numeric($_POST['tagid'])){
        $break = false;
        if(str_replace(" ", "", $_POST['fullName']) == ""){
			$error = '<span style="color:#FFFF66; font-weight:bold;">- You must include your full name.</span><br /><br>';
            $break = true;
        }
        if(str_replace(" ", "", $_POST['email']) == ""){
			$error = '<span style="color:#FFFF66; font-weight:bold;">- You must include an email.</span><br /><br>';
            $break = true;
        }
		
		if(!isset($_POST['deletepicture']))
		{
			$location="profiles/images/grand";
			$idpicture=encrypt($_SESSION['accountID']);
			include($rel_path_php."_misc/uploadPicture.php");
		}else
		{
			$urlPicture=" ";
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
		
			$query = "UPDATE `grand`.`tags` SET `fullName` = '".mysqli_real_escape_string($link,$_POST['fullName'])."',
			`titlePerson` = '".mysqli_real_escape_string($link,$_POST['title'])."',
			`affiliation` = '".mysqli_real_escape_string($link,$_POST['affiliation'])."',
			`address` = '".mysqli_real_escape_string($link,$_POST['address'])."',
			`homepage` = '".mysqli_real_escape_string($link,$_POST['url'])."',
			`phoneNo` = '".mysqli_real_escape_string($link,$_POST['phone'])."',
			`contactEmail` = '".mysqli_real_escape_string($link,$_POST['email'])."',
			`keywords` = '".mysqli_real_escape_string($link,$_POST['keywords'])."'".$upload."`access` = '".mysqli_real_escape_string($link,$_POST['public'])."' WHERE `tags`.`tag_ID` = ".mysqli_real_escape_string($link,decrypt($_POST['tagid'])).";";
			
			@mysqli_real_query($link,$query);
            //Redirect after the update
			buildJSONPrivateProfile($_SESSION['accountID'],$link);
			header("Location:index.php");
			exit(0);
        }
    }
	
	$thepicture="";
	
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
	
	
?>
    <br />
    <span class='title'>Editing Profile</span><br /><br>
    <div id='errors'>
    	
    </div>
    <font color='#FF0000'>*</font> Indicates a required Field<br /><br />
    <form enctype ="multipart/form-data" id='form' name='editform' action='edit.php' method='post' onSubmit="if (!validateFields()) {return false;}">
        Your current image:<br /><br><?php if($thepicture!="" && $thepicture!=" "){ ?><img width='128' alt='No Picture' src='<?php echo $thepicture; ?>' /><input type="checkbox" name="deletepicture" id="deletepicture" value="1" />[DELETE]<?php }else{ echo ""; } ?><br /><br>&nbsp;(200Kb max)&nbsp;<input class='replaceInput' type='file' name='picture' /><br /><br />
        <font color='#FF0000'>*</font>Name:<br /><input id='fullName' onKeyUp="validateFields();" class='replaceInput' maxlength="100" size="100" type='text' name='fullName' value='<?php echo str_replace("'", "&#39;", $_POST['fullName']); ?>' /><br /><br />
        <font color='#FF0000'>*</font>Email:<br /><input id='email' onKeyUp="validateFields();" class='replaceInput' size="20" type='text' maxlength="100" name='email' value='<?php echo str_replace("'", "&#39;", $_POST['email']); ?>' /><br /><br />
        <font color='#FF0000'></font>Visibility:<br /><input type='radio' name='public' value='P' <?php if($_POST['public'] == "P"){echo "checked";} ?> />Public<br /><input type='radio' name='public' value='V' <?php if($_POST['public'] == "V"){echo "checked";} ?> />Private<br /><br />
        Title:<br /><input class='replaceInput' onKeyUp="validateFields();" maxlength="100" size="20" type='text' name='title' value='<?php echo str_replace("'", "&#39;", $_POST['title']); ?>' /><br /><br />
        Affiliation:<br /><input class='replaceInput' onKeyUp="validateFields();" size="100" maxlength="100" type='text' name='affiliation' value='<?php echo str_replace("'", "&#39;", $_POST['affiliation']); ?>' /><br /><br />
        Address:<br /><input class='replaceInput' size="100" maxlength="100" type='text' name='address' value='<?php echo $_POST['address']; ?>' /><br /><br />
        Home&nbsp;Page:<br /><input class='replaceInput' onKeyUp="validateFields();" size="100" maxlength="100" type='text' name='url' value='<?php echo str_replace("'", "&#39;", $_POST['url']); ?>' /><br /><br />
        Phone:<br /><input class='replaceInput' onKeyUp="validateFields();" maxlength="100" size="20" type='text' name='phone' value='<?php echo str_replace("'", "&#39;", $_POST['phone']); ?>' /><br /><br />
        Keywords:<br /><input class='replaceInput' onKeyUp="validateFields();" size="100" maxlength="100" type='text' name='keywords' value='<?php echo str_replace("'", "&#39;", $_POST['keywords']); ?>' /><br /><br />
        <input type='hidden' name='flag' value='Edit' />
		<input type='hidden' name="picturepost" id="picturepost" value="<?php if(isset($_POST['picture'])){ echo $_POST['picture']; }elseif(isset($_POST['picturepost'])){ echo $_POST['picturepost']; } ?>" />
		<input type='hidden' name='tagid' value='<?php echo $_POST['tagid']; ?>' />
        <input id='edit' style='font-size:18px;' onKeyUp="validateFields();" type='button' onClick="submitForm();" name='edit' value='Save Profile' /><br /><br>
    </form>
</div>
<script>
    validateFields();
</script>
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
