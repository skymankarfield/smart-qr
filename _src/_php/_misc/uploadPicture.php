<?php

//we need one variable:
//location
//idpicture for picture

if(isset($_FILES['picture']) && count($_FILES['picture'])>0 && strcmp($_FILES['picture']['name'],"")!=0)
{
	$ftmp = $_FILES['picture']['tmp_name'];
	$fname = basename($_FILES['picture']['name']);
	$name_extension = explode(".",$fname);
	$extension = strtolower($name_extension[1]);
	$name = strtolower($name_extension[0]);
	
	if(($_FILES['picture']['size']/1024)>200)
	{
		$error = "- Your file exceeds the maximum file size allowed = 200Kb.";
		$break=true;
		
	}elseif($extension=="jpg" || $extension=="JPG" || $extension=="png" || $extension=="PNG")
	{
		if(!move_uploaded_file($ftmp,"/var/www/vhosts/smart-qr.com/subdomains/grand/html/".$location."/picture".$idpicture.".".$extension))
		{
			$error = "- There has been an error trying to upload your picture. Try again.";
			$break=true;
		}
	}else
	{
		$error = "- Make sure your picture is a .png or .jpg file";
		$break=true;
	}
	$urlPicture = "http://grand.smart-qr.com/".$location."/picture".$idpicture.".".$extension;
}
?>