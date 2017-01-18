<?php
namespace content_u\add;
use \lib\utility;
use \lib\debug;

class model extends \content_u\home\model
{
	use filter\model;
	use publish\model;

	public function get_edit($_args)
	{

	}


	public function post_add($_args)
	{
		if($this->term_list())
		{
			return;
		}
	}

	public function post_edit($_args)
	{
		if($this->term_list())
		{
			return;
		}
	}


	private function term_list()
	{
		if(utility::post("list"))
		{
			$result = [];

			$search = utility::post("q");

			switch (utility::post("list"))
			{
				case 'profile':
				case 'tag':
				case 'cat':
					$result = \lib\db\terms::search($search, ['term_type' => utility::post("list"), 'end_limit' => 10]);
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
					return deubg::error(T_("Type not found"));
					break;
			}

			debug::msg("list", $result);
			return true;
		}
		return false;
	}

}
?>