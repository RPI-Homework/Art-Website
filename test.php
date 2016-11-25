<?php
if($allowtest == 1)
{
	//Edit Below
	$maxwidth = 690;
	$maxsamplewidth = 100;
	$maxsampleheight = 100;
	$displaydates = false;
	$acceptedimages = array('image/vnd.wap.wbmp', 'image/png', 'image/gif', 'image/jpeg');
	$acceptedvideos = array('video/quicktime', 'video/mpeg', 'video/msvideo', 'video/x-ms-wmv', 'application/x-shockwave-flash', 'application/octet-stream');
	$acceptedaudio = array('audio/wav', 'audio/mpeg3', 'audio/aiff', 'audio/mpeg');
	$accepteddocuments = array('doc', 'docx', 'rtf', 'txt');
	//Do not edit below
	$idx = 0;
	$pro = 0;
	if($_GET['idx'] == '3')
	{
		$_GET['project'] = 'Final_Project';
	}
	function ucname($string)
	{
		$string = strtolower($string);
		foreach (array('_', ' ', '=5F', '=5f', '=20') as $delimiter)
		{
			if (strpos($string, $delimiter)!==false)
			{
				$string = implode('_', array_map("ucfirst", explode($delimiter, $string)));
			}
		}
		return $string;
	}
	function udname($string)
	{
		$string = strtolower($string);
		foreach (array('_', ' ', '=5F', '=5f', '=20') as $delimiter)
		{
			if (strpos($string, $delimiter)!==false)
			{
				$string = implode(' ', array_map("ucfirst", explode($delimiter, $string)));
			}
		}
		return $string;
	}
	function setMemoryForImage($filename)
	{
		$imageInfo = getimagesize($filename);
		$imageInfo['mime'] = strtolower($imageInfo['mime']);
		if($imageInfo['mime'] == "image/jpeg" || $imageInfo['mime'] == "image/gif")
			$memoryNeeded = round(($imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $imageInfo['channels'] / 8 + Pow(2, 16)) * 1.65);
		else if($imageInfo['mime'] == "image/png")
			$memoryNeeded = round(($imageInfo[0] * $imageInfo[1] * $imageInfo[2] * $imageInfo['bits'] / 8 + Pow(2, 16)) * 1.65);
		else if($imageInfo['mime'] == "image/vnd.wap.wbmp")
			$memoryNeeded = round(($imageInfo[0] * $imageInfo[1] * $imageInfo[2] + Pow(2, 16)) * 1.65);
		else
			return false;
		$memoryLimit = (int) ini_get('memory_limit')*1048576;
		if ((memory_get_usage() + $memoryNeeded) > $memoryLimit)//if do not already have that memory
		{
			ini_set('memory_limit', ceil((memory_get_usage() + $memoryNeeded + $memoryLimit)/1048576).'M');
			return true;
		}
		else
			return true;
	} 
	function thumb($filename, $acceptedimages = array('image/vnd.wap.wbmp', 'image/png', 'image/gif', 'image/jpeg'), $maxsamplewidth = 100, $maxsampleheight = 100, $destintation = "thumb")
	{
		$file = explode('/', $filename);
		$filel = array_pop($file);
		$directory = implode('/', $file);
		$target_path = $directory . "/" . $destintation . "/" . $filel;
		if(!is_dir($directory . "/" . $destintation))//make thumbnail directory if it does not exist
			mkdir($directory . "/" . $destintation, 0755);
		if(!is_file($directory . "/" . $destintation . "/index.php"))//make index.php if it does not exist
		{
			$handle = fopen($directory . "/" . $destintation . "/index.php", 'w');
			fwrite($handle, "<?php\n");
			fwrite($handle, "header('Location: ..');\n");
			fwrite($handle, "?>");
			fclose($handle);
		}
		if(!is_file($target_path) && is_file($filename))//checks if thumb is not created and if there is an actual file
		{
			$allow = false;
			$imageInfo = getimagesize($filename);
			$imageInfo['mime'] = strtolower($imageInfo['mime']);
			foreach ($acceptedimages as $allowedimages)//check if legal image type
			{
				if ($imageInfo['mime'] == $allowedimages)
				{
					$allow = true;
					break;
				}
			}
			if($allow)
			{
				setMemoryForImage($filename);//find required memory to make thumbnails
				$width = $imageInfo[0];
				$height = $imageInfo[1];
				if($width > 0 && $height > 0)//finds legal widths and heights
				{
					if($maxsamplewidth / $width < $maxsampleheight / $height)
					{
						$x = $maxsamplewidth / $width;
						$wide = $width * $x;
						$high = $height * $x;
					}
					else if($maxsamplewidth / $width > $maxsampleheight / $height)
					{
						$x = $maxsampleheight / $height;
						$wide = $width * $x;
						$high = $height * $x;
					}
					else
					{
						$x = $maxsamplewidth / $width;
						$wide = $width * $x;
						$high = $height * $x;
					}
				}
				$type = 0;
				if($imageInfo['mime'] == "image/jpeg")//loads in image
				{
					$type = 1;
					$source=imagecreatefromjpeg($filename);
				}
				else if($imageInfo['mime'] == "image/png")
				{
					$type = 2;
					$source=imagecreatefrompng($filename);
				}
				else if($imageInfo['mime'] == "image/gif")
				{
					$type = 3;
					$source=imagecreatefromgif($filename);
				}
				else if($imageInfo['mime'] == "image/vnd.wap.wbmp")
				{
					$type = 4;
					$source=imagecreatefromwbmp($filename);
				}
				if($type == 0)
					return false;
				$thumb = imagecreatetruecolor(floor($wide), floor($high));//creates thumbnail image
				imagecopyresized($thumb, $source, 0, 0, 0, 0, $wide, $high, $width, $height);//resizes thumbnail image
				$result = true;
				if($type == 1)
				{
					if(!imagejpeg($thumb, $target_path))//save thumbnail image
						$result = false;
				}
				else if($type == 2)
				{
					if(!imagepng($thumb, $target_path))//save thumbnail image
						$result = false;
				}
				else if($type == 3)
				{
					if(!imagegif($thumb, $target_path))//save thumbnail image
						$result = false;
				}
				else if($type == 4)
				{
					if(!imagewbmp($thumb, $target_path))//save thumbnail image
						$result = false;
				}
				ImageDestroy($thumb);
				ImageDestroy($source);
				ini_set("memory_limit","4M");//reset memory limit
				return $result;
			}
			else
				return false;
		}
		else if(is_file($target_path))
			return true;
		else
			return false;
	}
	//Images Page
	if (isset($_GET['image']) && isset($_GET['project']))
	{
		$_GET['project'] = ucfirst(ucname($_GET['project']));
		if(is_dir($_GET['project']) && ($_GET['project'] != '.' && $_GET['project'] != '..' && $_GET['project'] != 'images' && $_GET['project'] != 'papers'))//Makes sure directory is legal
		{
			if(is_file($_GET['project'] . "/" . $_GET['image']) && $_GET['image'] != 'index.php')//makes sure image is legal
			{
				echo "<h1>" . udname($_GET['project']) . "</h1><br><br>";
				$pro = 1;
				$image = explode('.', $_GET['image']);
				array_pop($image);
				$imagename = implode('.', $image);
				$allow = 0;
				$imageInfo = getimagesize($_GET['project'] . "/" . $_GET['image']);
				$imageInfo['mime'] = strtolower($imageInfo['mime']);
				foreach ($acceptedimages as $allowedimages)//check if is image
				{
					if ($imageInfo['mime'] == $allowedimages)
					{
						$allow = 1;
						$width = $imageInfo[0];
						if($width > $maxwidth)
						{
							echo "<a href='" . $_GET['project'] . "/" . $_GET['image'] . "'><img width='" . $maxwidth . "' src='" . $_GET['project'] . "/" . $_GET['image'] . "'></img></a><br>";
						}
						else
						{
							echo "<a href='" . $_GET['project'] . "/" . $_GET['image'] . "'><img src='" . $_GET['project'] . "/" . $_GET['image'] . "'></img></a><br>";
						}
						break;
					}
				}
				if(!$allow)
				{
					$f = escapeshellarg($_GET['project'] . "/" . $_GET['image']);
					$imagetype = strtolower(trim( `file -bi $f` ));
					foreach ($acceptedvideos as $allowedvideos)//check if is video
					{
						if ($imagetype == $allowedvideos)
						{
							$allow = 2;
							echo "<embed width='690' height='500' src='" . $_GET['project'] . "/" . $_GET['image'] . "'></embed><br>";
							if(is_dir($_GET['project'] . "/documents"))
							{
								if(!is_file($_GET['project'] . "/documents/index.php"))//make index.php if it does not exist
								{
									$handle = fopen($_GET['project'] . "/documents/index.php", 'w');
									fwrite($handle, "<?php\n");
									fwrite($handle, "header('Location: ..');\n");
									fwrite($handle, "?>");
									fclose($handle);
								}
								if(is_file($_GET['project'] . "/documents/" . $imagename . ".txt"))
								{
									include($_GET['project'] . "/documents/" . $imagename . ".txt");
								}
								echo "<br>";
							}
							else
								echo "<br>";
							echo "<center><a href='" . $_GET['project'] . "/" . $_GET['image'] . "'>&#60;Download&#62;</a></center>";
							break;
						}
					}
				}
				if(!allow)//if is something else
					echo "<a href='" . $_GET['project'] . "/" . $_GET['image'] . "'>" . $_GET['image'] . "</a><br>";
				if(is_dir($_GET['project'] . "/documents"))
				{
					if(!is_file($_GET['project'] . "/documents/index.php"))//make index.php if it does not exist
					{
						$handle = fopen($_GET['project'] . "/documents/index.php", 'w');
						fwrite($handle, "<?php\n");
						fwrite($handle, "header('Location: ..');\n");
						fwrite($handle, "?>");
						fclose($handle);
					}
					if(is_file($_GET['project'] . "/documents/" . $imagename . ".txt") && !($allow == 2))
					{
						include($_GET['project'] . "/documents/" . $imagename . ".txt");
						echo "<br>";
					}
				}
				echo "<center><a href='?project=" . $_GET['project'] . "&page=1'>&#60;Back&#62;</a></center><br>";
			}
		}
	}
	//Projects Page
	if (isset($_GET['project']) && !$pro)
	{
		$_GET['project'] = ucfirst(ucname($_GET['project']));
		if(is_dir($_GET['project']) && ($_GET['project'] != '.' && $_GET['project'] != '..' && $_GET['project'] != 'images' && $_GET['project'] != 'papers'))//Makes sure directory is legal
		{
			if(!is_file($_GET['project'] . "/index.php"))//make index.php if it does not exist
			{
				$handle = fopen($_GET['project'] . "/index.php", 'w');
				fwrite($handle, "<?php\n");
				fwrite($handle, "header('Location: ..');\n");
				fwrite($handle, "?>");
				fclose($handle);
			}
			if(!is_numeric($_GET['page']))//Makes sure page # is legal
				$_GET['page'] = 1;
			else if ($_GET['page'] < 1)
				$_GET['page'] = 1;
			$projects = array();
			$projects = scandir($_GET['project']);
			$count = 0;
			$allow = false;
			foreach($projects as $var)//Get the count of valid documents
				if(is_file($_GET['project'] . "/" . $var) && $var != 'index.php')
					$count++;
			$_GET['page'] = floor((int)$_GET['page']);
			$maxpages = ceil($count / 6);
			if($_GET['page'] > $maxpages)//Makes sure page # is legal
				$_GET['page'] = $maxpages;
			$lowend = ($_GET['page'] - 1) * 6;
			$highend = ($_GET['page'] - 1) * 6 + 5;
			if($highend > $count)
				$diff = $count - $lowend;
			else
				$diff = 6;
			echo "<h1>" . udname($_GET['project']) . "</h1><br><br>";
			if($count > 0)
			{
				if($diff != 1)
					echo '<table width="' . $maxwidth . '" border="0" align="center" cellpadding="0" cellspacing="0"><tr><td><center>';
				foreach($projects as $var)  
				{
					if($var != '.' && $var != '..' && $var != 'index.php' && is_file($_GET['project'] . "/" . $var))//checks for valid files
					{
						if($lowend <= $idx && $idx <= $highend)
						{
							$allow = 0;
							$imageInfo = getimagesize($_GET['project'] . "/" . $var);
							$imageInfo['mime'] = strtolower($imageInfo['mime']);
							foreach ($acceptedimages as $allowedimages)//check if legal image type
							{
								if ($imageInfo['mime'] == $allowedimages)
								{
									$allow = 1;
									if(!thumb($_GET['project'] . "/" . $var, $acceptedimages, $maxsamplewidth, $maxsampleheight))//creates a thumbnail
										$allow = 3;
									break;
								}
							}
							if($allow == 0)
							{
								$f = escapeshellarg($_GET['project'] . "/" . $var);
								$imagetype = strtolower(trim( `file -bi $f` ));
								foreach ($acceptedvideos as $allowedvideos)//check if legal video type
								{
									if ($imagetype == $allowedvideos)
									{
										$allow = 2;
										break;
									}
								}
							}
							if($diff != 1)
								if(($diff >= 5 && ($idx % 3) == 0) || ($diff >= 3 && $diff < 5 && ($idx % 2) == 0))
									echo "</center></td></tr><tr><td><center>";
								else if($idx != 0)
									echo "</center></td><td><center>";
							if($displaydates)
								echo date('n/j/y g:i:s A', filectime($_GET['project'] . "/" . $var)) . "<br>";
							if($allow == 1)
							{
								echo "<a href='?project=" . $_GET['project'] . "&image=" . $var . "'><img src='" . $_GET['project'] . "/thumb/" . $var . "'></img></a><br>";
								echo "<a href='?project=" . $_GET['project'] . "&image=" . $var . "'>" . $var . "</a><br>";
							}
							else if($allow == 2)
							{
								echo "<a href='?project=" . $_GET['project'] . "&image=" . $var . "'>" . $var . "</a><br>";
							}
							else
							{
								echo "<a href='" . $_GET['project'] . "/" . $var . "'>" . $var . "</a><br>";
							}
							$idx++;
						}
						else if($idx > $highend)
							break;
						else
							$idx++;
					}
				}
				if($diff != 1)
					echo "</center></td></tr></table>";
				if($maxpages > 1)//Makes page progression
				{
					echo "<center>";
					if($_GET['page'] > 1)
						echo "<a href='?project=" . $_GET['project'] . "&page=" . ($_GET['page'] - 1) . "'>&#60;&#60;Last Page</a>";
					if($_GET['page'] - 5 >= 1)
						echo " <a href='?project=" . $_GET['project'] . "&page=" . ($_GET['page'] - 5) . "'>" . ($_GET['page'] - 5) . "</a>";
					if($_GET['page'] - 4 >= 1)
						echo " <a href='?project=" . $_GET['project'] . "&page=" . ($_GET['page'] - 4) . "'>" . ($_GET['page'] - 4) . "</a>";
					if($_GET['page'] - 3 >= 1)
						echo " <a href='?project=" . $_GET['project'] . "&page=" . ($_GET['page'] - 3) . "'>" . ($_GET['page'] - 3) . "</a>";
					if($_GET['page'] - 2 >= 1)
						echo " <a href='?project=" . $_GET['project'] . "&page=" . ($_GET['page'] - 2) . "'>" . ($_GET['page'] - 2) . "</a>";
					if($_GET['page'] - 1 >= 1)
						echo " <a href='?project=" . $_GET['project'] . "&page=" . ($_GET['page'] - 1) . "'>" . ($_GET['page'] - 1) . "</a>";
					echo " " . $_GET['page'];
					if($_GET['page'] + 1 <= $maxpages)
						echo " <a href='?project=" . $_GET['project'] . "&page=" . ($_GET['page'] + 1) . "'>" . ($_GET['page'] + 1) . "</a>";
					if($_GET['page'] + 2 <= $maxpages)
						echo " <a href='?project=" . $_GET['project'] . "&page=" . ($_GET['page'] + 2) . "'>" . ($_GET['page'] + 2) . "</a>";
					if($_GET['page'] + 3 <= $maxpages)
						echo " <a href='?project=" . $_GET['project'] . "&page=" . ($_GET['page'] + 3) . "'>" . ($_GET['page'] + 3) . "</a>";
					if($_GET['page'] + 4 <= $maxpages)
						echo " <a href='?project=" . $_GET['project'] . "&page=" . ($_GET['page'] + 4) . "'>" . ($_GET['page'] + 4) . "</a>";
					if($_GET['page'] + 5 <= $maxpages)
						echo " <a href='?project=" . $_GET['project'] . "&page=" . ($_GET['page'] + 5) . "'>" . ($_GET['page'] + 5) . "</a>";
					if($_GET['page'] < $maxpages)
						echo " <a href='?project=" . $_GET['project'] . "&page=" . ($_GET['page'] + 1) . "'>Next Page&#62;&#62;</a>";
					echo "</center>";
				}
			}
			else
			{
				echo "There is currently nothing in this directory";
				$idx = 1;
			}
			echo "<br><center><a href='?idx=1'>&#60;Back&#62;</a></center><br>";
		}
	}
	if (is_numeric($_GET['idx']) && !$idx && !$pro)//index
	{
		$idx = 1;
		switch ($_GET['idx'])
		{
			case '1':
				//Projects
				echo "<h1>Projects</h1><br><br>";
				if($displaydates)
					echo "<table>";
				$projects = scandir('.');
				foreach($projects as $var)  
				{
					if($var != '.' && $var != '..' && is_dir(ucfirst(ucname($var))))
					{
						if($displaydates)
							echo "<tr><td><a href='?project=" . ucfirst(ucname($var)) . "&page=1'>" . ucfirst(udname($var)) . "</a></td><td>" . date('n/j/y g:i:s A', filectime($var)) . "</td></tr>";
						else
							echo "<a href='?project=" . ucfirst(ucname($var)) . "&page=1'>" . ucfirst(udname($var)) . "</a><br>";
					}
				}
				if($displaydates)
					echo "</table>";
				echo "<center><a href='?idx=0'>&#60;Back&#62;</a></center><br>";
				break;
			case '2':
				echo "<h1>Papers</h1><br><br>";
				$projects = scandir('papers');
				foreach($projects as $var)  
				{
					if($var != '.' && $var != '..' && $var != 'index.php' && is_file('papers/' . $var))
					{
						echo "<a href='papers/" . $var . "'>" . ucfirst(udname($var)) . "</a><br>";
					}
				}
				echo "<br><center><a href='?idx=0'>&#60;Back&#62;</a></center><br>";
				break;
			case '3':
				echo "<h1>Latest Project</h1><br><br>";
				$projects = scandir('.');
				foreach($projects as $var)  
				{
					if($var != '.' && $var != '..' && is_dir(ucfirst(ucname($var))))
					{
						if(!isset($newestproject))
							$newestproject = $var;
						if(filemtime($newestproject) - filemtime($var) < 0)
							$newestproject = $var;
					}
				}
				echo $newestproject;
				echo "<br><center><a href='?idx=0'>&#60;Back&#62;</a></center><br>";
				break;
			case '4':
				echo "<h1>About Me</h1><br>";
				include "about_me.txt";
				echo "<br><center><a href='?idx=0'>&#60;Back&#62;</a></center><br>";
				break;
			default:
				$idx = 0;
				break;
		}
	}
	if(!$idx && !$pro)//homepage
	{
		echo "<h1>Home</h1><br>";
		include "home.txt";
	}
}
else
{
	echo '<script type="text/javascript">
		<!--
		window.location = "index.php"
		//--></script>';
}
//LATEST IMAGE  filemtime;
//latest change filectime;
?>