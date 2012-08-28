<?php
//include("../../_src/_php/mysqlConnection.php");
//include("../../_src/_php/_misc/encrypt.php");

include("/var/www/vhosts/smart-qr.com/subdomains/grand/html/_src/_php/mysqlConnection.php");
include("/var/www/vhosts/smart-qr.com/subdomains/grand/html/_src/_php/_misc/encrypt.php");

function getDataObjecctsIScanned($id,&$link){
/*
    $a = array();
	
	$a[0]['nametitle']="MEOW Poster";
	$a[0]['userID']=34;
	
	$a[1]['nametitle']="NAVEL Poster";
	$a[1]['userID']=54;
	
	$a[2]['nametitle']="HLTHSIM Poster";
	$a[2]['userID']=48;
	
	$a[3]['nametitle']="GAMSIM Poster";
	$a[3]['userID']=100;
	
	$a[4]['nametitle']="BELIEVE Poster";
	$a[4]['userID']=101;
	
	return $a;
	*/
	$query = "SELECT tags.fullName, tags.tag_ID FROM actions, tags WHERE actions.account_ID=".$id." AND tags.tag_ID=actions.tag_ID AND tags.type='O' GROUP BY tags.tag_ID";
	@mysqli_real_query($link,$query);
	$result = mysqli_store_result($link);
	$i=0;
	$a = array();
	if(mysqli_num_rows($result)>0)
	{
		$row=mysqli_fetch_assoc($result);
		do{
			$a[$i]['nametitle']=$row['fullName'];
			$a[$i]['object_id']=encrypt($row['tag_ID']);
			$i++;
		}while($row=mysqli_fetch_assoc($result));
	}
	return $a;
	
}

function buildJSON($id,&$link){
    $data = getDataObjecctsIScanned($id,$link);
    $json = addslashes(json_encode($data));
    $fileHandler = fopen("/var/www/vhosts/smart-qr.com/subdomains/grand/html/myevent/objectsiscanned/static/grand/json".$id, 'w');  //accountID FROM DATABASE
    fwrite($fileHandler, $json);
    fclose($fileHandler);
}

	$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);
	if(!$link)
	{	
		echo "Error connecting to MySQL";
		exit(0);
	}

	$query = "SELECT account_ID FROM actions WHERE type='O' GROUP BY account_ID";
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
