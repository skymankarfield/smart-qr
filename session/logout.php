<?php
session_start();
unset($_SESSION['eventID']);
unset($_SESSION['accountID']);
unset($_SESSION['userKey']);
unset($_SESSION['scan']);
unset($_SESSION['fullName']);

header("Location:login.php");
exit(0);

?>