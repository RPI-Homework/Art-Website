<?php
$NextPage = "index.php";
require_once 'Func/Conf.php';
if($SQL->User != 0)
{
	header('Location: ' . $NextPage);
	die();
}
else
{
	$updates = array
	(
		2 => 'login',
		3 => 'logout'
	);
	$index = array
	(
		0 => 'home',
		2 => 'login',
		3 => 'logout'
	);
	$idx = 0;
	if((is_numeric($_POST['idx']) && $_POST['idx'] == 3) || (is_numeric($_GET['idx']) && $_GET['idx'] == 3))
	{
		include_once "Get/logout.php";
	}
	if (is_numeric($_POST['idx']))
	{
		$_POST['idx'] = (int)$_POST['idx'];
		if(array_key_exists($_POST['idx'], $updates))//info updates
		{
			$idx = 1;
			include_once "Post/" . $updates[$_POST['idx']] . ".php";
		}
	}
	echo '<!DOCTYPE PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<title>Grading Program</title>
		<link href="Skin/Skin1/styles.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<div id="bg">
			<div id="main">
				<div id="header">	
					<div id="logo"><a href="#">Rainbow Incorporated</a></div>
				</div>
				<div class="top_bar"></div>
				<div id="content_bg">
					<div id="contents">
						<div id="content">';
	if (is_numeric($_GET['idx']) && $idx == 0)
	{
		$_GET['idx'] = (int)$_GET['idx'];
		if(array_key_exists($_GET['idx'], $index))//pages
		{
			include_once "Get/" . $index[$_GET['idx']] . ".php";
			$idx = 1;
		}
	}
	if($idx == 0)//home page
	{
		include_once "Get/" . $index[0] . ".php";
	}
	echo '
						</div>
					</div>
				</div>
				<div class="bottom_bar">Copyright 2010 Rainbow Inc</div>
			</div>
		</div>
	</body>
</html>';
	unset($updates);
	unset($index);
	unset($idx);
}
unset($NextPage);
?>