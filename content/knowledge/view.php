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
		// check login to load option or no
		// check answeret to this poll or no

		$post = $this->model()->get_posts();

		if(isset($post['id']))
		{
			// save poll id into session to get in answer
			$_SESSION['last_poll_id']  = $post['id'];

			if(isset($post['post_meta']['opt']))
			{
				$_SESSION['last_poll_opt'] = $post['post_meta']['opt'];
			}

			// $x = array_column($_SESSION['last_poll_opt'], 'key');
			// $x[3] = '1';
			// var_dump($x);
			// var_dump(array_search('opt_1', $x));
			// var_dump($_SESSION['last_poll_opt']);exit();
			$this->data->post = $post;

			$post_id = $post['id'];
			$result  = \lib\db\stat_polls::get_result($post_id);
			$result['data'] = json_encode($result['data'], JSON_UNESCAPED_UNICODE);
			$this->data->chart = $result;
			$this->data->chart_type = 'column';
		}
		else
		{
			// bad request resived
			// post url not found
			\lib\error::bad("Not found");
		}
	}

}
?>