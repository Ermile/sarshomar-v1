<?php
namespace content_admin\home;

class view extends \mvc\view
{
	function config()
	{
		$this->include->chart = true;
		$this->data->visitors = \lib\utility\visitor::chart();
	}
}
?>