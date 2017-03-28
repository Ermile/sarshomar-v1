<?php
namespace lib\db\polls\insert;
use \lib\debug;

trait poll
{

	/**
	 * insert poll data
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	protected static function insert_poll()
	{
		// insert poll arguments
		$insert_poll = [];

		// check and insert poll title
		if(self::isset_args('title'))
		{
			if(mb_strlen(self::$args['title']) > 190)
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:title:max:length', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Poll title must be less than 190 character"), 'title', 'arguments');
			}

			if(isset(self::$args['title']))
			{
				$insert_poll['post_title'] = self::safe_user_string(self::$args['title']);
			}
			else
			{
				$insert_poll['post_title'] = '‌';
			}
		}
		else
		{
			if(self::$method == 'put')
			{
				$insert_poll['post_title'] = '‌';
			}
			elseif(self::$method == 'post')
			{
				$insert_poll['post_title'] = '‌';
			}
		}

		// if(isset(self::$args['slug']))
		// {
		// 	$slug = \lib\utility\filter::slug(self::$args['slug']);

		// 	$insert_poll['post_slug'] = substr($slug, 0 , 50);

		// 	if(self::$update_mod)
		// 	{
		// 		$new_url = '$/'. \lib\utility\shortURL::encode(self::$poll_id);
		// 		$new_url .= ($slug) ? '/'. $slug : null;
		// 		$insert_poll['post_url'] = $new_url;
		// 	}

		// 	if(mb_strlen(self::$args['slug']) > 50)
		// 	{
		// 		return debug::error(T_("Poll slug must be less than 50 character"), 'slug', 'arguments');
		// 	}
		// }

		// if poll title is null set this character >>‌<< this caracter
		// if(!self::$update_mod && !self::$args['title'])
		// {
		// 	$insert_poll['post_title'] = '‌';
		// }

		if(!self::$update_mod)
		{
			$insert_poll['post_slug'] = '‌';
		}

		// check surver id
		if(self::isset_args('survey'))
		{
			if(self::$args['survey'])
			{
				if(!preg_match("/^[". self::$args['shortURL']. "]+$/", self::$args['survey']))
				{
					// \lib\db::rollback();
					\lib\db\logs::set('user:poll:add:error:survey:invalid', self::$args['user'], ['meta' => ['input' => self::$args]]);
					return debug::error(T_("Invalid parametr survey "), 'survey', 'arguments');
				}

				$poll_parent_id = \lib\utility\shortURL::decode(self::$args['survey']);
				$poll_parent_id = \lib\db\polls::get_poll($poll_parent_id);

				if(!$poll_parent_id)
				{
					// \lib\db::rollback();
					\lib\db\logs::set('user:poll:add:error:survey:notfound', self::$args['user'], ['meta' => ['input' => self::$args]]);
					return debug::error(T_("Survey id not found"), 'survey', 'arguments');
				}

				if(!isset($poll_parent_id['type']) || (isset($poll_parent_id['type']) && $poll_parent_id['type'] != 'survey'))
				{
					// \lib\db::rollback();
					\lib\db\logs::set('user:poll:add:error:survey:isnot', self::$args['user'], ['meta' => ['input' => self::$args]]);
					return debug::error(T_("Survey id must be a survey record"), 'survey', 'arguments');
				}

				if(!isset($poll_parent_id['user_id']) || (isset($poll_parent_id['user_id']) && $poll_parent_id['user_id'] != self::$user_id))
				{
					// \lib\db::rollback();
					\lib\db\logs::set('user:poll:add:error:survey:permission', self::$args['user'], ['meta' => ['input' => self::$args]]);
					return debug::error(T_("This is not your survey"), 'survey', 'arguments');
				}

				self::max_survey_child($poll_parent_id);

				$insert_poll['post_survey'] = self::$args['survey'];
			}
			else
			{
				$insert_poll['post_survey'] = null;
			}
		}
		else
		{
			if(self::$method == 'put')
			{
				$insert_poll['post_survey'] = null;
			}
		}

		// get content
		if(self::isset_args('description'))
		{
			$insert_poll['post_content'] = trim(self::$args['description']);
		}
		else
		{
			if(self::$method == 'put')
			{
				$insert_poll['post_content'] = null;
			}
		}

		// summary
		if(self::isset_args('summary'))
		{
			$insert_poll['post_meta']['summary'] = trim(self::$args['summary']);
			if(self::$args['summary'] && mb_strlen(self::$args['summary']) > 150)
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:summary:max:length', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Summery must be less than 150 character"), 'summary', 'arguments');
			}
		}
		else
		{
			if(self::$method == 'put')
			{
				$insert_poll['post_meta']['summary'] = null;
			}
		}

		// summary
		if(self::isset_args('access_profile'))
		{
			$insert_poll['post_meta']['access_profile'] = self::$args['access_profile'];
			if(!is_array(self::$args['access_profile']) && !is_null(self::$args['access_profile']))
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:access_profile:array', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Access profile must be array"), 'access_profile', 'arguments');
			}
		}
		else
		{
			if(self::$method == 'put')
			{
				$insert_poll['post_meta']['access_profile'] = null;
			}
		}

		// get the insert id by check sarshomar permission
		// when not in upldate mode
		if(!self::$update_mod && self::poll_check_permission('u', 'sarshomar', 'view'))
		{
			$next_id   = (int) \lib\db\polls::sarshomar_id();
			$insert_poll['id'] = ++$next_id;
		}

		if(self::isset_args('language'))
		{
			// check language
			if(!\lib\utility\location\languages::check(self::$args['language']))
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:language:invalid', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return \lib\debug::error(T_("Invalid parametr language"), 'language', 'arguments');
			}

			if(\lib\define::get_language() !== self::$args['language'])
			{
				\lib\db\logs::set('user:poll:add:difrent:language', self::$args['user'], ['meta' => ['input' => self::$args]]);
			}

			$insert_poll['post_language'] = self::$args['language'];

		}
		else
		{
			if(self::$method == 'post')
			{
				$insert_poll['post_language'] = \lib\define::get_language();
			}
		}


		if(!self::$update_mod)
		{
			$insert_poll['post_sarshomar'] = self::poll_check_permission('u', 'sarshomar', 'view') ? 1 : 0;
			$insert_poll['post_privacy']   = 'private';
			$insert_poll['user_id']        =  self::$args['user'];
			$insert_poll['post_status']    = 'draft';
			$insert_poll['post_type']      = 'poll';
			$insert_poll['post_comment']   = 'open';
		}

		$change_tree = false;
		$tree_args   = false;

		// tree
		if(self::isset_args('tree','parent'))
		{
			$change_tree = true;

			$parent = self::$args['tree']['parent'];

			if($parent && !preg_match("/^[". self::$args['shortURL']. "]+$/", $parent))
			{

				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:tree:invalid', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Invalid tree parameter parent"), 'parent', 'arguments');
			}

			if($parent)
			{
				$loc_id  = \lib\utility\shortURL::decode($parent);
				$loc_opt = self::$args['tree']['answers'];

				if($loc_opt === true || $loc_opt == 'skipped')
				{
					$loc_opt = $loc_opt;
				}
				elseif(!is_array($loc_opt))
				{
					$loc_opt = [$loc_opt];

					foreach ($loc_opt as $key => $value)
					{
						if(!is_numeric($value))
						{
							// \lib\db::rollback();
							\lib\db\logs::set('user:poll:add:error:tree:value', self::$args['user'], ['meta' => ['input' => self::$args]]);
							return debug::error(T_("Invalid tree parameter :value", ['value' => $value]), 'answers', 'arguments');
						}
					}
				}

				$tree_args            = [];
				$tree_args['user_id'] = self::$args['user'];
				$tree_args['parent']  = $loc_id;
				$tree_args['opt']     = $loc_opt;
			}
		}
		else
		{
			if(self::$method == 'put')
			{
				if(self::$poll_id)
				{
					\lib\utility\poll_tree::remove(self::$poll_id);
				}
			}
		}

		$is_public_poll = false;
		if(self::isset_args('privacy'))
		{
			if(self::$args['privacy'] === 'public')
			{
				$insert_poll['post_privacy'] = 'public';
				$is_public_poll = true;
			}
			else
			{
				if(self::isset_args('from','count') && (int) self::$args['from']['count'] > 0)
				{
					\lib\db\logs::set('user:poll:add:error:private:set:member', self::$args['user'], ['meta' => ['input' => self::$args]]);
					return debug::error(T_("You can not set member in private poll"), 'privacy', 'arguments');
				}
				$insert_poll['post_privacy'] = 'private';
			}
		}
		else
		{
			if(self::$method == 'put')
			{
				$insert_poll['post_privacy'] = 'private';
			}
		}


		if(!self::poll_check_permission('admin'))
		{
			$insert_poll['post_status'] = 'draft';
		}

		$post_meta = [];

		if(isset($insert_poll['post_meta']) && !empty($insert_poll['post_meta']))
		{
			$post_meta                = $insert_poll['post_meta'];
			$insert_poll['post_meta'] = json_encode($insert_poll['post_meta'], JSON_UNESCAPED_UNICODE);
		}

		// inset poll if we not in update mode
		if(!self::$update_mod)
		{
			self::$poll_id = self::insert($insert_poll);
		}
		else
		{
			$old_post_meta = isset(self::$old_saved_poll['meta']) ? self::$old_saved_poll['meta'] : [];
			$post_meta     = array_merge($old_post_meta, $post_meta);
			$insert_poll['post_meta'] = json_encode($post_meta, JSON_UNESCAPED_UNICODE);
			unset($insert_poll['id']);
			self::update($insert_poll, self::$poll_id);
		}

		if(!self::$update_mod)
		{
			$new_url = '$/'. \lib\utility\shortURL::encode(self::$poll_id);
			self::$poll_full_url = $new_url;
			self::update(['post_url' => $new_url], self::$poll_id);
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

		if($is_public_poll)
		{
			\lib\db\ranks::plus(self::$poll_id, 'public', 1, ['replace' => true]);
		}
		else
		{
			\lib\db\ranks::plus(self::$poll_id, 'public', 0, ['replace' => true]);
		}

		// insert filters
		if(self::isset_args('from'))
		{
			if(!is_array(self::$args['from']))
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:from:array', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Parameter From must be array"), 'from', 'arguments');
			}

			$insert_filters = \lib\utility\postfilters::update(self::$args['from'], self::$poll_id);
			/**
			 * set ranks
			 * plus (int) member in member field
			 */
			if(self::isset_args('from','count'))
			{

				$member       = (int) self::$args['from']['count'];

				unset(self::$args['from']['count']);

				$member_exist = (int) \lib\db\filters::count_user(self::$args['from']);

				debug::msg("member_exist", $member_exist);

				if(self::poll_check_permission('u', 'sarshomar', 'view') && $member_exist === $member)
				{
					\lib\db\ranks::plus(self::$poll_id, "member", 1000000000, ['replace' => true]);
				}
				else
				{
					\lib\db\ranks::plus(self::$poll_id, "member", $member, ['replace' => true]);
				}

				if($member <= $member_exist)
				{
					if(self::$debug)
					{
						// \lib\db::rollback();
						\lib\db\logs::set('user:poll:add:error:max:member', self::$args['user'], ['meta' => ['input' => self::$args]]);
						return debug::error(T_(":max users were found, reduce the number of users ",["max" => $member_exist]), 'count', 'arguments');
					}
				}

			}
		}

	}
}
?>