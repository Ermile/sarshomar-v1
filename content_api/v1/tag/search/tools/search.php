<?php
namespace content_api\v1\tag\search\tools;
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
	public function tag_search($_args = null)
	{
		debug::title(T_("Search faild"));

		$result = [];
		$search = utility::request("search");
		switch (utility::request("type"))
		{
			case 'profile':
			case 'cat':
			case 'tag':

				$meta = [];

				if(utility::request("type") == 'tag')
				{
					$meta['term_type'] = 'sarshomar%';
				}
				else
				{
					$meta['term_type'] = 'sarshomar';
				}

				$meta['end_limit'] = 10;
				if(utility::request("parent"))
				{
					if(utility\shortURL::is_shortURL(utility::request("parent")))
					{
						$meta['parent'] = utility\shortURL::decode(utility::request("parent"));
					}
					else
					{
						debug::error(T_("Invalid parameter parent"), 'parent', 'arguments');
						return;
					}
				}
				$result = \lib\db\terms::search($search, $meta);

				if(is_array($result))
				{
					foreach ($result as $key => $value)
					{
						if(isset($value['id']))
						{
							$result[$key]['value'] = utility\shortURL::encode($value['id']);
							unset($result[$key]['id']);
						}

						if(isset($value['parent']))
						{
							unset($result[$key]['parent']);
						}
					}
				}
				break;

			// case 'article':
			// 	$meta       = ['post_type' => 'article'];
			// 	$result     = \lib\db\polls::search($search, $meta);
			// 	$tmp_result = [];
			// 	if(is_array($result))
			// 	{
			// 		foreach ($result as $key => $value)
			// 		{
			// 			$id    = null;
			// 			$title = null;
			// 			$url   = null;

			// 			if(isset($value['id']))
			// 			{
			// 				$id = utility\shortURL::encode($value['id']);
			// 			}

			// 			if(isset($value['title']))
			// 			{
			// 				$title = $value['title'];
			// 			}

			// 			if(isset($value['url']))
			// 			{
			// 				$url = $value['url'];
			// 			}

			// 			$tmp_result[] = ['id' => $id, 'title' => $title, 'url' => $url];
			// 		}
			// 	}
			// 	$result = $tmp_result;

			// 	break;

			default:
				return debug::error(T_("Type not found"), 'type', 'arguments');
				break;
		}
		debug::title(T_("Search successfuly"));
		return $result;
	}
}
?>