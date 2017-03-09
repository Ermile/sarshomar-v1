<?php
namespace content_api\v1\poll\tools;
use \lib\utility;
use \lib\debug;

trait add
{
	/**
	 * add a post
	 *
	 * @param      <type>   $_args     The arguments
	 * @param      array    $_options  The options
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function poll_add($_args = [])
	{
		if(!debug::$status)
		{
			return false;
		}

		if(!is_array($_args))
		{
			$_args = [];
		}

		if(utility::request() == '' || is_null(utility::request()))
		{
			return debug::error(T_("Invalid input"), 'input', 'arguments');
		}

		$default_args =
		[
			'args'   => [],
			'method' => 'post'
		];

		$_args = array_merge($default_args, $_args);

		if($_args['method'] != 'put' && $_args['method'] != 'patch' && $_args['method'] != 'add_opt_file_site')
		{
			$_args['method'] = 'post';
		}

		/**
		 * update the poll or survey
		 */
		$update = false;

		if($_args['method'] == 'put' || $_args['method'] == 'patch' || $_args['method'] == 'add_opt_file_site')
		{
			if(utility\shortURL::is(utility::request("id")))
			{
				$update = utility::request("id");
			}
			else
			{
				return debug::error(T_("Invalid id parameter"), 'id', 'arguments');
			}
		}
		elseif(utility::request("id"))
		{
			return debug::error(T_("Can not send parameter id in post mode"), 'id', 'arguments');
		}

		$args = [];
		if(utility::isset_request('access_profile'))
		{
			$access_profile = utility::request('access_profile');
			if(!is_array($access_profile))
			{
				$access_profile = explode(" ", $access_profile);
			}
			$profile_values = array_keys(\lib\utility\profiles::profile_data());
			$profile_values[] = 'displayname';
			$diff = array_diff($access_profile, $profile_values);
			if(!empty($diff))
			{
				return debug::error(T_("Profile values is incorrect") . " ('" . implode($diff, "', '") . "')", 'access_profile', 'arguments');
			}
			$args['access_profile'] = $access_profile;
		}
		// insert args
		$args['update']   = $update;
		$args['debug']    = $this->debug;
		$args['user']     = $this->user_id;
		$args['method']   = $_args['method'];
		$args['shortURL'] = \lib\utility\shortURL::ALPHABET;

		// $args['permission_sarshomar']                     = $this->access('u', 'sarshomar_knowledge', 'add');
		// $args['permission_profile']                       = $this->access('u', 'complete_profile', 'admin');
		//
		if(utility::isset_request('title'))
		{
			$args['title'] = utility::request("title");
		}

		if(utility::isset_request('answers'))
		{
			$args['answers'] = utility::request("answers");
		}

		if(utility::isset_request('survey'))
		{
			$args['survey'] = utility::request('survey');
		}

		if(utility::isset_request('status'))
		{
			$args['status'] = utility::request('status');
		}

		if(utility::isset_request('summary'))
		{
			$args['summary'] = utility::request('summary');
		}

		if(utility::isset_request('description'))
		{
			$args['description'] = utility::request('description');
		}

		if(utility::isset_request('language'))
		{
			$args['language'] = utility::request('language');
		}

		if(utility::isset_request('file'))
		{
			$args['file'] = utility::request('file');
		}

		if(utility::isset_request('tree'))
		{
			$args['tree'] = utility::request('tree');
		}

		if(utility::isset_request('options'))
		{
			$args['options'] = utility::request("options");
		}

		if(utility::isset_request('brand'))
		{
			$args['brand'] = utility::request("brand");
		}

		if(utility::isset_request('from'))
		{
			$args['from'] = utility::request("from");
		}

		if(utility::isset_request('schedule'))
		{
			$args['schedule'] = utility::request('schedule');
		}

		if(utility::isset_request('hidden_result'))
		{
			$args['hidden_result'] = utility::request('hidden_result');
		}

		if(utility::isset_request('articles'))
		{
			$args['articles'] = utility::request('articles');
		}

		if(utility::isset_request('tags'))
		{
			$args['tags'] = utility::request('tags');
		}

		if(utility::isset_request('cat'))
		{
			$args['cat'] = utility::request('cat');
		}

		// $args['comment'] = utility::request('comment');
		// $args['slug']    = utility::request('slug');
		// $args['type']    = utility::request("type");

		$result = \lib\db\polls::create($args);
		return $result;
	}
}
?>