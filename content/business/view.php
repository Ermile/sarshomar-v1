<?php
namespace content\business;

class view extends \mvc\view
{
	function config()
	{
		// $this->include->css_ermile   = false;
		$this->include->js         = false;
		$this->include->js_main    = false;
		$this->include->chart      = false;
		// $this->include->css_ermile = false;
		$this->include->css        = false;

	}
}
?>