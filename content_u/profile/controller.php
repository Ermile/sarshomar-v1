<?php
namespace content_u\profile;

class controller extends  \content_u\home\controller
{
	public function _route()
	{
		if(\lib\utility::get("import"))
		{

			$file = \lib\utility\file::read(root.'content_u/profile/profile/cat.csv');
			$line = preg_split("/\n/", $file);
			foreach ($line as $key => $value)
			{
				$split = preg_split("/\,/", $value);
				if($key === 0)
				{
					continue;
				}
				$cat1 = null;
				if(isset($split[1]))
				{
					$cat1 = strtolower(trim($split[1]));
				}
				$cat2 = null;
				if(isset($split[2]))
				{
					$cat2 = strtolower(trim($split[2]));
				}
				$cat3 = null;
				if(isset($split[3]))
				{
					$cat3 = strtolower(trim($split[3]));
				}
				$cat4 = null;
				if(isset($split[4]))
				{
					$cat4 = strtolower(trim($split[4]));
				}
				$cat5 = null;
				if(isset($split[5]))
				{
					$cat5 = strtolower(trim($split[5]));
				}
				$cat6 = null;
				if(isset($split[6]))
				{
					$cat6 = strtolower(trim($split[6]));
				}
				$cat7 = null;
				if(isset($split[7]))
				{
					$cat7 = strtolower(trim($split[7]));
				}


			// 	var_dump($split);
			// var_dump($cat6);

			// exit();
			}

		}
		// die(':)');
		parent::check_login();

		$this->get("profile", "profile")->ALL();
		$this->post("profile")->ALL();
	}

	/**
	 * insert terms
	 *
	 * @param      string  $_key    The key
	 * @param      <type>  $_title  The value
	 */
	public static function insert_terms($_caller, $_title , $_meta)
	{
		$parent_id   = null;
		$title_slug  = \lib\utility\filter::slug($_title);
		$key_slug    = \lib\utility\filter::slug($_key);

		$new_term_id = \lib\db\terms::caller($_caller);
		// insrt new terms
		if(!$new_term_id || empty($new_term_id))
		{
			$split_caller = explode(':', $_caller);
			foreach ($split_caller as $key => $value)
			{
				array_pop($split_caller);
				$new_term_id_parent = \lib\db\terms::caller(implode(":", $split_callerk));

				if(!$new_term_id_parent || empty($new_term_id_parent))
				{
					$insert_new_terms_parent =
					[
						'term_type'   => 'users',
						'term_caller' => $key_slug,
						'term_title'  => $_key,
						'term_slug'   => $key_slug,
						'term_url'    => $_key,
						'term_status' => 'awaiting'
					];
					$insert_new_terms_parent = \lib\db\terms::insert($insert_new_terms_parent);
					if($insert_new_terms_parent)
					{
						$parent_id = \lib\db::insert_id();
					}
					else
					{
						return false;
					}
				}
			}
		}
		elseif(isset($new_term_id_parent['id']))
		{
			$parent_id = $new_term_id_parent['id'];
		}
		else
		{
			return false;
		}

		if(!$parent_id)
		{
			return false;
		}

		// new term find we need to save this to terms table
		$term_status = 'awaiting';
		if(isset($_valus_checked_true[$_key]) && $_valus_checked_true[$_key] == $_title)
		{
			$term_status = 'enable';
		}
		// cehc termslug len
		$title_slug = \lib\utility\filter::slug($_title);
		if(mb_strlen($title_slug) > 50)
		{
			$title_slug = substr($title_slug, 0, 49);
		}

		$insert_new_terms =
		[
			'term_type'   => 'users',
			'term_caller' => "$_key:$title_slug",
			'term_title'  => $_title,
			'term_slug'   => $title_slug,
			'term_url'    => $_key. '/'. $title_slug,
			'term_status' => $term_status,
			'term_parent' => $parent_id,
		];

		$new_term_id = \lib\db\terms::insert($insert_new_terms);

		$new_term_id = \lib\db\terms::caller("$_key:$_title");

		if(!$new_term_id)
		{
			return false;
		}

		return $new_term_id;
	}
}

?>