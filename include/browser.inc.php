<?php

/**
 * Class to detect which browser is currently accessing the page/site
 * @author Paul Scott
 * This class is very loosely based on scripts by Gary White
 * @copyright Paul Scott
 * @package browser
 */

class browser 
{
	/**
	 * @var string $name
	 */
	var $name = NULL;
	
	/**
	 * @var string $version
	 */
	var $version = NULL;
	
	/**
	 * @var $useragent
	 */
	var $useragent = NULL;
	
	/**
	 * @var string $platform
	 */
	var $platform;
	
	/**
	 * @var string aol
	 */
	var $aol = FALSE;
	
	/**
	 * @var string browser
	 */
	var $browsertype;
	
	/**
	 * Class constructor
	 * @param void
	 * @return void
	 */
	function browser()
	{
		$agent = $_SERVER['HTTP_USER_AGENT'];
		//set the useragent property
		$this->useragent = $agent;
	}
	
	/**
	 * Method to get the browser details from the USER_AGENT string in 
	 * the PHP superglobals
	 * @param void
	 * @return string property platform 
	 */
	function getBrowserOS()
	{
		$win = preg_match("win", $this->useragent);
		$linux = preg_match("linux", $this->useragent);
		$mac = preg_match("mac", $this->useragent);
		$os2 = preg_match("OS/2", $this->useragent);
		$beos = preg_match("BeOS", $this->useragent);
		
		//now do the check as to which matches and return it
		if($win)
		{
			$this->platform = "Windows";
		}
		elseif ($linux)
		{
			$this->platform = "Linux"; 
		}
		elseif ($mac)
		{
			$this->platform = "Macintosh"; 
		}
		elseif ($os2)
		{
			$this->platform = "OS/2"; 
		}
		elseif ($beos)
		{
			$this->platform = "BeOS"; 
		}
		return $this->platform;
	}
	
	/**
	 * Method to check for FireFox
	 * @param void
	 * @return bool false on failure
	 */ 
	function isFirefox()
	{
		if(preg_match("/Firefox/", $this->useragent))
		{
			$this->browsertype = "Firefox"; 
			$val = stristr($this->useragent, "Firefox");
			$val = explode("/",$val);
			$this->version = $val[1];
			return true;
		}
		else {
			return FALSE;
		}
	}
	
	/**
	 * Method to tie them all up and output something useful
	 * @param void
	 * @return array
	 */
	function whatBrowser()
	{
		$this->getBrowserOS();
		$this->isFirefox();
		return array('browsertype' => $this->browsertype, 
					 'version' => $this->version, 
					 'platform' => $this->platform, 
					 'AOL' => $this->aol); 
	}
}//end class
?>