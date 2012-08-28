<?php
# THIS IS THE PHP PEAR CLASS USED FOR SENDING OUR EMAIL
include("Mail.php");

function sendMail($toName, $toEmail, $mainsubject, $body, &$error_code)
{
	$host = "hypatia.cs.ualberta.ca";
	$username = "";
	$password = "";
	
	# SENDERS INFORMATION
	$FromName = "grand-qr-support";
	$FromEmail = "grand-qr-support@ssrg.cs.ualberta.ca";
	
	# RECIPIENT INFORMATION
	$ToName = $toName;//"Mike";
	$ToEmail = $toEmail;//"anything@mikesmit.com";
	
	# EMAIL TITLE
	$subject = $mainsubject;//"Testing email";
	
	# EMAIL CONTENT
	$from = "$FromName<$FromEmail>";
	$to = "$ToName <$ToEmail>";
	$headers = array ('From' => $from,
	'To' => $to,
	'Subject' => $subject);
	$smtp = Mail::factory('smtp',
	array ('host' => $host,
	'auth' => false,
	'username' => $username,
	'password' => $password));
	$mail = $smtp->send($to, $headers, $body);
	if (PEAR::isError($mail)) 
	{
		$error_code = $mail->getMessage();
		return false;
	//echo("<p>" . $mail->getMessage() . "</p>");
	}else
	{
		return true;
	//echo("<p>Message successfully sent!</p>");
	}
}
?>
