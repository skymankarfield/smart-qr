<?php
include($rel_path_php."_misc/encrypt.php");
function getDataPrivateProfile($id,&$link){
    // "" represents an empty field(ie. do not display in the profile page
    $a = array();
	
	/*//Required
	$a['fullName'] = "Lucio Gutierrez";
	$a['email'] = "lucio@ualberta.ca";
	$a['tagid'] = "1";
	
	//Optional
	$a['picture'] = "";
	$a['address'] = "";
	$a['affiliation'] = "University of Alberta";
	$a['url'] = "";
	$a['phone'] = "";
	$a['title'] = "Student";
	$a['keywords'] = "";
	*/
	
	$query = "SELECT * FROM tags, invitations WHERE invitations.account_ID=".mysqli_real_escape_string($link,$id)." AND invitations.event_ID=".$_SESSION['eventID']." AND invitations.tag_ID=tags.tag_ID AND tags.type='P' and invitations.status='1' AND invitations.scan='1'";
	@mysqli_real_query($link,$query);
	$result = mysqli_store_result($link);
	if(mysqli_num_rows($result)==1)
	{
		$row = mysqli_fetch_assoc($result);
		$a = array();
	
		//Required
		$a['fullName'] = $row['fullName'];
		$a['email'] = $row['contactEmail'];
		$a['tagid'] = encrypt($row['tag_ID']);
		
		//Optional
		$a['picture'] = $row['URLPicture'];
		$a['address'] = $row['address'];
		$a['affiliation'] = $row['affiliation'];
		$a['url'] = $row['homepage'];
		$a['phone'] = $row['phoneNo'];
		$a['title'] = $row['titlePerson'];
		$a['keywords'] = $row['keywords'];
		$a['public'] = $row['access'];
		$a['project'] = $row['project'];
				
		return $a;
	}
}

function buildJSONPrivateProfile($id,&$link){
    $data = getDataPrivateProfile($id,$link);
    $json = addslashes(json_encode($data));
    $fileHandler = fopen("./static/".$_SESSION['event']."/private".$id, 'w');  //accountID FROM DATABASE
    fwrite($fileHandler, $json);
    fclose($fileHandler);
}

?>
