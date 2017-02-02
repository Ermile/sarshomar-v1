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
				if(true)
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

			case 'trash':
				$can_change_to =
				[
					'draft',
					'delete',
				];

			default:
				$can_change_to = [];
				break;
		}
		return ['current' => $current_status, 'available' => $can_change_to];
	}
}
?>