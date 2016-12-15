<?php
namespace content\poll;

class view extends \mvc\view
{
	function config()
	{
		// add all template of poll into new file
		$this->data->template['poll']['layout'] = 'content/poll/layout.html';

		if ($this->module() === 'home')
		{
			$this->include->js_main = true;
		}

		$this->data->stat = T_(":number Questions answered", ["number"=>\lib\utility\stat_polls::get_sarshomar_total_answered()]);

		$this->include->js          = true;
		$this->include->chart       = true;
		$this->include->fontawesome = true;
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
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
					$this->redirector()->set_url($this->url('prefix').'/'. $url)->redirect();
				}
				else
				{
					return $_args->match->url[0][0];
				}
			}
		}
	}

	/**
	 * show one poll to answer user
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_poll($_args)
	{
		// get the descriptive from meta of poll
		// for evry poll unset this session
		// to set form database
		unset($_SESSION['profile']);
		unset($_SESSION['descriptive']);
		unset($_SESSION['multiple_choice']);

		// default show all result
		$show_result = true;

		// check the url and redirect to poll url
		// for example redirect $/ewR3  to $/ewR3/poll_title
		$this->check_url($_args);

		$post = $this->model()->get_posts();

		// this poll is not a published poll
		// check some thing
		// 1. the user is a sarshomar_knowlege permission and can see all poll
		// 2. the user has been creat this poll and then can see the poll
		//
		if(!isset($post['id']))
		{
			$poll_id   = $this->data->child;
			$poll_id   = \lib\utility\shortURL::decode($poll_id);

			if(\lib\db\polls::is_my_poll($poll_id, $this->login('id')) || $this->access('u','publish_poll','admin'))
			{
				$new_post = \lib\db\polls::get_poll($poll_id);
				$post = [];
				foreach ($new_post as $key => $value) {
					if($key == 'id' || $key == 'filter_id' || $key == 'user_id' || $key == 'date_modified')
					{
						$key = $key;
					}
					else
					{
						$key = 'post_'. $key;
					}
					$post[$key] = $value;
				}
				$post['postmeta'] = \lib\db\posts::get_post_meta($poll_id);
			}
		}

		if(isset($post['id']))
		{
			// set post_id
			$post_id = $post['id'];

			// save poll id into session to get in answer
			$_SESSION['last_poll_id']  = $post_id;
			// check next url and this post url to find load opt or no

			// users load poll from other link
			if(isset($post['post_meta']['opt']))
			{
				$_SESSION['last_poll_opt'] = $post['post_meta']['opt'];
			}

			// get post status to show in html page
			$this->data->status = $post['post_status'];
			// compile meta of this post

			if(isset($post['post_meta']))
			{
				$meta = [];
				foreach ($post['postmeta'] as $key => $value) {
					switch ($value['option_key']) {
						// ignore opt_1, opt_2, ...
						case substr($value['option_key'], 0,3) == "opt":
							continue;
							break;

						// show article
						case "article":
							$this->data->article = \lib\db\polls::get_poll($value['option_value']);
							break;

						// get start date of publish this poll
						case "date_start":
							$this->data->date_start = $value['option_value'];
							break;

						// get end date of publish this poll
						case "date_end":
							$this->data->date_end = $value['option_value'];
							break;
						case "meta":
							// check the meta of this poll
							switch ($value['option_value']) {
								case "multiple_choice":
									// the people can select multiple choice
									$this->data->multiple_choice = true;
									$_SESSION['multiple_choice'] = true;
									break;

								case "descriptive":
									// load a input to type people the opthr opt
									$this->data->descriptive = true;
									$_SESSION['descriptive'] = true;
									break;

								case "random_sort":
									// suffle the opt if random sort is enable
									if(isset($post['post_meta']['opt']))
									{
										$keys = array_keys($post['post_meta']['opt']);
								        shuffle($keys);
								        foreach($keys as $key)
								        {
								        	$new[$key] = $post['post_meta']['opt'][$key];
								        }
								        $post['post_meta']['opt'] = $new;
									}
									break;

								case "profile":
									// this poll has lucked by profiel field
									// we must be save answer to user profile
									$this->data->profile = true;
									// to save user answer in profile
									$_SESSION['profile'] = true;
									break;

								case "hidden_result":
									$show_result = false;
									break;

								case "update_result":
									$this->data->update_result = true;
									break;
							}
							break;
							// show rate of comments
						case 'comment':
							$rate = [];
							for ($i=1; $i <= 5; $i++) {
								if(isset($value['option_meta']["rate$i"]))
								{
									$rate["rate$i"] = $value['option_meta']["rate$i"]['avg'];
								}
								else
								{
									$rate["rate$i"] = 0;
								}
							}
							$rate['total'] = isset($value['option_meta']['total']['avg']) ? $value['option_meta']['total']['avg']: 0;

							$this->data->rate = $rate;
							break;
						// case "true_answer":
						default:
							// !
							break;
					}
				}
				$this->data->meta = $meta;
			}

			// load post filters
			$post_filter         = \lib\db\filters::get_poll_filter($post['id']);
			$this->data->filters = $post_filter;

			$show_result = true;

			// check show result
			if($show_result)
			{
				/*
				 * get all chart result
				*/
				$chart_mode =
				[
					'gender',
					'marrital',
					'range',
					'degree',
					'city'
				];
				// load result as chart
				$chart = \lib\utility\stat_polls::get_result($post_id,
					['validation' => 'valid', 'filter' => $chart_mode]);
				$this->data->chart = $chart;
			}

			// comment
			if(isset($post['comment']) && $post['comment'] == 'closed')
			{
				$thid->data->comment = false;
			}

			// to load post data in html
			$this->data->post = $post;

			$this->data->is_like = \lib\db\polls::is_like($this->login('id'), $post_id);
		}
		else
		{
			\lib\error::bad("NOT FOUND");
		}
	}


	/**
	 * get all comments of this poll
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_comments($_args)
	{
		$this->data->show_all_comments = true;
		$this->data->all_comments = $_args->api_callback;
	}
}
?>