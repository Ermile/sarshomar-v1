<?php
namespace content_u\billing\tools;
use \lib\utility;
use \lib\debug;
trait unit
{

	public $active_units =
	[
		'toman',
		'dollar',
	];


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_bank  The bank
	 */
	public function user_unit()
	{
		$new_unit = utility::post('user-unit');
		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input'   => utility::post(),
				'session' => $_SESSION,
			],
		];

		if($new_unit || $new_unit === '')
		{
			if($new_unit === '')
			{
				\lib\db\logs::set('user:unit:set:empty', $this->user_id, $log_meta);
				debug::error(T_("Please select one units"), 'user-unit', 'arguments');
				return false;
			}

			$current_unit = \lib\db\units::user_unit($this->user_id);
			if($current_unit !== $new_unit)
			{
				if(in_array($new_unit, $this->active_units))
				{
					$log_meta['meta']['old'] = $current_unit;
					$log_meta['meta']['new'] = $new_unit;
					\lib\db\logs::set('user:unit:change', $this->user_id, $log_meta);
					\lib\db\units::set_user_unit($this->user_id, $new_unit);
					debug::true(T_("Your unit has change"));
				}
				else
				{
					\lib\db\logs::set('user:unit:set:invalid:unit', $this->user_id, $log_meta);
					debug::error(T_("Please select a valid units"), 'user-unit', 'arguments');
				}
			}
			else
			{
				\lib\db\logs::set('user:unit:set:duplicate', $this->user_id, $log_meta);
			}
			return false;
		}
		return true;
	}
}
?>