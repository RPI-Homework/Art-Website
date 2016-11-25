<?
require_once "gets/sql.php";
require_once "gets/error.php";
require_once 'gets/cookie.php';
$updates = array
(
	2 => 'register'
);
$index = array
(
	0 => 'home',
	2 => 'register',
	3 => 'friendsoncity',
	4 => 'friends',
	5 => 'login',
);
$idx = 0;
if (is_numeric($_POST['idx']))
{
	$_POST['idx'] = (int)$_POST['idx'];
	if(array_key_exists($_POST['idx'], $index))//info updates
	{
		$idx = 1;
		include_once "updates/" . $updates[$_POST['idx']] . ".php";
	}
}
require_once "skin/top.php";
if (is_numeric($_GET['idx']) && $idx == 0)
{
	$_GET['idx'] = (int)$_GET['idx'];
	if(array_key_exists($_GET['idx'], $index))//pages
	{
		include_once "pages/" . $index[$_GET['idx']] . ".php";
		$idx = 1;
	}
}
if($idx == 0)//home page
	include_once "pages/" . $index[0] . ".php";
require_once "skin/bottom.php";
mysql_close($link);
?>