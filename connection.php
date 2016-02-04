<?php
//connection file
include_once 'defines.php';
$db=mysql_connect(host,user,pass) or die("Connection Error");
mysql_select_db(database,$db) or die("Database Error");

?>
