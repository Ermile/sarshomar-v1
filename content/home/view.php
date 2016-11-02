<?php
namespace content\home;

class view extends \mvc\view
{
	function config()
	{
		// \lib\db\profiles::get_count("gender", "marrital");
		// $this->include->css_ermile   = false;
		$this->include->js    = true;
		$this->include->chart = true;
		if($this->module() === 'home')
		{
			$this->include->js_main      = true;
		}
		else
		{
			$this->data->page['title'] = T_($this->data->module);
		}

		// get #homepage post by random function
		$this->data->result = $this->model()->random_result();
		// get total sarshomart answered
		$total = \lib\db\stat_polls::get_sarshomar_total_answered();
		$this->data->stat = T_(":number Questions answered", ["number"=> $total]);
		$this->include->fontawesome = true;
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


	function view_tags()
	{

	}
}
?>