<?php
namespace content_api\tag\tools;
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
	public function search($_args)
	{
		$result = [];
		$search = utility::request("search");
		switch (utility::request("type"))
		{
			case 'profile':
			case 'tag':
			case 'cat':
				$result = \lib\db\terms::search($search, ['term_type' => utility::request("type"), 'end_limit' => 10]);
				if(is_array($result))
				{
					foreach ($result as $key => $value)
					{
						if(isset($result[$key]['id']))
						{
							$result[$key]['id'] = utility\shortURL::encode($result[$key]['id']);
						}
					}
				}
				break;

			case 'article':
				$meta       = ['post_type' => 'article'];
				$result     = \lib\db\polls::search($search, $meta);
				$tmp_result = [];
				if(is_array($result))
				{
					foreach ($result as $key => $value)
					{
						$id    = null;
						$title = null;
						$url   = null;

						if(isset($value['id']))
						{
							$id = \lib\utility\shortURL::encode($value['id']);
						}

						if(isset($value['title']))
						{
							$title = $value['title'];
						}

						if(isset($value['url']))
						{
							$url = $value['url'];
						}

						$tmp_result[] = ['id' => $id, 'title' => $title, 'url' => $url];
					}
				}
				$result = $tmp_result;

				break;

			default:
				return debug::error(T_("Type not found"));
				break;
		}
		return $result;
	}
}
?>