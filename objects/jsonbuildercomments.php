<?php
//include("../_src/_php/mysqlConnection.php");
//include("../_src/_php/_misc/encrypt.php");

include("/var/www/vhosts/smart-qr.com/subdomains/grand/html/_src/_php/mysqlConnection.php");
include("/var/www/vhosts/smart-qr.com/subdomains/grand/html/_src/_php/_misc/encrypt.php");

function getDataComments($id,$link){
    $a = array();
    
   /* $a['0']['name'] = "David Turner";
    $a['0']['rating'] = "Like";
    $a['0']['comment'] = "Wow what an amazing poster, very informative";
    
    return $a;*/
	
	//$query="SELECT rate, comment FROM actions WHERE actions.tag_ID=".$id." AND actions.type='O' ORDER BY timestamp ASC";
	$query="SELECT account_ID, rate, comment FROM actions WHERE actions.tag_ID=".$id." AND actions.type='O' ORDER BY timestamp ASC";
	@mysqli_real_query($link,$query);
	$result = mysqli_store_result($link);
	$i=0;
	$a = array();
	if(mysqli_num_rows($result)>0)
	{
		$row=mysqli_fetch_assoc($result);
		do{
			$a[$i]['rating']=$row['rate'];
			$a[$i]['comment']=$row['comment'];
			$a[$i]['userID'] = encrypt($row['account_ID']);
			$i++;
		}while($row=mysqli_fetch_assoc($result));
	}
	return $a;
}


function buildJSONComments($id,&$link){
    $data = getDataComments($id,$link);
    $json = addslashes(json_encode($data));
    $fileHandler = fopen("/var/www/vhosts/smart-qr.com/subdomains/grand/html/objects/static/grand/comments".$id, 'w');
    fwrite($fileHandler, $json);
    fclose($fileHandler);
}


$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);
if(!$link)
{	
	echo "Error connecting to MySQL";
	exit(0);
}

$query = "SELECT tags.tag_ID FROM tags, actions WHERE actions.tag_ID=tags.tag_ID AND actions.type='O' AND tags.type='O' GROUP BY tags.tag_ID";
@mysqli_real_query($link,$query);
$result = mysqli_store_result($link);
if(mysqli_num_rows($result)>0)
{
	$row=mysqli_fetch_assoc($result);
	do{
		buildJSONComments($row['tag_ID'],$link);
	}while($row=mysqli_fetch_assoc($result));
}


?>