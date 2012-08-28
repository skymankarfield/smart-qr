<div id="menudiv">

<?php if(isset($_SESSION['accountID'])){ ?>


<?php if($_SESSION['profile']==1){ ?>
<div class="menu">
<a href="<?php echo $rel_path_menulinks; ?>myevent/index.php" <?php if ($_SESSION['menu']=='myevent'){
	echo 'class="menulink2"'; } else { echo 'class="menulink"'; }?>><?php echo "My Scans"; ?></a>
</div>
<?php } ?>

<?php if($_SESSION['profile']==1){ ?>
<div class="menu">
<a href="<?php echo $rel_path_menulinks; ?>profiles/index.php" <?php if ($_SESSION['menu']=='profiles'){
	echo 'class="menulink2"'; } else { echo 'class="menulink"'; }?>>My&nbsp;Profile</a>
</div>
<?php } ?>

<?php if($_SESSION['objects']==1){ ?>
<div class="menu">
<a href="<?php echo $rel_path_menulinks; ?>objects/index.php" <?php if ($_SESSION['menu']=='objects'){
	echo 'class="menulink2"'; } else { echo 'class="menulink"'; }?>>My&nbsp;Exhibits</a>
</div>
<?php 
	}

} ?>

<?php if(isset($_SESSION['accountID'])){ ?>
<div class="menu">
<a href="<?php echo $rel_path_menulinks; ?>session/login.php?logout=1" <?php if ($_SESSION['menu']=='session'){
	echo 'class="menulink2"'; } else { echo 'class="menulink"'; }?>>Log&nbsp;Out</a>
</div>
<?php }else
		{
?>
<div class="menu">
<a href="<?php echo $rel_path_menulinks; ?>session/login.php" <?php if ($_SESSION['menu']=='session'){
	echo 'class="menulink2"'; } else { echo 'class="menulink"'; }?>>Log&nbsp;In</a>
</div>
<?php 
		}
?>

<div class="menu">
<a href="<?php echo $rel_path_menulinks; ?>support.php" <?php if ($_SESSION['menu']=='support'){
	echo 'class="menulink2"'; } else { echo 'class="menulink"'; }?>>Support</a>
</div>

</div>

<div class="menu-spacer">&nbsp;</div>

<!-- SUB-MENUS -->
<?php 
switch($_SESSION['menu']){

case "myevent": ?>
<div id="submenudiv">

<?php if($_SESSION['profile']==1){ ?>
<div class="submenu">
<a href="<?php echo $rel_path_menulinks; ?>myevent/myscans/index.php" <?php if ($_SESSION['submenu']=='myscans'){
	echo 'class="menulink2"'; } else { echo 'class="menulink"'; }?>>Participants</a>
</div>
<?php } ?>

<?php if($_SESSION['profile']==1){ ?>
<div class="submenu">
<a href="<?php echo $rel_path_menulinks; ?>myevent/objectsiscanned/index.php" <?php if ($_SESSION['submenu']=='objectsiscanned'){
	echo 'class="menulink2"'; } else { echo 'class="menulink"'; }?>>Exhibits</a>
</div>
<?php } ?>

</div><!-- SUB-MENU END -->
<?php   //CHANGE THIS TO ANOTHER SUB-MENU
break;


case "profiles": ?>
<div id="submenudiv">
<?php if($_SESSION['profile']==1){ ?>
<div class="submenu">
<a href="<?php echo $rel_path_menulinks; ?>myevent/scannedby/index.php" <?php if ($_SESSION['submenu']=='scannedby'){
	echo 'class="menulink2"'; } else { echo 'class="menulink"'; }?>>Scans&nbsp;By</a>
</div>
<?php } ?>


</div><!-- SUB-MENU END -->
<?php   //CHANGE THIS TO ANOTHER SUB-MENU
break;

case "objects": ?>
<div id="submenudiv">
<?php if($_SESSION['objects']==1){ ?>
<div class="submenu">
<a href="<?php echo $rel_path_menulinks; ?>myevent/objectsscannedby/index.php" <?php if ($_SESSION['submenu']=='objectsscannedby'){
	echo 'class="menulink2"'; } else { echo 'class="menulink"'; }?>>Scans&nbsp;By</a>
</div>
<?php }

break;

 } ?>


<div class="menu-spacer">&nbsp;</div>
