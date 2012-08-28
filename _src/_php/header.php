<?php $_SESSION['event'] = "grand";  //THIS VARIABLE IS SUPPOSED TO COME FROM THE CLIENT ?>

<?php 
switch($_SESSION['event']){
	case "grand":
		$_SESSION['eventName']="2011 GRAND Conference";
		$_SESSION['gameID']="4";
		$_SESSION['consentIDProfile']="3";
		$_SESSION['consentIDObject']="4";
		$_SESSION['websiteEventID']="1";
	break;
}

$urlGlobalStats = $_SERVER['REQUEST_URI'];
if(!isset($_SESSION['accountID']) && $_SESSION['menu']!="home" && $_SESSION['menu']!="session" && strstr($urlGlobalStats, "global/visualize.php") == false && $_SESSION['menu']!="support" && $_SESSION['menu']!="action")
{
	header("Location:http://grand.smart-qr.com/index.php");
	exit(0);
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $_SESSION['eventName']; ?></title>
<meta name="Description" content="Add website description in this area">
<meta name="KeyWords" content="add, your, keywords in this area, separated, by, commas">

<meta http-equiv="Content-Type" content="text/html;charset=utf-8">

<link rel="shortcut icon" href=""<?php echo $rel_path_img; ?>favicon.ico" >
<link rel="icon" type="image/gif" href=""<?php echo $rel_path_img; ?>animated_favicon1.gif" >
<link href="<?php echo $rel_path_css; ?><?php echo $_SESSION['event']; ?>.css" media="screen" rel="stylesheet" type="text/css" />
<!--[if lt IE 9]>
<style>
    #leftcontent	{
			width: 55%;
			padding-left:20px;
			padding-right:20px;
			}
	
	#rightcontent	{
			width: 35%;
			padding-left: 20px;
			padding-right: 20px;
			}
			
	#logo {
		width: 450px;
		height: 193px;
		display: block;
		margin-left: auto;
		margin-right: auto;
	}
	
	#splitdiv	{
		align:center;
		width: 80%;
		}
    
    th.table {
        padding:20px;
    }
    
    a.cellLink {
        padding:20px;
    }
	
	#consent{
	width: 90%;
	height:400px; 
    }
</style>
<![endif]-->
<meta http-equiv="Content-Language" content="en">
<meta http-equiv="Cache-Control" content="max-age=1200">
<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1"/>
