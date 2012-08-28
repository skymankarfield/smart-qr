<?php
//include("../../_src/_php/mysqlConnection.php");
//include("../../_src/_php/_misc/encrypt.php");

include("/var/www/vhosts/smart-qr.com/subdomains/grand/html/_src/_php/mysqlConnection.php");
include("/var/www/vhosts/smart-qr.com/subdomains/grand/html/_src/_php/_misc/encrypt.php");

function getDataObjectsScannedBy($id,&$link){

   /* $a = array();
	
	$a[0]['nametitle']="MEOW Poster";
	$a[0]['userID']=34;
	
	return $a;
	*/
	$query = "SELECT actions.account_ID FROM tags, invitations, actions WHERE tags.type='O' AND invitations.tag_ID=tags.tag_ID AND actions.tag_ID=tags.tag_ID AND invitations.account_ID=".$id." AND actions.type='O' GROUP BY actions.account_ID";
	@mysqli_real_query($link,$query);
	$result = mysqli_store_result($link);
	$i=0;
	$a = array();
	if(mysqli_num_rows($result)>0)
	{
		$row=mysqli_fetch_assoc($result);
		do{

			$query2 = "SELECT tags.fullName FROM tags, invitations WHERE invitations.tag_ID=tags.tag_ID AND invitations.account_ID=".$row['account_ID']." AND status='1' AND scan='1'";
			@mysqli_real_query($link,$query2);
			$result2 = mysqli_store_result($link);
			if(mysqli_num_rows($result2)>0)
			{
				$row2=mysqli_fetch_assoc($result2);
				$a[$i]['userID']=encrypt($row['account_ID']);
				$a[$i]['nametitle']=$row2['fullName'];
			}
			$i++;
		}while($row=mysqli_fetch_assoc($result));
	}
	return $a;
}

function buildJSON($id,&$link){
    $data = getDataObjectsScannedBy($id,$link);
    $json = addslashes(json_encode($data));
    $fileHandler = fopen("/var/www/vhosts/smart-qr.com/subdomains/grand/html/myevent/objectsscannedby/static/grand/json".$id, 'w');  //accountID FROM DATABASE
    fwrite($fileHandler, $json);
    fclose($fileHandler);
}


$link = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdata);
	if(!$link)
	{	
		echo "Error connecting to MySQL";
		exit(0);
	}

	$query = "SELECT invitations.account_ID FROM tags, invitations, actions WHERE tags.type='O' AND invitations.tag_ID=tags.tag_ID AND actions.tag_ID=tags.tag_ID AND actions.type='O' GROUP BY invitations.account_ID";
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
