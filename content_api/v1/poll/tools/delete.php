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
	public function poll_delete($_options = [])
	{

		if(!debug::$status)
		{
			return false;
		}

		$default_options =
		[
			'id' => null
		];
		$_options = array_merge($default_options, $_options);

		if(!$_options['id'])
		{
			return debug::error(T_("Invalid parametr id"), 'id', 'arguments');
		}

		$_options['id'] = \lib\utility\shortURL::decode($_options['id']);

		$poll = \lib\db\polls::get_poll($_options['id']);

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

		debug::title(T_("Can not delete poll"));

		if(isset($poll['status']))
		{
			switch ($poll['status'])
			{
				case 'draft':
				case 'awaiting':
				case 'pause':

					$delete = \lib\db\polls::update(['post_status' => 'deleted'], $poll['id']);
					if($delete)
					{
						return debug::title(T_("Poll deleted"));
					}
					else
					{
						return debug::error(T_("Error in deleting poll"), 'id', 'system');
					}
					break;

				case 'deleted':
					return debug::error(T_("The poll has already been deleted"), 'id');
					break;

				default:
					return debug::error(T_("To delete a question the status of it must be one of these: Draft, Awaiting and Puase"), 'id', 'permission');
					break;
			}
		}
	}
}
?>