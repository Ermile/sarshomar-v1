<?php
namespace content_api\v1\helpcenter\search\tools;
use \lib\utility;
use \lib\debug;

trait search
{

	/**
	 * search for the first match.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public function search($_args = null)
	{
		$result = [];
		$search = utility::request("search");

		$meta                = [];
		$meta['post_type']   = 'help';
		$meta['post_status'] = 'publish';

		$result = \lib\db\posts::search($search, $meta);
		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				$result[$key] = array_filter($value);
				if(isset($value['id']))
				{
					$result[$key]['id'] = \lib\utility\shortURL::encode($value['id']);
				}
			}
		}
		else
		{
			$result = [$result];
		}
		return $result;
	}
}
?>