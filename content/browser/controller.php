<?php
namespace content\browser;
use \lib\saloos;

class controller extends \mvc\controller
{
	function _route()
	{
		if($this->url('child') == null)
		{
			$this->get(false, false)->ALL("browser");
			// $this->route_check_true = true;
			echo "<pre>";
			print_r(\lib\utility\browserDetection::browser_detection('full_assoc'));
			echo "</pre>";
			exit();
		}

	}
}
?>