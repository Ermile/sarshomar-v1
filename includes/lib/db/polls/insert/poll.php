<?php 
namespace lib\db\polls\insert;
use \lib\debug;

trait poll
{
	protected static function insert_poll()
	{
		// insert poll arguments
		$insert_poll = [];

		if(isset(self::$args['options']['survey_id']))
		{
			$insert_poll['parent_id'] = \lib\utility\shortURL::decode(self::$args['options']['survey_id']);
			$insert_poll['type']      = "survey";
		}

		if(isset(self::$args['title']))
		{	
			$insert_poll['post_title'] = self::$args['title'];
	
			if(strlen(self::$args['title']) > 190)
			{
				return debug::error(T_("Poll title must be less than 190 character"), 'title', 'arguments');
			}
		}
		
		if(!self::$update_mod && !self::$args['title'])
		{
			$insert_poll['post_title'] = '~';
		}

		// get content
		if(isset(self::$args['options']['description']))
		{
			$insert_poll['post_content'] = self::$args['options']['description'];
		}

		if(isset(self::$args['options']['summary']))
		{
			$insert_poll['post_meta']['desc'] = self::$args['options']['summary'];
			if(self::$args['options']['summary'] && strlen(self::$args['options']['summary']) > 150)
			{
				return debug::error(T_("Summery must be less than 150 character"), 'summary', 'arguments');
			}
		}

		// save meta range_timing_maxs
		if(isset(self::$args['options']['range_timing_max']))
		{
			if(self::$args['options']['range_timing_max'] && !is_numeric(self::$args['options']['range_timing_max']))
			{
				return debug::error(T_("Invalid arguments range_timing_max"), 'range_timing_max', 'arguments');
			}
			$insert_poll['post_meta']['range_timing_max'] = self::$args['options']['range_timing_max'];
		}

		// save meta choice_count_min
		if(isset(self::$args['options']['choice_count_min']))
		{
			if(self::$args['options']['choice_count_min'] && !is_numeric(self::$args['options']['choice_count_min']))
			{
				return debug::error(T_("Invalid arguments choice_count_min"), 'choice_count_min', 'arguments');
			}
			$insert_poll['post_meta']['choice_count_min'] = self::$args['options']['choice_count_min'];
		}

		// save meta choice_count_max
		if(isset(self::$args['options']['choice_count_max']))
		{
			if(self::$args['options']['choice_count_max'] && !is_numeric(self::$args['options']['choice_count_max']))
			{
				return debug::error(T_("Invalid arguments choice_count_max"), 'choice_count_max', 'arguments');	
			}
			$insert_poll['post_meta']['choice_count_max'] = self::$args['options']['choice_count_max'];
		}

		// save meta random_sort
		if(isset(self::$args['options']['random_sort']))
		{
			if(self::$args['options']['random_sort'])
			{
				$insert_poll['post_meta']['random_sort'] = true;
			}
			else
			{
				$insert_poll['post_meta']['random_sort'] = false;
			}
		}

		// save meta hidden_result
		if(isset(self::$args['options']['hidden_result']))
		{
			if(self::$args['options']['hidden_result'])
			{
				$insert_poll['post_meta']['hidden_result'] = true;
			}
			else
			{
				$insert_poll['post_meta']['hidden_result'] = false;
			}
		}

		// save meta ordering
		if(isset(self::$args['options']['ordering']))
		{
			if(self::$args['options']['ordering'])
			{
				$insert_poll['post_meta']['ordering'] = true;
			}
			else
			{
				$insert_poll['post_meta']['ordering'] = false;
			}
		}

		// save meta start_date
		if(isset(self::$args['options']['start_date']))
		{
			if(self::$args['options']['start_date'] && \DateTime::createFromFormat('Y-m-d', self::$args['options']['start_date']) === false)
			{
				return debug::error(T_("Invalid arguments start_date"), 'start_date', 'arguments');
			}
			$insert_poll['post_meta']['start_date'] = self::$args['options']['start_date'];
		}

		// save meta end_date
		if(isset(self::$args['options']['end_date']))
		{
			if(self::$args['options']['end_date'] &&  \DateTime::createFromFormat('Y-m-d', self::$args['options']['end_date']) === false)
			{
				return debug::error(T_("Invalid arguments end_date"), 'end_date', 'arguments');
			}
			$insert_poll['post_meta']['end_date'] = self::$args['options']['end_date'];
		}

		// save meta article
		if(isset(self::$args['options']['article']))
		{
			if(self::$args['options']['article'] && !preg_match("/^[". self::$args['shortURL']. "]+$/", self::$args['options']['article']))
			{
				return debug::error(T_("Invalid arguments article"), 'article', 'arguments');
			}
			$insert_poll['post_meta']['article'] = self::$args['options']['article'];
		}

		/**
		 * upload files of poll title
		 */
		// save meta file
		if(isset(self::$args['options']['file']))
		{
			// remove attachment from this post
			if(!self::$args['options']['file'])
			{
				$insert_poll['post_meta']['attachment_id'] = '';
			}
			else
			{	
				// upload new file
				$upload_name_path = 'upload_name';

				if(substr(self::$args['options']['file'], 0, 7) == 'http://' ||
					substr(self::$args['options']['file'], 0, 8) == 'https://'
				)
				{
					$upload_name_path = 'file_path';
				}

				$upload_args =
				[
					'user_id'         => self::$args['user'],
					$upload_name_path => self::$args['options']['file']
				];

				$file_title = \lib\utility\upload::upload($upload_args);
				
				if(\lib\debug::get_msg("result"))
				{
					$insert_poll['post_meta']['attachment_id'] = \lib\debug::get_msg("result");
				}
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
			$post_meta                  = $insert_poll['post_meta'];
			$insert_poll['post_meta'] = json_encode($insert_poll['post_meta'], JSON_UNESCAPED_UNICODE);
		}

		// insert filters
		if(isset(self::$args['filters']) && is_array(self::$args['filters']))
		{
			$insert_filters = \lib\utility\postfilters::update(self::$args['filters'], $poll_id);
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
					return debug::error(T_(":max user was found, low  the slide of members ",["max" => $member_exist]));
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