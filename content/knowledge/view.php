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

	public function check_url($_args)
	{
		if(isset($_args->match->url[0]) && is_array($_args->match->url[0]))
		{
			if(isset($_args->match->url[0][2]))
			{
				if(!isset($_args->match->url[0][3]) || !isset($_args->match->url[0][4]))
				{
					$url = $_args->match->url[0][2];
					$url = \lib\utility\shortURL::decode($url);
					$url = \lib\db\polls::get_poll_url($url);
					$this->redirector()->set_url($url)->redirect();
				}
				else
				{
					return $_args->match->url[0][0];
				}
			}
		}
	}

	public function view_poll($_args)
	{
		// check login to load option or no
		// check answeret to this poll or no

		$corrent_url =  $this->check_url($_args);

		$post = $this->model()->get_posts();

		if(isset($post['id']))
		{
			if($this->login())
			{
				$this->data->previous_url = \lib\db\polls::get_previous_url($this->login("id"), $corrent_url);
				$next_url = \lib\db\polls::get_next_url($this->login("id"));
				// save poll id into session to get in answer
				$_SESSION['last_poll_id']  = $post['id'];

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
					else
					{
						$_SESSION['last_poll_opt'] = $post['post_meta']['opt'];
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

			/*
			 * get all chart result
			*/
			$chart = \lib\db\stat_polls::get_result($post_id, "*");

			$this->data->chart = $chart;

			$this->data->filter = \lib\db\filters::get_poll_filter($post['id']);

			// get article of this poll
			$article =
			[
				'post_id'    => $post['id'],
				'option_cat' => "poll_". $post['id'],
				'option_key' => "article",
				'limit'      => 1
			];
			$option_record = \lib\db\options::get($article);
			if(isset($option_record[0]['id']))
			{
				$this->data->article = \lib\db\polls::xget(['id' => $option_record[0]['value'], 'post_type' => 'article']);
			}

			$similar = \lib\db\tags::get_post_similar(['tags' => $post['tags']]);

			$this->data->similar = $similar;

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