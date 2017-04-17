<?php
namespace content\referer;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{
	public function get_ref($_args)
	{
		if(!$this->login())
		{
			return null;
		}

		$meta =
		[
			'get_count' => true,
			'log_data'  => $this->login('id'),
		];
		$result = [];

		$result['click'] = \lib\db\logs::search(null, array_merge($meta, ['caller' => 'user:ref:set']));
		$result['signup'] = \lib\db\logs::search(null, array_merge($meta, ['caller' => 'user:ref:signup']));
		$result['profile'] = \lib\db\logs::search(null, array_merge($meta, ['caller' => 'user:ref:complete:profile']));
		return $result;
	}
}