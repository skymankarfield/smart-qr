<?php
# THIS IS THE PHP PEAR CLASS USED FOR SENDING OUR EMAIL
include("Mail.php");
include("../mysqlConnection.php");

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
	//'Cc' => "grand-qr-support<grand-qr-support@ssrg.cs.ualberta.ca>");
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



$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);
if(!$link)
{	
	echo "Error connecting to MySQL";
	exit(0);
}
	//$query = "SELECT * FROM tags WHERE contactEmail!='' AND fullName!='' AND batchNumber=4 AND event_ID=1 ORDER BY tag_ID ASC";
	@mysqli_real_query($link,$query);
	$result = mysqli_store_result($link);
	if(mysqli_num_rows($result)>0)
	{
		$row = mysqli_fetch_assoc($result);
		 
		do{
		
$body="For this year's GRAND conference, the MEOW team has developed a tool to support social scanning. The nametags of the conference participants will include QR codes associated with corresponding 'profile pages'; by allowing a new contact to scan your nametag, you will be giving this person access to your profile, during and after the conference. GRAND posters will also be associated with QR codes; scanning the QR code of a poster will allow one to access more information about the poster, again during and after the conference, and to rate (and comment on) the poster.

Through this service we hope to enable increased networking within our community and enable more connections among the GRAND researchers. At the same time, we are interested in analyzing the record of these interactions in order to better understand how people establish connections in such events.

If you wish to participate in this activity, please follow this URL (".$row['url'].") to read more about the design of our study and to provide your voluntary consent to participate, along with a username (the email address which you used to register to the GRAND conference) and a password of your choice. Then you will be able to login to the system and edit your profile; you can choose to provide as much or as little information you want on your profile. If you choose to make your profile public, descriptive statistics of your scanning activity during the conference may be projected on screens during the GRAND opening reception.

If you are the contact author of a poster, you will be receiving this message twice; note that the URLs included in these messages are different since one is associated with your personal profile (accessible through your nametag) and the other is associated with your poster, accessible through the QR code of your poster, which will be provided to you during the poster setup period. In order to associate your personal profile and your poster with the same account, you have to enter the email address of the account where you received your first confirmation email in the consent form.

We are looking forward to connecting with you all during the conference! In the mean time, we will be happy to answer any questions you may have.

Best Regards

Lucio Gutierrez, Ioanis Nikolaidis and Eleni Stroulia
The MEOW Smart-QR team";

			$error_code="";
	
			if(sendMail("", $row['contactEmail'], "[POSTER] QR codes during GRAND 2011", $body, &$error_code))
			{
				$query="INSERT INTO mails VALUES('',".$row['tag_ID'].",'".$row['fullName']."','".$row['contactEmail']."','".$row['url']."')";
				@mysqli_real_query($link,$query);
			}else
			{
				echo "There has been an error: ".$error_code." Stopped at tag_ID=".$row['tag_ID'];
				break;
			}
		}while($row = mysqli_fetch_assoc($result));
	}
?>
