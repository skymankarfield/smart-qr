<?php

//we need one variable:
//location
//idpicture for picture

if(isset($_FILES['file']) && count($_FILES['file'])>0 && strcmp($_FILES['file']['name'],"")!=0)
{
	$ftmp = $_FILES['file']['tmp_name'];
	$fname = basename($_FILES['file']['name']);
	$name_extension = explode(".",$fname);
	$extension = strtolower($name_extension[1]);
	$name = strtolower($name_extension[0]);
	
	if(($_FILES['file']['size']/1024)>200)
	{
		$error = "- Your file exceeds the maximum file size allowed = 200Kb.";
		$break=true;
		
	}elseif($extension=="pdf" || $extension=="PDF")
	{
		if(!move_uploaded_file($ftmp,"/var/www/vhosts/smart-qr.com/subdomains/grand/html/".$location."/file".$idpicture.".".$extension))
		{
			$error = "- There has been an error trying to upload your file. Try again.";
			$break=true;
		}
	}else
	{
		$error = "- Make sure your file is a PDF";
		$break=true;
	}
	$urlFile = "http://grand.smart-qr.com/".$location."/file".$idpicture.".".$extension;
}
?>