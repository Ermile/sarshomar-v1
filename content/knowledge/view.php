<?php
namespace content\knowledge;

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


		$this->data->stat = T_(":number Questions answered", ["number"=>\lib\db\stat_polls::get_sarshomar_total_answered()]);
		$this->include->fontawesome = true;
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
			$result  = \lib\db\stat_polls::get_result($post_id);
			$result['data'] = json_encode($result['data'], JSON_UNESCAPED_UNICODE);
			$this->data->chart = $result;
			$this->data->chart_type = 'column';
		}
		else
		{
			\lib\error::bad("Not found");
		}
	}

}
?>