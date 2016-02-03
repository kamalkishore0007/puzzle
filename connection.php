<?php
//connection file

if($_SERVER['HTTP_HOST']=='localhost')
{

$host="localhost";
$db=mysql_connect($host,"root","")
or die("Connection Error");
mysql_select_db("puzzle",$db)
or die("Database Error");

}
elseif($_SERVER['HTTP_HOST']=='172.16.9.106')
{
$host="localhost";
$db=mysql_connect($host,"root","hello")
or die("Connection Error");
mysql_select_db("puzzle",$db)
or die("Database Error");

}
else{
$host="live host";
$db=mysql_connect($host,"username","password")
or die("Connection Error");
mysql_select_db("database name",$db)
or die("Database Error");
}
define('base_path',__DIR__);
define('base_url','http://172.16.9.106/kamal-data/puzzle');
define('FILE_SERVER',base_url);
?>
