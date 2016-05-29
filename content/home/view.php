<?php
namespace content\home;

class view extends \mvc\view
{
	function config()
	{
		// $this->include->css_ermile   = false;
		$this->include->js    = false;
		$this->include->chart = true;
		if($this->module() === 'home')
		{
			$this->include->js_main      = false;
		}
		$this->data->chart      = \lib\utility\visitor::chart();
		$this->data->chart_type = 'column';
	}


	/**
	 * [pushState description]
	 * @return [type] [description]
	 */
	function pushState()
	{
		if($this->module() !== 'home')
		{
			$this->data->display['mvc']     = "content/home/layout-xhr.html";
		}
	}
}
?>