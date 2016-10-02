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
		$list = \lib\db\polls::get();
		$this->data->list = $list;
	}


	public function view_poll($_args)
	{
		// check login to load option or no
		// check answeret to this poll or no
		$post = $this->model()->get_posts();

		if(isset($post['id']))
		{
			if($this->login())
			{
				// save poll id into session to get in answer
				$_SESSION['last_poll_id']  = $post['id'];

				$next_url = \lib\db\polls::get_next_url($this->login("id"));

				if(isset($_args->get("url")[0][0]) && $_args->get("url")[0][0] == $next_url)
				{
					if(isset($post['post_meta']['opt']))
					{
						$_SESSION['last_poll_opt'] = $post['post_meta']['opt'];
					}
				}
				else
				{
					// check this user answerd to this poll or no
					if(\lib\db\answers::is_answered($this->login("id"), $post['id']))
					{
						// this user answered to this poll
						$post['post_meta'] = ['opt' => null];
						$this->data->next_url = $next_url;
					}
				}
			}
			else
			{
				// this user not logined  => remove answers button
				$post['post_meta'] = ['opt' => null];
			}

			$this->data->post = $post;

			$post_id = $post['id'];
			$result  = \lib\db\stat_polls::get_result($post_id);
			$result['data'] = json_encode($result['data'], JSON_UNESCAPED_UNICODE);
			$this->data->chart = $result;
			$this->data->chart_type = 'column';

			$this->data->filter = \lib\db\filters::get_poll_filter($post['id']);
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