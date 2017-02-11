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
			$_args = [$_args];
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

		if($_args['method'] != 'put' && $_args['method'] != 'patch')
		{
			$_args['method'] = 'post';
		}

		/**
		 * update the poll or survey
		 */
		$update = false;

		if($_args['method'] == 'put' || $_args['method'] == 'patch')
		{
			if(utility\shortURL::is(utility::request("id")))
			{
				$update = utility::request("id");
			}
			else
			{
				return debug::error(T_("Invalid parametr id"), 'id', 'arguments');
			}
		}
		elseif(utility::request("id"))
		{
			return debug::error(T_("Can not send parametr id in post mod"), 'id', 'arguments');
		}

		// insert args
		$args                         = [];
		$args['update']               = $update;
		$args['debug']                = $this->debug;
		$args['user']                 = $this->user_id;
		$args['method']               = $_args['method'];
		$args['shortURL']             = \lib\utility\shortURL::ALPHABET;
		$args['permission_sarshomar'] = $this->access('u', 'sarshomar_knowledge', 'add');
		$args['permission_profile']   = $this->access('u', 'complete_profile', 'admin');
		$args['title']                = utility::request("title");
		$args['answers']              = utility::request("answers");
		$args['survey']               = utility::request('survey');
		$args['status']               = utility::request('status');
		$args['summary']              = utility::request('summary');
		$args['description']          = utility::request('description');
		$args['language']             = utility::request('language');
		$args['file']                 = utility::request('file');
		$args['tree']                 = utility::request('tree');
		$args['options']              = utility::request("options");
		$args['brand']                = utility::request("brand");
		$args['from']                 = utility::request("from");
		$args['schedule']             = utility::request('schedule');
		$args['hidden_result']        = utility::request('hidden_result');
		$args['articles']             = utility::request('articles');
		$args['tags']                 = utility::request('tags');
		$args['cat']                  = utility::request('cat');

		// $args['comment']           = utility::request('comment');
		// $args['slug']              = utility::request('slug');
		// $args['type']              = utility::request("type");

		$result = \lib\db\polls::create($args);
		return $result;
	}
}
?>