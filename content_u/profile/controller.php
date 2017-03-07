<?php
namespace content_u\profile;

class controller extends  \content_u\home\controller
{
	public function _route()
	{
		if(\lib\utility::get("bugfix_terms_slug") == 'we_are_godzilla')
		{
			$run = false;
			if(\lib\utility::get("run") == 'run')
			{
				$run = true;
			}

			echo '<pre>';
			$term_list = \lib\db::get("SELECT * FROM terms WHERE terms.term_type = 'sarshomar_tag' ");
			foreach ($term_list as $key => $value)
			{
				if(
					isset($value['id']) &&
					is_numeric($value['id']) &&
					isset($value['term_title'])
				  )
				{
					$id = $value['id'];
					var_dump($value['term_title']);
					$term_slug = \lib\utility\filter::slug($value['term_title'], null, 'persian');
					var_dump($term_slug);
					$term_url = '$/tag/'. $term_slug;
					$query = "UPDATE terms SET term_slug = '$term_slug', term_url = '$term_url' WHERE id = $id LIMIT 1";
					var_dump($query);
					if($run)
					{
						\lib\db::query($query);
					}
				}
			}
			// var_dump($term_list);

			exit();
		}

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