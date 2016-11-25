<?
class ERROR
{
	private $error;
	private $errorlist;
	private $privateerrorlist;
	
	$this->errorlist = array
	(
		'Get_User' 			=> array(
							1 => "Invalid Username or Password",
							2 => "Invalid Username"
							),
		'Get_User_Session' 	=> array(
							1 => "Expired Session",
							2 => "Bad IP",
							3 => "Session no longer exists",
							4 => "Invalid  Session",
							5 => "User does not exist"
							),
		'Set_Session' 		=> array(
							1 => "Invalid Username or Password",
							2 => "Invalid Username"
							),
		'Remove_Session' 	=> array(
							1 => "Bad IP",
							2 => "Session no longer exists",
							3 => "Invalid  Session"
							),
		'Create_User'		=> array(
							1 => "Username is already in use",
							2 => "Invalid Username - Valid Chartacters Are Uppercase, lowercase, numbers, spaces, -, _, ., and +",
							3 => "Invalid Email - Please Use a valid email"
							)
	);
	$this->privateerrorlist = array
	(
		'Get_User' 			=> array(
							2 => "JQuery Work Around"
							),
		'Get_User_Session' 	=> array(
							2 => "Possible IP Hijack",
							4 => "Fake Session",
							),
		'Set_Session' 		=> array(
							1 => "Invalid Username or Password",
							2 => "Invalid Username"
							),
		'Remove_Session' 	=> array(
							1 => "Possible IP Hijack",
							3 => "Fake Session"
							),
		'Create_User'		=> array(
							1 => "Username is already in use",
							2 => "JQuery Work Around",
							3 => "JQuery Work Around or does not match PHP email Validation"
							)
	);
	public function __construct($error = "logs/error.php")
	{
		if(is_ file($error) && is_writable($error))
		{
			$this->error = fopen($error, "a");
		}
		else
		{
			$this->error = false;
		}
	}
	private privateoutput($type, $number, $elements = array())
	{
		if($this->error != false)
		{
			$error = "[" . date("l F j, Y g:i:s A", gmmktime()) . "][" . $_SERVER['REMOTE_ADDR'] . "] - " . $type . ": " . $number . " - " . $this->errorlist[$type][$number] . "\r\n";
			fwrite($this->error, $error);
		}
	}
	private publicoutput($type, $number)
	{
		echo $this->errorlist[$type][$number];
	}
	public error($type, $number, $elements = array())
	{
		if(array_key_exists($type, $this->errorlist))
		{
			if(array_key_exists($number, $this->errorlist[$type]))
			{
				publicoutput($type, $number);
			}
		}
		if(array_key_exists($type, $this->privateerrorlist))
		{
			if(array_key_exists($number, $this->privateerrorlist[$type]))
			{
				privateoutput($type, $number, $elements);
			}
		}
	}
	public function __destruct()
	{
		fclose($this->error);
	}
}
?>