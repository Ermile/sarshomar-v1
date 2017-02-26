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

				if(utility::request('type') == 'profile')
				{
					$meta['order'] = "DESC";
				}

				$meta['end_limit'] = 10;
				if(utility::request("parent"))
				{
					if(utility\shortURL::is(utility::request("parent")))
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
				$result = \lib\utility\filter::meta_decode($result);

				if(is_array($result))
				{
					foreach ($result as $key => $value)
					{
						if(isset($value['id']))
						{
							$result[$key]['value'] = utility\shortURL::encode($value['id']);
							unset($result[$key]['id']);
						}

						if(isset($value['title']) )
						{
							$myTitle = $result[$key]['title'];
							if($myTitle != T_($myTitle))
							{
								$result[$key]['title'] = $myTitle . " | ". T_($myTitle);
							}
						}

						$translate = [];
						if(isset($value['meta']))
						{
							if(isset($value['meta']['translate']) && is_array($value['meta']['translate']))
							{
								foreach ($value['meta']['translate'] as $lang => $trans)
								{
									if(is_string($lang) && is_string($trans))
									{
										// show only current language url
										if($lang == 'fa')
										{
											$translate[] = "<div class='rtl'><b>$lang</b>: " . $trans. "</div>";
										}
										else
										{
											$translate[] = "<div class='ltr'><b>$lang</b>: " . $trans. "</div>";
										}
									}
								}
							}
						}
						unset($result[$key]['parent']);
						unset($result[$key]['meta']);
						$result[$key]['translate'] = implode("", $translate);
					}
				}
				break;

			default:
				return debug::error(T_("Type not found"), 'type', 'arguments');
				break;
		}

		debug::title(T_("Search successfuly"));
		return $result;
	}
}
?>