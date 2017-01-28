<?php
namespace content_api\v1\poll\tools;
use \lib\utility;
use \lib\debug;

trait delete
{
	/**
	 * add a post
	 *
	 * @param      <type>   $_args     The arguments
	 * @param      array    $_options  The options
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function poll_delete($_args = null)
	{

		if(!debug::$status)
		{
			return false;
		}

		if(utility::request() == '' || is_null(utility::request()))
		{
			return debug::error(T_("Invalid input"), 'input', 'arguments');
		}

		if(!utility::request("id"))
		{
			return debug::error(T_("Invalid parametr id"), 'id', 'arguments');
		}

		$id = \lib\utility\shortURL::decode(utility::request("id"));
		$poll = \lib\db\polls::get_poll($id);
		if(!$poll)
		{
			return debug::error(T_("Poll not found"), 'id', 'arguments');
		}

		if(isset($poll['user_id']))
		{
			if((int) $this->user_id !== (int) $poll['user_id'])
			{
				return debug::error(T_("Can not access to delete this poll"), 'id', 'permission');
			}
		}
		else
		{
			return debug::error(T_("Poll user not found"), 'user_id', 'system');
		}

		if(isset($poll['status']))
		{
			switch ($poll['status'])
			{
				case 'draft':
				case 'publish':
				case 'awaiting':

					$delete = \lib\db\polls::update(['post_status' => 'deleted'], $poll['id']);
					if($delete)
					{
						return debug::true(T_("Poll deleted"));
					}
					else
					{
						return debug::error(T_("Error in deleting poll"), 'id', 'system');
					}
					break;

				case 'deleted':
					return debug::error(T_("This poll already deleted"), 'id');
					break;

				default:
					return debug::error(T_("Can not access to delete this poll"), 'id', 'permission');
					break;
			}
		}
	}
}
?>