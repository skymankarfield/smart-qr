<?php

function checkUser($username,$password,&$link,&$error_code)
{
	if($username=="" || $password=="")
	{
		$error_code = '<span style="color:#FFFF66; font-weight:bold;">- Email and password are both required. Please, try again.</span>';
		return false;
	}
	
	$query_exists = "SELECT * FROM user WHERE userKey='".mysqli_real_escape_string($link,$username)."' AND password='".mysqli_real_escape_string($link,$password)."'";
	@mysqli_real_query($link,$query_exists);
	$result_exists = mysqli_store_result($link);
	$number_rows = mysqli_num_rows($result_exists);
	if($number_rows==1)
	{
		$row = mysqli_fetch_assoc($result_exists);
		if($row['active']=='1')
		{
			$error_code = $row['fullName'];
			return $row['userKey'];
		}else
		{
			$error_code = '<span style="color:#FFFF66; font-weight:bold;">- The account has not been activated yet. Please, activate your account.</span>';
			return false;
		}
	}else
	{
		$error_code = '<span style="color:#FFFF66; font-weight:bold;">- This account does not exist in the system.</span>';
		return false;
	}
	
}

?>