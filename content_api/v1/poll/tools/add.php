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
				return debug::error(T_("Invalid id parameter"), 'id', 'arguments');
			}
		}
		elseif(utility::request("id"))
		{
			return debug::error(T_("Can not send parameter id in post mode"), 'id', 'arguments');
		}

		// insert args
		$args                                             = [];
		$args['update']                                   = $update;
		$args['debug']                                    = $this->debug;
		$args['user']                                     = $this->user_id;
		$args['method']                                   = $_args['method'];
		$args['shortURL']                                 = \lib\utility\shortURL::ALPHABET;

		$args['permission_sarshomar']                     = $this->access('u', 'sarshomar_knowledge', 'add');
		$args['permission_profile']                       = $this->access('u', 'complete_profile', 'admin');
		$args['permission']                               = [];
		$args['permission']['free_account']               = $this->access('u', 'free_account', 'view');
		$args['permission']['free_add_poll']              = $this->access('u', 'free_add_poll', 'view');
		$args['permission']['free_add_brand']             = $this->access('u', 'free_add_brand', 'view');
		$args['permission']['free_add_filter']            = $this->access('u', 'free_add_filter', 'view');
		$args['permission']['free_add_member']            = $this->access('u', 'free_add_member', 'view');
		$args['permission']['lock_edit_mobile']           = $this->access('u', 'lock_edit_mobile', 'view');
		$args['permission']['lock_edit_user_details']     = $this->access('u', 'lock_edit_user_details', 'view');
		$args['permission']['lock_edit_username']         = $this->access('u', 'lock_edit_username', 'view');
		$args['permission']['sarshomar']                  = $this->access('u', 'sarshomar', 'view');
		$args['permission']['add_poll_cats']              = $this->access('u', 'add_poll_cats', 'view');
		$args['permission']['add_poll_article']           = $this->access('u', 'add_poll_article', 'view');
		$args['permission']['add_max_tags']               = $this->access('u', 'add_max_tags', 'view');
		$args['permission']['max_update_answer']          = $this->access('u', 'max_update_answer', 'view');
		$args['permission']['draft_poll_10']              = $this->access('u', 'draft_poll_10', 'view');
		$args['permission']['draft_poll_50']              = $this->access('u', 'draft_poll_50', 'view');
		$args['permission']['draft_poll_500']             = $this->access('u', 'draft_poll_500', 'view');
		$args['permission']['draft_poll_max']             = $this->access('u', 'draft_poll_max', 'view');
		$args['permission']['lock_answer_sarshomar_poll'] = $this->access('u', 'lock_answer_sarshomar_poll', 'view');
		$args['permission']['lock_answer_poll']           = $this->access('u', 'lock_answer_poll', 'view');

		$args['title']                                    = utility::request("title");
		$args['answers']                                  = utility::request("answers");
		$args['survey']                                   = utility::request('survey');
		$args['status']                                   = utility::request('status');
		$args['summary']                                  = utility::request('summary');
		$args['description']                              = utility::request('description');
		$args['language']                                 = utility::request('language');
		$args['file']                                     = utility::request('file');
		$args['tree']                                     = utility::request('tree');
		$args['options']                                  = utility::request("options");
		$args['brand']                                    = utility::request("brand");
		$args['from']                                     = utility::request("from");
		$args['schedule']                                 = utility::request('schedule');
		$args['hidden_result']                            = utility::request('hidden_result');
		$args['articles']                                 = utility::request('articles');
		$args['tags']                                     = utility::request('tags');
		$args['cat']                                      = utility::request('cat');

		// $args['comment']                               = utility::request('comment');
		// $args['slug']                                  = utility::request('slug');
		// $args['type']                                  = utility::request("type");

		$result = \lib\db\polls::create($args);
		return $result;
	}
}
?>