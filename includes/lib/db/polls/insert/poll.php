<?php 
namespace lib\db\polls\insert;
use \lib\debug;

trait poll
{
	protected static function insert_poll()
	{
		// insert poll arguments
		$insert_poll = [];

		// check and insert poll title
		if(isset(self::$args['title']))
		{	
			$insert_poll['post_title'] = trim(self::$args['title']);
			
			if(strlen(self::$args['title']) > 190)
			{
				return debug::error(T_("Poll title must be less than 190 character"), 'title', 'arguments');
			}
		}
		
		// post slug
		if(isset(self::$args['options']['slug']))
		{	
			$slug = \lib\utility\filter::slug(self::$args['options']['slug']);

			$insert_poll['post_slug'] = substr($slug, 0 , 50);

			if(self::$update_mod)
			{
				$new_url = '$/'. \lib\utility\shortURL::encode(self::$poll_id);
				$new_url .= ($slug) ? '/'. $slug : null;
				$insert_poll['post_url'] = $new_url;
			}

			if(strlen(self::$args['options']['slug']) > 50)
			{
				return debug::error(T_("Poll slug must be less than 50 character"), 'slug', 'arguments');
			}
		}

		// if poll title is null set ~ 
		if(!self::$update_mod && !self::$args['title'])
		{
			$insert_poll['post_title'] = '~';
		}

		// check surver id
		if(isset(self::$args['options']['survey_id']))
		{
			if(self::$args['options']['survey_id'])
			{
				if(!preg_match("/^[". self::$args['shortURL']. "]+$/", self::$args['options']['survey_id']))
				{
					return debug::error(T_("Invalid parametr survey_id"), 'survey_id', 'arguments');
				}

				$poll_parent_id = \lib\utility\shortURL::decode(self::$args['options']['survey_id']);
				$poll_parent_id = \lib\db\polls::get_poll($poll_parent_id);

				if(!$poll_parent_id)
				{
					return debug::error(T_("Survey id not found"), 'survey_id', 'arguments');
				}

				if(!isset($poll_parent_id['type']) || (isset($poll_parent_id['type']) && $poll_parent_id['type'] != 'survey'))
				{
					return debug::error(T_("Survey id must be a survey record"), 'survey_id', 'arguments');
				}

				if(!isset($poll_parent_id['user_id']) || (isset($poll_parent_id['user_id']) && $poll_parent_id['user_id'] != self::$user_id))
				{
					return debug::error(T_("This is not your survey"), 'survey_id', 'arguments');
				}
				$insert_poll['post_survey'] = self::$args['options']['survey_id'];
			}	
			else
			{
				$insert_poll['post_survey'] = null;
			}
		}

		// get content
		if(isset(self::$args['options']['description']))
		{
			$insert_poll['post_content'] = trim(self::$args['options']['description']);
		}

		// summary 
		if(isset(self::$args['options']['summary']))
		{
			$insert_poll['post_meta']['summary'] = trim(self::$args['options']['summary']);
			if(self::$args['options']['summary'] && strlen(self::$args['options']['summary']) > 150)
			{
				return debug::error(T_("Summery must be less than 150 character"), 'summary', 'arguments');
			}
		}

		// get the insert id by check sarshomar permission
		// when not in upldate mode
		if(!self::$update_mod && self::$args['permission_sarshomar'] === true)
		{
			$next_id   = (int) \lib\db\polls::sarshomar_id();
			$insert_poll['id'] = ++$next_id;
		}

		$insert_poll['user_id'] =  self::$args['user'];

		if(isset(self::$args['options']['language']))
		{
			// check language
			if(self::$args['options']['language'] && !\lib\utility\location\languages::check(self::$args['options']['language']))
			{
				return \lib\debug::error(T_("Invalid parametr language"), 'language', 'arguments');
			}
			$insert_poll['post_language'] = self::$args['options']['language'];
		}

		if(!self::$update_mod)
		{	
			$insert_poll['post_sarshomar'] = self::$args['permission_sarshomar'] === true ? 1 : 0;
			$insert_poll['post_privacy']   = 'private';
		}

		$change_tree = false;
		$tree_args   = false;

		// tree
		if(isset(self::$args['options']['tree']['parent_id']))
		{
			$change_tree = true;

			$parent_id = self::$args['options']['tree']['parent_id'];

			if($parent_id && !preg_match("/^[". self::$args['shortURL']. "]+$/", $parent_id))
			{
				return debug::error(T_("Invalid parametr tree:parent_id"), 'parent_id', 'arguments');
			}

			if($parent_id)
			{
				$loc_id  = \lib\utility\shortURL::decode($parent_id);
				$loc_opt = self::$args['options']['tree']['answers'];
				if(!is_array($loc_opt))
				{
					$loc_opt = [$loc_opt];
				}
				
				foreach ($loc_opt as $key => $value)
				{
					if(!is_numeric($value))
					{
						return debug::error(T_("Invalid parametr tree:answers (:value)", ['value' => $value]), 'answers', 'arguments');
					}
				}

				$tree_args            = [];
				$tree_args['user_id'] = self::$args['user'];
				$tree_args['parent']  = $loc_id;
				$tree_args['opt']     = $loc_opt;	
			}
		}

		if(isset(self::$args['options']['comment']))
		{
			if(self::$args['options']['comment'])
			{
				$insert_poll['post_comment'] = 'open';
			}
			else
			{
				$insert_poll['post_comment'] = 'closed';
			}			
		}

		if(isset(self::$args['type']))
		{
			if(self::$args['type'] == 'poll' || self::$args['type'] == 'survey')
			{
				$insert_poll['post_type'] = self::$args['type'];
			}
			elseif(self::$args['type'])
			{
				return debug::error(T_("Invalid parametr type"), 'type', 'arguments');
			}
		}

		if(self::$publish_mod)
		{
			$insert_poll['post_status'] = "publish";
		}
		else
		{
			$insert_poll['post_status'] = "draft";
		}

		$post_meta = [];

		if(isset($insert_poll['post_meta']) && !empty($insert_poll['post_meta']))
		{
			$post_meta                = $insert_poll['post_meta'];
			$insert_poll['post_meta'] = json_encode($insert_poll['post_meta'], JSON_UNESCAPED_UNICODE);
		}

		// insert filters
		if(isset(self::$args['filters']) && is_array(self::$args['filters']))
		{
			$insert_filters = \lib\utility\postfilters::update(self::$args['filters'], self::$poll_id);
			/**
			 * set ranks
			 * plus (int) member in member field
			 */
			if(isset(self::$args['filters']['max_person']))
			{	

				$member       = (int) self::$args['filters']['max_person'];
				$member_exist = (int) \lib\db\users::get_count("awaiting");
				if($member <= $member_exist)
				{
					\lib\db\ranks::plus($poll_id, "member", $member, ['replace' => true]);
				}
				else
				{
					return debug::error(T_(":max user was found, low  the slide of members ",["max" => $member_exist]), 'max_person', 'arguments');
				}

				if(self::$args['filters']['max_person'])
				{
					$insert_poll['post_privacy'] = 'public';
				}
				else
				{
					$insert_poll['post_privacy'] = 'private';
				}
			}
		}

		// inset poll if we not in update mode
		if(!self::$update_mod)
		{
			self::$poll_id = self::insert($insert_poll);
		}
		else
		{
			$old_post_meta            = \lib\db\polls::get_poll_meta(self::$poll_id);
			$post_meta                = array_merge($old_post_meta, $post_meta);
			$insert_poll['post_meta'] = json_encode($post_meta, JSON_UNESCAPED_UNICODE);
			if(isset($insert_poll['id']))
			{
				unset($insert_poll['id']);
			}
			self::update($insert_poll, self::$poll_id);
		}

		if($change_tree)
		{
			\lib\utility\poll_tree::remove(self::$poll_id);
			if($tree_args)
			{
				$tree_args['child'] = self::$poll_id;
				\lib\utility\poll_tree::set($tree_args);
			}
		}
	}
}
?>