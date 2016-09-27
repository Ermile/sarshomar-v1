<?php
namespace content\home;

class view extends \mvc\view
{
	function config()
	{
		// $this->include->css_ermile   = false;
		$this->include->js    = true;
		$this->include->chart = true;
		if($this->module() === 'home')
		{
			$this->include->js_main      = true;
		}
		// $this->data->chart      = \lib\db\polls::getResult(3, 'count', 'txt');
		$post = $this->model()->get_posts();
		if(isset($post['id']))
		{
			$post_id = $post['id'];
			$this->data->chart      = \lib\db\stat_polls::get_result($post_id);
			$this->data->chart_type = 'column';

		}
		$this->data->result = $this->model()->random_result();
		$this->data->stat = T_(":number Questions answered", ["number"=>12]);

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
}
?>