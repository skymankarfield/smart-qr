<?php
include("../_src/_php/_misc/encrypt.php");
include("../_src/_php/mysqlConnection.php");

function getDataPrivateProfile($id,&$link){
    // "" represents an empty field(ie. do not display in the profile page
    $a = array();
	
	$query = "SELECT * FROM tags, invitations WHERE invitations.account_ID=".mysqli_real_escape_string($link,$id)." AND invitations.event_ID=1 AND invitations.tag_ID=tags.tag_ID AND tags.type='P' and invitations.status='1' AND invitations.scan='1'";
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
    $fileHandler = fopen("./static/grand/private".$id, 'w');  //accountID FROM DATABASE
    fwrite($fileHandler, $json);
    fclose($fileHandler);
}

$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);
if(!$link)
{	
	echo "Error connecting to MySQL";
	exit(0);
}

//buildJSONPrivateProfile(9,&$link);

?>
