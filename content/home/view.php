<?php
namespace content\home;

class view extends \mvc\view
{
	function config()
	{
		if($this->module() === 'home')
		{
			$this->include->js_main      = true;
		}
		else
		{
			$this->data->page['title'] = $this->data->module;
		}

		// get #homepage post by random function
		$this->data->result = $this->model()->random_result();
		$this->data->male_female_chart = $this->model()->male_female_chart();
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