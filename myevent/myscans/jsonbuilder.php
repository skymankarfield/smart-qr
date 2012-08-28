<?php
//include("../../_src/_php/mysqlConnection.php");
//include("../../_src/_php/_misc/encrypt.php");

include("/var/www/vhosts/smart-qr.com/subdomains/grand/html/_src/_php/mysqlConnection.php");
include("/var/www/vhosts/smart-qr.com/subdomains/grand/html/_src/_php/_misc/encrypt.php");

function getDataMyScans($id,&$link){
    /*$a = array();
	
	$a[0]['nametitle']="Lucio Gutierrez";
	$a[0]['userID']=34;
	
	$a[1]['nametitle']="David Turner";
	$a[1]['userID']=54;
	
	$a[2]['nametitle']="Mike Smith";
	$a[2]['userID']=48;
	
	$a[3]['nametitle']="Matt Delaney";
	$a[3]['userID']=100;
	
	return $a;*/
	$query = "SELECT tags.fullName, invitations.account_ID FROM actions, tags, invitations WHERE actions.account_ID=".$id." AND tags.tag_ID=actions.tag_ID AND tags.tag_ID=invitations.tag_ID AND tags.type='P' GROUP BY tags.tag_ID";
	@mysqli_real_query($link,$query);
	$result = mysqli_store_result($link);
	$i=0;
	$a = array();
	if(mysqli_num_rows($result)>0)
	{
		$row=mysqli_fetch_assoc($result);
		do{
			$a[$i]['nametitle']=$row['fullName'];
			$a[$i]['userID']=encrypt($row['account_ID']);
			$i++;
		}while($row=mysqli_fetch_assoc($result));
	}
	return $a;
}

function buildJSON($id,&$link){
    $data = getDataMyScans($id,$link);
    $json = addslashes(json_encode($data));
    $fileHandler = fopen("/var/www/vhosts/smart-qr.com/subdomains/grand/html/myevent/myscans/static/grand/json".$id, 'w');
    fwrite($fileHandler, $json);
    fclose($fileHandler);
}

$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);
if(!$link)
{	
	echo "Error connecting to MySQL";
	exit(0);
}

$query = "SELECT account_ID FROM actions WHERE type='P' GROUP BY account_ID";
@mysqli_real_query($link,$query);
$result = mysqli_store_result($link);
if(mysqli_num_rows($result)>0)
{
	$row=mysqli_fetch_assoc($result);
	do{
		buildJSON($row['account_ID'],$link);
	}while($row=mysqli_fetch_assoc($result));
}

?>
