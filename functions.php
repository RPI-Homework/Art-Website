<?
//Finds memory size needed to take in an image
//$filename is a location of the file
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
//Creates a thumbnail for a file
//$filename is a location of the file
//$maxsamplewidth and $maxsampleheight and max height and width of thumb, default is 100 for both
//$destintation is directory to put new file, if null, places in orginal directory, default is directory /thumb/
//$name is the new name of the file (includes extention), default and null make file same name
//$acceptedimages is an array of accepted image types, can only do wbmp, png, gif, and jpeg
function thumb($filename, $maxsamplewidth = 100, $maxsampleheight = 100, $destintation = "thumb", $name = null, $acceptedimages = array('image/vnd.wap.wbmp', 'image/png', 'image/gif', 'image/jpeg'))
{
	$file = explode('/', $filename);
	$filel = array_pop($file);
	$directory = implode('/', $file);
	if(!is_null($name))
		$file1 = $name;
	if(!is_null($destintation))
	{
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
	}
	else
		$target_path = $directory . "/" . $filel;
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
//finds if a string is in binary format
function isbinary($string = '')
{
	$length = strlen($string);
	if($length = 0)
		return false;
	foreach(str_split($string) as $s)
	{
		switch($s)
		{
			case '0':
			case '1':
				break;
			default:
				return false;
				break;
		}
	}
	return true;
}
//finds if a string is in hexdecimal format
function ishex($string = '')
{
	$length = strlen($string);
	if($length = 0)
		return false;
	foreach(str_split($string) as $s)
	{
		switch($s)
		{
			case '0':
			case '1':
			case '2':
			case '3':
			case '4':
			case '5':
			case '6':
			case '7':
			case '8':
			case '9':
			case 'a':
			case 'A':
			case 'b':
			case 'B':
			case 'c':
			case 'C':
			case 'd':
			case 'D':
			case 'e':
			case 'E':
			case 'f':
			case 'F':
				break;
			default:
				return false;
				break;
		}
	}
	return true;
}
//converts a string of binary digits to hexdecimal
function bintohex($string = '0000', $caps = false)
{
	$length = strlen($string);
	if($length = 0)
		return false;
	switch($length % 4)
	{
		case 1:
			$string = '000' . $string;
		case 2:
			$string = '00' . $string;
		case 3:
			$string = '0' . $string;
	}
	$hexdata = "";
	foreach(str_split($string, 4) as $s)
	{
		switch($s)
		{
			case '0000':
				$hexdata .= '0';
				break;
			case '0001':
				$hexdata .= '1';
				break;
			case '0010':
				$hexdata .= '2';
				break;
			case '0011':
				$hexdata .= '3';
				break;
			case '0100':
				$hexdata .= '4';
				break;
			case '0101':
				$hexdata .= '5';
				break;
			case '0110':
				$hexdata .= '6';
				break;
			case '0111':
				$hexdata .= '7';
				break;
			case '1000':
				$hexdata .= '8';
				break;
			case '1001':
				$hexdata .= '9';
				break;
			case '1010':
				if($caps)
					$hexdata .= 'A';
				else
					$hexdata .= 'a';
				break;
			case '1011':
				if($caps)
					$hexdata .= 'B';
				else
					$hexdata .= 'b';
				break;
			case '1100':
				if($caps)
					$hexdata .= 'C';
				else
					$hexdata .= 'c';
				break;
			case '1101':
				if($caps)
					$hexdata .= 'D';
				else
					$hexdata .= 'd';
				break;
			case '1110':
				if($caps)
					$hexdata .= 'E';
				else
					$hexdata .= 'e';
				break;
			case '1111':
				if($caps)
					$hexdata .= 'F';
				else
					$hexdata .= 'f';
				break;
			default:
				return false;
				break;
		}
	}
	return $hexdata;
}
//converts a string of hexdecimal digits to binary
function hextobin($string = '0')
{
	$length = strlen($string);
	if($length = 0)
		return false;
	$bindata="";
	foreach(str_split($string) as $s)
	{
		switch($s)
		{
			case'0':
				$bindata .= '0000';
				break;
			case'1':
				$bindata .= '0001';
				break;
			case'2':
				$bindata .= '0010';
				break;
			case'3':
				$bindata .= '0011';
				break;
			case'4':
				$bindata .= '0100';
				break;
			case'5':
				$bindata .= '0101';
				break;
			case'6':
				$bindata .= '0110';
				break;
			case'7':
				$bindata .= '0111';
				break;
			case'8':
				$bindata .= '1000';
				break;
			case'9':
				$bindata .= '1001';
				break;
			case'a':
			case'A':
				$bindata .= '1010';
				break;
			case'b':
			case'B':
				$bindata .= '1011';
				break;
			case'c':
			case'C':
				$bindata .= '1100';
				break;
			case'd':
			case'D':
				$bindata .= '1101';
				break;
			case'e':
			case'E':
				$bindata .= '1110';
				break;
			case'f':
			case'F':
				$bindata .= '1111';
				break;
			default:
				return false;
				break;
		}
	}
	return $bindata;
}
?>