<?php
//all the constants here
$base_url='http://localhost/puzzle';
if($_SERVER['REMOTE_ADDR']=='172.16.9.106'){ //if using ip instead of localhost
$base_url='http://you_ip/puzzle';
}
define('base_url',$base_url);
define('host','localhost');
define('user','root');
define('pass','hello');
define('database','puzzle');
define('FILE_SERVER',base_url);
