<?php

include("../_src/_php/mysqlConnection.php");

$error="";
if(isset($_POST['enter']) && isset($_POST['qr']) && isset($_POST['name']) && isset($_POST['email']))
{
	if(trim($_POST['qr'])!="" && is_numeric($_POST['qr']) && trim($_POST['name'])!="" && trim($_POST['email'])!="")
	{
		$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);
		if(!$link)
		{	
			echo "Error connecting to MySQL";
			exit(0);
		}
		$query="SELECT * FROM tags WHERE tag_ID=".trim($_POST['qr'])." AND fullName='' AND contactEmail=''";
		@mysqli_real_query($link,$query);
		$result = mysqli_store_result($link);
		if(mysqli_num_rows($result)==1)
		{
			$query="UPDATE tags SET fullName='".trim($_POST['name'])."', contactEmail='".trim($_POST['email'])."' WHERE tag_ID=".trim($_POST['qr'])."";
			@mysqli_real_query($link,$query);
			$error = "DONE!, UPDATE IN THE DATABASE WAS SUCCESSFUL";
			
		}else
		{
			$error="The QR ID number does NOT exist. Please, decode the QR Tag again";
		}
		
	}else
	{
		$error="Please, check the input data";
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<body>
<?php if(trim($error)!=""){ echo $error."<br><br>"; }?>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="on-site">
- QR CODE ID:&nbsp;<input type="text" name="qr" id="qr" /><br /><br />
- NAME PARTICIPANT:&nbsp;<input type="text" name="name" id="name" maxlength="100" size="100" /><br /><br />
- EMAIL PARTICIPANT:&nbsp;<input type="text" name="email" id="email" maxlength="100" size="100" /><br /><br /><br />
<input type="submit" name="enter" id="enter" value="ENTER PARTICIPANT" />
</form>

</body>
</html>
