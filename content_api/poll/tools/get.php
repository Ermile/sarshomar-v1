<?php 
namespace content_api\poll\tools;

trait get
{
	/**
	 * Gets the poll.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     array   The poll.
	 */
	public function get_poll($_args)
	{
		$result  = [];
		$poll_id = $this->check_poll_url($_args);
		$poll    = \lib\db\polls::get_poll($poll_id);
				
		if(isset($poll['id']))
		{
			$poll['id'] = \lib\utility\shortURL::encode($poll['id']);
		}

		if(isset($poll['user_id']))
		{
			$poll['user_id'] = \lib\utility\shortURL::encode($poll['user_id']);
		}

		if(isset($poll['parent']))
		{
			$poll['parent'] = \lib\utility\shortURL::encode($poll['parent']);
		}

		if(isset($poll['survey']))
		{
			$poll['survey'] = \lib\utility\shortURL::encode($poll['survey']);
		}


		if(isset($poll['sarshomar']) && $poll['sarshomar'])
		{
			$poll['sarshomar'] = "yes";
		}
		else
		{
			$poll['sarshomar'] = "no";
		}

		if(isset($poll['type']))
		{
			$poll['type'] = \lib\db\polls::set_html_type($poll['type']);
		}

		$result['poll'] = $poll;
		$answers = \lib\db\pollopts::get($poll_id);

		$result['answers'] = $answers;

		$result['poll_tree_opt']   = null;
		$result['poll_tree_id']    = null;
		$result['poll_tree_title'] = null;
		if(isset($poll['parent']) && $poll['parent'] !== null)
		{
			$poll_tree = \lib\utility\poll_tree::get($poll['id']);

			if($poll_tree && is_array($poll_tree))
			{
				$opt = array_column($poll_tree, 'value');
				$result['poll_tree_opt'] = is_array($opt) ? join($opt, ',') : null;
				$result['poll_tree_id'] = \lib\utility\shortURL::encode($poll['parent']);
				$result['poll_tree_title'] = \lib\db\polls::get_poll_title($poll['parent']);
			}
		}
		$filters = \lib\utility\postfilters::get_filter($poll_id);
		$result['filters'] = $filters;
		$result['filters']['member'] = \lib\db\ranks::get($poll_id, 'member');
		var_dump($result);
		exit();
		return $result;
	}
}

?>