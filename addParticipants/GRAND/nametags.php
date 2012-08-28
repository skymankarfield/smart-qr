<?php

/*IMPORTANT!!!!!!

IN ORDER TO GENERATE NAMETAGS, FIRST EXECUTE THIS FILE IN THE BACKEND TO GENERATE THE QR TAGS IMAGES FOR ALL THE NAMETAGS AT THE SAME TIME. THEN, BY COMMENTING OUT THE QR TAG IMAGE GENERATION ALGORITHM, WE RUN THIS FILE IN THE FRONT-END TO GENERATE THE PDFs.
*/

include ("../../_src/_php/qr_library/qrlib.php");
include("../../_src/_php/_misc/encrypt.php");
//$url = "http://ssrg.cs.ualberta.ca/index.php/Main_Page";
$image_url = "";
//QRcode::png($url, $image_url, 'H', 4, 0);
$link = mysqli_connect("localhost","grandsystem","grand938","grand");
if(!$link)
{
	printf("Connection failed : %s\n", mysqli_connect_error());
	exit();
}

//BATCH1
//batch1 0 - 50
//batch2 51 - 100
//btach3 101-150
//batch4 151-200
//batch5 201-250
//batch6 251-300
//batch7 301-350
//batch8 351-400
//batch9 401-450
//bath10 >450


//BATCH2
//DONE IN ONE SINGLE FILE

//RE-PRINTING NAMETAGS BATCH1 HERE!!!
/*$query = "SELECT * FROM tags WHERE (batchNumber=100 AND event_ID=1) OR (tag_ID=152 AND event_ID=1 AND batchNumber=1) OR (tag_ID=150 AND event_ID=1 AND batchNumber=1) ORDER BY tag_ID";
@mysqli_real_query($link,$query);
$result = mysqli_store_result($link);
$row=array();
if(mysqli_num_rows($result)>0)
{
	$row = mysqli_fetch_assoc($result);
}*/
/////////////////////////

$query = "SELECT * FROM tags WHERE batchNumber=4 AND event_ID=1 ORDER BY tag_ID";
@mysqli_real_query($link,$query);
$result = mysqli_store_result($link);
$row=array();
if(mysqli_num_rows($result)>0)
{
	$row = mysqli_fetch_assoc($result);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GRAND nametags</title>
</head>

<body>

	<?php do{ 
	
	
	$url = "http://grand.smart-qr.com/action.php?tagid=".encrypt($row['tag_ID'])."&eventid=".encrypt(1)."";
	$image_url = "qrs/qr-".$row['tag_ID'].".png";
	//QRcode::png($url, $image_url, 'H', 4, 0);
	
	//$query = "UPDATE tags SET url='".$url."' WHERE tag_ID=".$row['tag_ID'];
	//@mysqli_real_query($link,$query);  //This is ALSO executed in the backend!!!

	?>
	 <div style="height:530px;">
	<table width="330" border="0" cellspacing="10" cellpadding="0">
      <tr>
        <td height="149"><img width="348" height="149" src="Grand2011_Header.png" /></th>
      </tr>
      
      <tr>
        <td style="font-size:24px;" align="center"><strong><?php echo $row['fullName']; ?></strong>&nbsp;</td>
      </tr>
	  <!--<tr>
        <td style="font-size:20px" align="center"><?php //echo $row['titlePerson']; ?>&nbsp;</td>
      </tr>
	  <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td style="font-size:22px" align="center"><?php //echo $row['affiliation']; ?>&nbsp;</td>
      </tr>
      <tr>
        <td style="font-size:20px" align="center"><?php //echo $row['description']; ?>&nbsp;</td>
      </tr>-->
	  <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td align="center"><img src="<?php echo $image_url;?>" height="150"/>&nbsp;</td>
      </tr>
    </table>
	</div>
	<?php if($row = mysqli_fetch_assoc($result))
	{
	
	$url = "http://grand.smart-qr.com/action.php?tagid=".encrypt($row['tag_ID'])."&eventid=".encrypt(1)."";
	$image_url = "qrs/qr-".$row['tag_ID'].".png";
	//QRcode::png($url, $image_url, 'H', 4, 0);
	
	//$query = "UPDATE tags SET url='".$url."' WHERE tag_ID=".$row['tag_ID'];
	//@mysqli_real_query($link,$query);    //This is ALSO executed in the backend!!!

	
	?>
	<div style="height:530px;">
	<table width="330" border="0" cellspacing="10" cellpadding="0">
      <tr>
        <td height="149"><img width="348" height="149" src="Grand2011_Header.png" />&nbsp;</th>
      </tr>
      <tr>
        <td style="font-size:24px;" align="center"><strong><?php echo $row['fullName']; ?></strong>&nbsp;</td>
      </tr>
	   <!--<tr>
        <td style="font-size:20px" align="center"><?php //echo $row['titlePerson']; ?>&nbsp;</td>
      </tr>
	  <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td style="font-size:22px" align="center"><?php //echo $row['affiliation']; ?>&nbsp;</td>
      </tr>
      <tr>
        <td style="font-size:20px" align="center"><?php //echo $row['description']; ?>&nbsp;</td>
      </tr>-->
      <tr>
        <td>&nbsp;</td>
      </tr>
	  <tr>
        <td align="center"><img src="<?php echo $image_url;?>" height="150"/>&nbsp;</td>
      </tr> 
    </table>
	</div>
	<?php
	}
	?>
  <!--<br /><br /><br /><br /> -->
  <?php
	}
	while($row = mysqli_fetch_assoc($result));
	?>

</body>
</html>
