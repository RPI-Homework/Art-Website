<?
//database that can select, update, insert, and delete
$database = array
(
	'name'		=> 'salomj_list',
	'user'		=> 'salomj_advance',
	'password'	=> '-?HE)M@R[Apu',
	'host'		=> 'localhost',
	'url'		=> 'http://salomj.myrpi.org/',
);
$link = mysql_connect($database['host'], $database['user'], $database['password']);
mysql_select_db($database['name'], $link);
unset($database);
?>