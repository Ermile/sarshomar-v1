<?php
namespace content\poll;

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

		if(isset($post['id']))
		{
			// set post_id
			$post_id = $post['id'];
			// check login for load opt
			if($this->login())
			{
				// save poll id into session to get in answer
				$_SESSION['last_poll_id']  = $post_id;
				// check next url and this post url to find load opt or no

				// check this user answerd to this poll or no
				if(\lib\db\answers::is_answered($this->login("id"), $post_id))
				{
					// this user answered to this poll
					$post['post_meta'] = ['opt' => null];
				}
				else
				{
					// users load poll from other link
					$_SESSION['last_poll_opt'] = $post['post_meta']['opt'];
				}
			}
			else
			{
				// this user not logined  => remove answers button
				$post['post_meta'] = ['opt' => null];
			}

			// get post status to show in html page
			$this->data->status = $post['post_status'];
			// compile meta of this post

			if(isset($post['meta']))
			{
			$meta = [];
				foreach ($post['meta'] as $key => $value) {
					switch ($value['option_key']) {
						// ignore opt_1, opt_2, ...
						case substr($value['option_key'], 0,3) == "opt":
							continue;
							break;

						// show article
						case "article":
							$this->data->article = \lib\db\polls::xget(['id' => $value['option_value'], 'post_type' => 'article']);
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

			// load poll filters
			if(isset($post['filter_id']) && $post['filter_id'])
			{
				$filters = \lib\db\filters::get($post['filter_id']);
				$filters = array_filter($filters);
				unset($filters['id']);
				unset($filters['unique']);

				$show_filters = [];
				foreach ($filters as $key => $value)
				{
					$show_filters[] =
					[
						'filter' => $value,
						'icon'   => self::filter_icon($key)
					];
				}
				$this->data->filters = $show_filters;
			}
			$show_result = true;

			// check show result
			if($show_result)
			{
				/*
				 * get all chart result
				*/
				$chart_mode =
				[
					'result',
					'gender',
					'marrital',
					'range',
					'degree',
					'city'
				];
				// load result as chart
				$chart = \lib\db\stat_polls::get_result($post_id, $chart_mode);
				$this->data->chart = $chart;
			}

			// comment
			if(isset($post['post_comment']) && $post['post_comment'] == 'closed')
			{
				$thid->data->comment = false;
			}

			// to load post data in html
			$this->data->post = $post;
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