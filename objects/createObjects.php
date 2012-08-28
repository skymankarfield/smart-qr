<?php
include($rel_path_php."_misc/encrypt.php");
function getDataListObjects($id,&$link){
    $a = array();
	
	/*$a['0']['title'] = "This is the name of a MEOW Poster";
	$a['0']['id'] = encrypt("45");
	
	$a['1']['title'] = "This is the name of a HLTHSIM Poster";
	$a['1']['id'] = encrypt("46");
	*/
	
	$query = "SELECT * FROM invitations, tags WHERE invitations.account_ID=".$id." AND invitations.event_ID=1 AND invitations.tag_ID=tags.tag_ID AND tags.type='O' AND invitations.status='1'";
	@mysqli_real_query($link,$query);
	$result = mysqli_store_result($link);
	$i=0;
	$a = array();
	if(mysqli_num_rows($result)>0)
	{
		$row = mysqli_fetch_assoc($result);
		do{
			$a[$i]['title']=$row['fullName'];
			$a[$i]['id']=encrypt($row['tag_ID']);
			$i++;
		}while($row=mysqli_fetch_assoc($result));
	}					
	
	return $a;
}

function getDataPrivateObject($id,&$link){
    // "" represents an empty field(ie. do not display in the profile page
    $a = array();
    /*
    $a['picture'] = "http://www.ff-type0.net/images/media/4db9b595_YYN7LYrag5VfAnAFN75xDJFlmD5cxkLx.jpg";
    $a['file'] = "";
    $a['title'] = "This is the name of a MEOW Poster";
    $a['public'] = "Public";
    $a['description'] = "This is a crazy description.  It is also a very long description which will probably wrap to the next line.";
    $a['keywords'] = "Media Graphics";
    */
	
	$query = "SELECT * FROM tags WHERE tag_ID=".$id." AND event_ID=".$_SESSION['eventID']."";
	@mysqli_real_query($link,$query);
	$result = mysqli_store_result($link);
	if(mysqli_num_rows($result)==1)
	{
		$row = mysqli_fetch_assoc($result);
		$a = array();
	
		$a['picture'] = $row['URLPicture'];
		$a['file'] = $row['URLFile'];
		$a['title'] = $row['fullName'];
		$a['public'] = $row['access'];
		$a['description'] = $row['description'];
		$a['keywords'] = $row['keywords'];
		$a['project'] = $row['project'];
	
    	return $a;
	}
}

function buildJSONListObjects($id,&$link){
    $data = getDataListObjects($id,$link);
    $json = addslashes(json_encode($data));
    $fileHandler = fopen("./static/grand/list".$id, 'w');
    fwrite($fileHandler, $json);
    fclose($fileHandler);
}

function buildJSONPrivateObject($id,&$link){
    $data = getDataPrivateObject($id,$link);
    $json = addslashes(json_encode($data));
    $fileHandler = fopen("./static/grand/private".$id, 'w');
    fwrite($fileHandler, $json);
    fclose($fileHandler);
}
?>
