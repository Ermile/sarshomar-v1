<?php
namespace content\knowledge;

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


	public function view_all()
	{

	}


	public function view_poll($_args)
	{
		$post = $this->model()->get_posts();
		if(isset($post['id']))
		{
			$post_id = $post['id'];
			$this->data->post = $post;
			$this->data->chart      = \lib\db\stat_polls::get_result($post_id);
			$this->data->chart_type = 'column';
		}
		else
		{
			\lib\error::bad("Not found");
		}
	}

}
?>