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

	public function view_poll($_args)
	{
		// check login to load option or no
		// check answeret to this poll or no

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
				// get previus link
				// $this->data->previous_url = \lib\db\polls::get_previous_url($this->login("id"), $post_id);

				// get next poll url
				$next_url = \lib\db\polls::get_next_url($this->login("id"));
				// save poll id into session to get in answer
				$_SESSION['last_poll_id']  = $post_id;
				// check next url and this post url to find load opt or no
				// this this url == next url mean the user not answered to this poll and must be load the opt
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
					if(\lib\db\answers::is_answered($this->login("id"), $post_id))
					{
						// this user answered to this poll
						$post['post_meta'] = ['opt' => null];
						$this->data->next_url = $next_url;
					}
					else
					{
						// users load poll from other link
						$_SESSION['last_poll_opt'] = $post['post_meta']['opt'];
					}
				}
			}
			else
			{
				// this user not logined  => remove answers button
				$post['post_meta'] = ['opt' => null];
			}
			// to load post data in html
			$this->data->post = $post;

			/*
			 * get all chart result
			*/
			$chart_mode =
			[
				'result',
				'gender',
				'city',
				'country'
			];
			// load result as chart
			$chart = \lib\db\stat_polls::get_result($post_id, $chart_mode);
			$this->data->chart = $chart;
			// get post similar
			$similar = \lib\db\tags::get_post_similar(null, ['tags' => $post['tags']]);
			$this->data->similar = $similar;
			// get post status to show in html page
			$this->data->status = $post['post_status'];
			// compile meta of this post
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
						switch ($value['option_value']) {
							case "multiple_choice":
								$this->data->multiple_choice = true;
								break;

							case "descriptive":
								$this->data->descriptive = true;
								break;

							case "random_sort":
								$this->data->random_sort = true;
								break;
						}
						break;
					// case "true_answer":
					default:
						$meta[$value['option_key']] =
							[
								'filter' => $value['option_value'],
								'icon'   => self::find_icon($value['option_key'])
							];

						break;
				}
			}
			$this->data->meta = $meta;
		}
		else
		{
			// bad request resived
			// post url not found
			\lib\error::bad("Not found");
		}
	}


	/**
	 * find fontawesome icon
	 *
	 * @param      <type>  $_filter  The filter
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function find_icon($_filter)
	{
		switch ($_filter) {

			case 'gender':
			      $class = "venus-mars";
			      break;

			case 'marrital':
			case 'parental':
			      $class = "users";
			      break;

			case 'exercise':
			case 'devices':
			case 'internetusage':
			      $class = "star";
			      break;

			case 'employment':
			case 'business':
			case 'industry':
			      $class = "rocket";
			      break;

			case 'birthdate':
			case 'range':
			case 'age':
			      $class = "circle-o-notch";
			      break;

			case 'graduation':
			case 'course':
			      $class = "certificate";
			      break;

			case 'countrybirth':
			case 'country':
			case 'provincebirth':
			case 'province':
			case 'birthcity':
			case 'city':
			case 'citybirth':
			case 'language':
			      $class = "map-marker";
			      break;

			default:
				 $class = "star";
				break;
		}

		return $class;
	}

}
?>