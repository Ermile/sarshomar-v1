<?php
namespace content_api\v1\poll\status\tools;
use \lib\utility;
use \lib\debug;

trait get
{

	/**
	 * get pollstatus
	 *
	 * @param      array  $_options  The options
	 */
	public function poll_status()
	{
		$options =
		[
			'get_filter'        => false,
			'get_opts'          => false,
			'get_options'       => false,
			'get_public_result' => false,
			'run_options'       => false,
			'check_is_my_poll'  => true,
		];

		$result = $this->poll_get($options);

		if(!debug::$status)
		{
			return ;
		}

		if(isset($result['status']))
		{
			$current_status = $result['status'];
		}
		else
		{
			return debug::error(T_("Poll status not found"), 'status', 'system');
		}

		$can_change_to = [];

		switch ($current_status)
		{
			case 'draft':
				$can_change_to =
				[
					'publish',
					'trash',
				];
				break;

			case 'publish':
				// not body answer to this poll
				if(!self::answer_one_person(utility\shortURL::decode(utility::request('id'))))
				{
					$can_change_to =
					[
						'draft',
						// 'trash',
						'pause',
						// 'stop',
					];
				}
				else
				{
					$can_change_to =
					[
						'pause',
						// 'stop',
					];
				}
				break;

			case 'pause':
				$can_change_to =
				[
					'publish',
					'stop',
				];
				break;

			case 'stop':
				$can_change_to =
				[
					'publish',
				];
				break;

			case 'awaiting':
				$can_change_to =
				[
					'draft',
					'trash',
				];
				break;

			case 'trash':
				$can_change_to =
				[
					'draft',
					'delete',
				];
				break;

			default:
				$can_change_to = [];
				break;
		}
		return ['current' => $current_status, 'available' => $can_change_to];
	}

	/**
	 * no body answer to this poll
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function answer_one_person($_poll_id)
	{
		$query  = "SELECT polldetails.id AS `count` FROM polldetails WHERE polldetails.post_id = $_poll_id LIMIT 1";
		$result = \lib\db::get($query,'count', true);
		$result = intval($result);
		if($result)
		{
			return true;
		}
		return false;
	}

}
?>