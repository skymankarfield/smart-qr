<?php
include("sendMail.php");

function enterUser($fullname,$username,$email,$password1,$password2,$gamehandle,$formhandle,$agree,&$link,&$error_code)
{
	$consent_ID=0;
	if(trim($username)=="" || trim($password1)=="" || trim($password2)=="" || trim($email)=="" || trim($fullname)=="")
	{
		$error_code = "- All the fields are required.";
		return -1;
	}
	
	if(!preg_match('/^[\w-]+(?:\.[\w-]+)*@(?:[\w-]+\.)+[a-zA-Z]{2,6}$/', $username))
	{
		$error_code = "- Invalid email address.";
		return -1;
	}
	
	$query_exists = "SELECT * FROM user WHERE userKey='".mysqli_real_escape_string($link,$username)."' AND email='".$email."'";
	@mysqli_real_query($link,$query_exists);
	$result_exists = mysqli_store_result($link);
	$number_rows = mysqli_num_rows($result_exists);
	if($number_rows==1)
	{
		$row = mysqli_fetch_assoc($result_exists);
		$user_id = $row['user_ID'];
		
		$query_insert = "INSERT INTO `percom_profiles`.`registered_user_game` (`registered_user_game_ID`, `user_ID`, `game_ID`, `consent_form_ID`, `timestamp`, `REQUEST_TIME`, `QUERY_STRING`, `HTTP_REFERER`, `HTTP_USER_AGENT`, `REMOTE_ADDR`, `REMOTE_HOST`, `REMOTE_PORT`, `active`) VALUES (NULL, '".$user_id."', '".$gamehandle."', '".$formhandle."', CURRENT_TIMESTAMP, '".$_SERVER['REQUEST_TIME']."', '".$_SERVER['QUERY_STRING']."', '".$_SERVER['HTTP_REFERER']."', '".$_SERVER['HTTP_USER_AGENT']."', '".$_SERVER['REMOTE_ADDR']."', 'none', '".$_SERVER['REMOTE_PORT']."', '1')";
		@mysqli_real_query($link,$query_insert);
		$consent_ID=mysqli_insert_id($link);
		
		if($row['active']=="1")
		{
			$emailbody="Dear ".$row['fullName'].",
			
Thank you for agreeing to participate to the GRAND 2011 social-scanning activity.
				
For your convenience, we have added your QR Code to an existing account. You can use the following information to log in to your GRAND-QR acccount:
-username: ".$row['userKey']."
-password: ".$row['password']."
-login website: http://grand.smart-qr.com/index.php

If you have any questions, please contact grand-qr-support@ssrg.cs.ualberta.ca.
		
Thank you!";
			
			if(sendMail("", $row['email'], "GRAND-QR Account Confirmation", $emailbody, &$error_code))
			{
				$error_code=$consent_ID;
				return 1;
			}else
			{
				return -2;
			}
		}elseif($row['active']=="0")
		{
			$emailbody="Dear ".$row['fullName'].",
		
Thank you for agreeing to participate to the GRAND 2011 social-scanning activity.
				
For your convenience, we have added your QR Code to an existing account. You can use the following information to log in to your GRAND-QR acccount:
-username: ".$row['userKey']."
-password: ".$row['password']."
-login website: http://grand.smart-qr.com/index.php

To confirm your consent and activate your account, please click on the link below:
http://grand.smart-qr.com/action.php?accountID=".encrypt($user_id)."&unlock=1

If you have any questions, please contact grand-qr-support@ssrg.cs.ualberta.ca.
	
Thank you!";
			
			if(sendMail("", $row['email'], "GRAND-QR Account Confirmation", $emailbody, &$error_code))
			{
				$error_code=$consent_ID;
				return 1;
			}else
			{
				return -2;
			}
		}
	}else
	{
		$query_exists = "SELECT * FROM user WHERE userKey='".mysqli_real_escape_string($link,$username)."'";
		@mysqli_real_query($link,$query_exists);
		$result_exists = mysqli_store_result($link);
		$number_rows = mysqli_num_rows($result_exists);
		if($number_rows==0)
		{
	
			if($password1!=$password2)
			{
				$error_code = "- Passwords do not match. Try again.";
				return -1;
			}
			
			$query_insert = "INSERT INTO `percom_profiles`.`user` (`user_ID`,`fullName`,`userKey`,`email`,`password`,`timestamp`,`active`) VALUES(NULL,'".mysqli_real_escape_string($link,$fullname)."','".mysqli_real_escape_string($link,$username)."','".mysqli_real_escape_string($link,$email)."','".mysqli_real_escape_string($link,$password1)."',CURRENT_TIMESTAMP,'0')";
			@mysqli_real_query($link,$query_insert);
			$user_id=mysqli_insert_id($link);
				
			if($agree=="1")
			{
				$query_insert = "INSERT INTO `percom_profiles`.`registered_user_game` (`registered_user_game_ID`, `user_ID`, `game_ID`, `consent_form_ID`, `timestamp`, `REQUEST_TIME`, `QUERY_STRING`, `HTTP_REFERER`, `HTTP_USER_AGENT`, `REMOTE_ADDR`, `REMOTE_HOST`, `REMOTE_PORT`, `active`) VALUES (NULL, '".$user_id."', '".$gamehandle."', '".$formhandle."', CURRENT_TIMESTAMP, '".$_SERVER['REQUEST_TIME']."', '".$_SERVER['QUERY_STRING']."', '".$_SERVER['HTTP_REFERER']."', '".$_SERVER['HTTP_USER_AGENT']."', '".$_SERVER['REMOTE_ADDR']."', 'none', '".$_SERVER['REMOTE_PORT']."', '1')";
				@mysqli_real_query($link,$query_insert);
				$consent_ID=mysqli_insert_id($link);
			
			$emailbody="Dear ".$fullname.",
				
Thank you for agreeing to participate to the GRAND 2011 social-scanning activity.
				
You can use the following information to log in to your GRAND-QR account:
-username: ".$username."
-password: ".$password1."
-login website: http://grand.smart-qr.com/index.php
				
To confirm your consent and activate your account, please click on the link below:
http://grand.smart-qr.com/action.php?accountID=".encrypt($user_id)."&unlock=1

If you have any questions, please contact grand-qr-support@ssrg.cs.ualberta.ca.
			
Thank you!";
				
				if(sendMail("", $email, "GRAND-QR Account Confirmation", $emailbody, &$error_code))
				{
					$error_code=$consent_ID;
					return 2;
				}else
				{
					return -2;
				}
			}
		}else
		{
			$error_code = "- Email account already exists. Try with a different one.";
			return -1;
		}
	}
}


?>