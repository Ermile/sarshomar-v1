<?php
namespace lib\db\polls\insert;
use \lib\debug;
use \lib\utility;
use \lib\utility\shortURL;

trait options
{
	use options_members;

	protected static function insert_options()
	{
		/**
		 * check members and save it options
		 */
		self::insert_options_members();

		if(!debug::$status)
		{
			return false;
		}

		// save send by sms options
		if(self::isset_args('options','send_sms'))
		{
			if(self::$args['options']['send_sms'])
			{
				self::save_options('send_sms', true);
			}
			else
			{
				self::save_options('send_sms', false);
			}
		}
		else
		{
			if(self::$method == 'put')
			{
				self::save_options('send_sms', false);
			}
		}

		// save poll password
		if(self::isset_args('options','password'))
		{
			if(self::$args['options']['password'])
			{
				if(!is_string(self::$args['options']['password']) || mb_strlen(self::$args['options']['password']) > 20)
				{
					debug::error(T_("You must set less than 20 character in password"), 'password', 'arguments');
					return false;
				}
				self::save_options('password', trim(self::$args['options']['password']));
			}
			else
			{
				self::save_options('password', false);
			}
		}
		else
		{
			if(self::$method == 'put')
			{
				self::save_options('password', false);
			}
		}

		// save options prize
		if(self::isset_args('options','prize'))
		{
			if(self::$args['options']['prize'])
			{
				$user_unit = null;
				if(self::poll_check_permission('admin'))
				{
					if(isset(self::$old_saved_poll['sarshomar']) && self::$old_saved_poll['sarshomar'] === '1')
					{
						$user_unit = 'sarshomar';
					}
					elseif(self::$real_user_id)
					{
						$user_unit = \lib\db\units::find_user_unit(self::$real_user_id);
					}
				}
				else
				{
					$user_unit = \lib\db\units::find_user_unit(self::$user_id);
				}
				self::save_options('prize', self::$args['options']['prize'], ['unit' => $user_unit]);
			}
			else
			{
				self::save_options('prize', false);
			}
		}
		else
		{
			if(self::$method == 'put')
			{
				self::save_options('prize', false);
			}
		}


		// save meta times
		if(self::isset_args('brand','title'))
		{
			if(self::$args['brand']['title'] && mb_strlen(self::$args['brand']['title']) > 99)
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:options:brand:max:title', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Invalid brand title argument, you must set less than 99 character for the title"), 'title', 'arguments');
			}

			$url = null;
			if(self::isset_args('brand','url'))
			{
				if(mb_strlen(self::$args['brand']['url']) > 99)
				{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:options:brand:max:url', self::$args['user'], ['meta' => ['input' => self::$args]]);
					return debug::error(T_(" Invalid brand URL argument, you must set less than 99 character for brand URL "), 'url', 'arguments');
				}

				$url = self::safe_user_string(self::$args['brand']['url']);
			}
			$brand_title = self::safe_user_string(self::$args['brand']['title']);
			self::save_options('brand', $brand_title, ['url' => $url]);
		}
		else
		{
			if(self::$method == 'put')
			{
				self::save_options('brand', false);
			}
		}

		// save meta times
		if(self::isset_args('options','time'))
		{
			if(self::$args['options']['time'] && !is_numeric(self::$args['options']['time']))
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:options:time:invalid', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Invalid arguments time"), 'time', 'arguments');
			}
			self::save_options('time', self::$args['options']['time']);
		}
		else
		{
			if(self::$method == 'put')
			{
				self::save_options('time', false);
			}
		}

		$set_multi_min = false;
		$set_multi_max = false;
		$set_multi     = false;
		$ordering      = false;

		// save meta min
		if(self::isset_args('options','multi','min'))
		{
			if(self::$args['options']['multi']['min'] && !is_numeric(self::$args['options']['multi']['min']))
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:options:multi:min', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Invalid arguments min"), 'min', 'arguments');
			}

			if(intval(self::$args['options']['multi']['min']) < 1)
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:options:multi:lessthan:1', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Can not set parameter 'min' less than 1"), 'min', 'arguments');
			}

			if(
				self::isset_args('options','multi','max') &&
				intval(self::$args['options']['multi']['min']) >
				intval(self::$args['options']['multi']['max'])
			  )
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:options:multi:min:greater:max', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("You can not set minimum greater than maximum in multi select settings"), 'min', 'arguments');
			}

			if(intval(self::$args['options']['multi']['min']) > count(self::$args['answers']))
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:options:multi:answers:lessthan:min', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("You have set :count answers and can not set :min in min parameter ",
					[
						'count' => count(self::$args['answers']),
						'min'   => self::$args['options']['multi']['min']
					]), 'min', 'arguments');
			}

			if(self::$args['options']['multi']['min'] > 1)
			{
				self::save_options('multi_min', self::$args['options']['multi']['min']);
			}
			else
			{
				self::save_options('multi_min', false);
			}
			$set_multi_min = true;
		}
		else
		{
			if(self::$method == 'put')
			{
				self::save_options('multi_min', false);
			}
		}

		// save meta max
		if(self::isset_args('options','multi','max'))
		{
			if(self::$args['options']['multi']['max'] && !is_numeric(self::$args['options']['multi']['max']))
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:options:multi:max', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Invalid arguments max"),'max', 'arguments');
			}

			if(intval(self::$args['options']['multi']['max']) > count(self::$args['answers']))
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:options:multi:max:largerthan:answer', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("You have set :count answers and can not set :max in max parameter ",
					[
						'count' => count(self::$args['answers']),
						'max'   => self::$args['options']['multi']['max']
					]), 'max', 'arguments');
			}

			if(
				self::isset_args('options','multi','min') &&
				intval(self::$args['options']['multi']['max']) <
				intval(self::$args['options']['multi']['min'])
			  )
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:options:multi:ordering', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("You can not set minimum greater than maximum in multi select settings"), 'max', 'arguments');
			}

			if(self::$args['options']['multi']['max'] < self::$answer_count)
			{
				self::save_options('multi_max', self::$args['options']['multi']['max']);
			}
			else
			{
				self::save_options('multi_max', false);
			}
			$set_multi_max = true;
		}
		else
		{
			if(self::$method == 'put')
			{
				self::save_options('multi_max', false);
			}
		}

		if(self::isset_args('options','multi'))
		{
			self::save_options('multi', true);
			$set_multi = true;
		}
		else
		{
			if(self::$method == 'put')
			{
				self::save_options('multi', false);
			}
		}

		// save meta ordering
		if(self::isset_args('options','ordering'))
		{
			if(self::$args['options']['ordering'])
			{
				if($set_multi_min)
				{
					// \lib\db::rollback();
					\lib\db\logs::set('user:poll:add:error:options:multi:min:with:ordering', self::$args['user'], ['meta' => ['input' => self::$args]]);
					return debug::error(T_("Can not use multi:min and ordering"), 'ordering', 'arguments');
				}

				if($set_multi_max)
				{
					// \lib\db::rollback();
					\lib\db\logs::set('user:poll:add:error:options:multi:max:with:ordering', self::$args['user'], ['meta' => ['input' => self::$args]]);
					return debug::error(T_("Can not use multi:max and ordering"), 'ordering', 'arguments');
				}

				if($set_multi)
				{
					// \lib\db::rollback();
					\lib\db\logs::set('user:poll:add:error:options:multi:with:ordering', self::$args['user'], ['meta' => ['input' => self::$args]]);
					return debug::error(T_("You can not use multi select and ordering poll"), 'ordering', 'arguments');
				}

				self::save_options('ordering', true);
				self::save_options('multi', false);
				self::save_options('multi_min', false);
				self::save_options('multi_max', false);

			}
			else
			{
				self::save_options('ordering', false);
			}
		}
		else
		{
			if(self::$method == 'put')
			{
				self::save_options('ordering', false);
			}
		}

		// save meta random_sort
		if(self::isset_args('options','random_sort'))
		{
			if(self::$args['options']['random_sort'])
			{
				self::save_options('random_sort', true);
			}
			else
			{
				self::save_options('random_sort', false);
			}
		}
		else
		{
			if(self::$method == 'put')
			{
				self::save_options('random_sort', false);
			}
		}

		if(self::isset_args('options','hide_result'))
		{
			self::save_options('hide_result', true);
		}
		else
		{
			if(self::$method == 'put')
			{
				self::save_options('hide_result', false);
			}
		}

		// save meta start_date
		if(self::isset_args('schedule','start'))
		{
			if(self::$args['schedule']['start'] && \DateTime::createFromFormat('Y-m-d', self::$args['schedule']['start']) === false)
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:options:time:start', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Invalid arguments start"), 'schedule', 'arguments');
			}
			self::save_options('start_date', self::$args['schedule']['start']);
		}
		else
		{
			if(self::$method == 'put')
			{
				self::save_options('start_date', false);
			}
		}

		// save meta end_date
		if(self::isset_args('schedule','end'))
		{
			if(self::$args['schedule']['end'] && \DateTime::createFromFormat('Y-m-d', self::$args['schedule']['end']) === false)
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:options:time:end', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Invalid arguments end_date"), 'end_date', 'arguments');
			}
			self::save_options('end_date', self::$args['schedule']['end']);
		}
		else
		{
			if(self::$method == 'put')
			{
				self::save_options('end_date', false);
			}
		}

		// save meta article
		if(self::isset_args('articles'))
		{
			if(!is_array(self::$args['articles']))
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:options:articles:array', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Parameter article must be array"), 'article', 'arguments');
			}

			foreach (self::$args['articles'] as $key => $value)
			{
				if(!preg_match("/^[". self::$args['shortURL']. "]+$/", $value))
				{
					// \lib\db::rollback();
					\lib\db\logs::set('user:poll:add:error:options:articles:invalid', self::$args['user'], ['meta' => ['input' => self::$args]]);
					return debug::error(T_("Invalid arguments article on index :key", ['key' => $key]), 'articles', 'arguments');
					break;
				}
				self::save_options('articles', \lib\utility\shortURL::decode($value), ['_multi_' => true]);
			}
		}
		else
		{
			if(self::$method == 'put')
			{
				self::save_options('articles', false);
			}
		}

		/**
		 * upload files of poll title
		 */
		// save meta file
		if(self::isset_args('file'))
		{
			// remove attachment from this post
			if(!self::$args['file'])
			{
				self::save_options('title_attachment', false);
			}
			else
			{

				$attachment = self::is_attachment(self::$args['file']);
				$url = null;
				if(isset($attachment['url']))
				{
					$url = $attachment['url'];
				}

				$type = null;
				if(isset($attachment['type']))
				{
					$type = $attachment['type'];
				}

				if(!debug::$status)
				{
					return;
				}

				self::save_options('title_attachment',  shortURL::decode(self::$args['file']), ['url' => $url, 'type' => $type]);
			}
		}
		else
		{
			if(self::$method == 'put')
			{
				self::save_options('title_attachment', false);
			}
		}

		if(self::isset_args('tags'))
		{
			if(!is_array(self::$args['tags']))
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:answer:tag:array', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Parameter tags must be array"), 'tags', 'arguments');
			}

			$tags = self::$args['tags'];

			$check_count = array_filter($tags);

			if(count($check_count) >= 5 && !self::poll_check_permission('u','sarshomar', 'view'))
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:options:tag:max:limit', self::$args['user'], ['meta' => ['input' => self::$args]]);
				debug::warn(T_("You have added so many tags, You can save just 5 tags"), 'tags', 'arguments');
				array_splice($tags, 5);
			}

			$temp_poll_id = self::$poll_id;

			$remove_tags =
			"
				DELETE FROM
					termusages
				WHERE
					termusages.termusage_foreign = 'tag' AND
					termusages.termusage_id      = $temp_poll_id

			";

			\lib\db::query($remove_tags);

			$insert_tag = [];
			foreach ($tags as $key => $value)
			{
				$value = self::safe_user_string($value);
				if(mb_strlen($value) > 45)
				{
					// \lib\db::rollback();
					\lib\db\logs::set('user:poll:add:error:options:tag:max:length', self::$args['user'], ['meta' => ['input' => self::$args]]);
					return debug::error(T_("Invalid tag in index :key, tags must be less than 45 character", ['key' => $key]), 'tags', 'arguments');
				}

				$slug  = utility\filter::slug($value, null, 'persian');
				if(!$slug && $value)
				{
					$slug = $value;
				}
				$temp_insert_tags =
				[
					'term_type'   => 'sarshomar_tag',
					'term_title'  => $value,
					'term_url'    => '$/tag/'. $slug,
					'term_slug'   => $slug,
					'term_caller' => null,
				];
				if(\lib\db\terms::insert($temp_insert_tags))
				{
					$insert_tag[] = $temp_insert_tags;
				}
			}

			$tags_id = [];

			if(!empty($insert_tag))
			{

				$tags_title = array_column($insert_tag, 'term_title');
				$check_tags = \lib\db\words::save_and_check($tags_title);
				if(!$check_tags)
				{
					$spam_words = \lib\db\words::$spam;
					foreach ($spam_words as $key => $value)
					{
						unset($tags_title[array_search($key, $tags_title)]);
					}
				}



				if(!empty($tags_title))
				{
					$tag_slug = $tags_title;

					foreach ($tag_slug as $key => $value)
					{
						$tag_slug[$key]  = utility\filter::slug($value, null, 'persian');
					}

					$tag_slug = implode("','", $tag_slug);
					$get_ids =
					"
						SELECT
							terms.id  AS `id`
						FROM
							terms
						WHERE
							terms.term_slug IN ('$tag_slug') AND
							terms.term_type LIKE 'sarshomar_tag'
					";
					$tags_id = \lib\db::get($get_ids, 'id');
				}
			}

			if(!is_array($tags_id))
			{
				$tags_id = [];
			}

			// save tag to this poll
			$useage_arg = [];
			foreach ($tags_id as $key => $value)
			{
				$useage_arg[] =
				[
					'termusage_foreign' => 'tag',
					'term_id'           => $value,
					'termusage_id'      => self::$poll_id
				];
			}
			\lib\db\termusages::insert_multi($useage_arg);
		}
		else
		{
			if(self::$method == 'put')
			{
				$temp_poll_id = self::$poll_id;

				$remove_tags =
				"
					DELETE FROM
						termusages
					WHERE
						termusages.termusage_foreign = 'tag' AND
						termusages.termusage_id      = $temp_poll_id
				";
				\lib\db::query($remove_tags);
			}
		}

		if(self::isset_args('cat') && self::poll_check_permission('u', 'sarshomar', 'view'))
		{
			if(!preg_match("/^[". self::$args['shortURL']. "]+$/", self::$args['cat']))
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:cats:invalid', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Invalid parameter cats"), 'cat', 'arguments');
			}

			$term_id = shortURL::decode(self::$args['cat']);

			$check = \lib\db\terms::get($term_id);

			if(!$check)
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:options:cat:notfound', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Cat not found"), 'cat', 'arguments');
			}

			if(!isset($check['term_type']) || (isset($check['term_type']) && $check['term_type'] != 'sarshomar'))
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:options:cat:invalid', self::$args['user'], ['meta' => ['input' => self::$args]]);
				return debug::error(T_("Invalid category"), 'cat', 'arguments');
			}

			$temp_poll_id = self::$poll_id;

			$query_delete_exist_cat =
			"
				DELETE FROM
					termusages
				WHERE
					termusages.termusage_foreign = 'cat' AND
					termusages.termusage_id      = $temp_poll_id

			";
			\lib\db::query($query_delete_exist_cat);

			$query =
			"
				INSERT INTO
					termusages
				SET
					termusages.termusage_foreign = 'cat',
					termusages.termusage_id      = $temp_poll_id,
					termusages.term_id           = $term_id
				ON DUPLICATE KEY UPDATE
					termusages.term_id = $term_id
			";

			if(\lib\db::query($query))
			{
				if(isset($check['term_type']) && $check['term_type'])
				{
					$new_url = isset($check['term_url']) ? $check['term_url'] : null;
					$new_url .= '/'. shortURL::encode(self::$poll_id);
					self::$poll_full_url = $new_url;
					self::update(['post_url' => $new_url], self::$poll_id);
				}
			}
		}
		else
		{
			if(self::$method == 'put')
			{

				$temp_poll_id = self::$poll_id;

				$query_delete_exist_cat =
				"
					DELETE FROM
						termusages
					WHERE
						termusages.termusage_foreign = 'cat' AND
						termusages.termusage_id      = $temp_poll_id
				";
				\lib\db::query($query_delete_exist_cat);
			}
		}
	}


	/**
	 * save options record for option of poll
	 *
	 * @param      <type>  $_key    The key
	 * @param      <type>  $_value  The value
	 */
	private static function save_options($_key, $_value, $_meta = [])
	{
		$multi_options = false;
		if(isset($_meta['_multi_']) && $_meta['_multi_'])
		{
			$multi_options = true;
			unset($_meta['_multi_']);
		}

		$option_meta = null;
		if(!empty($_meta))
		{
			$option_meta = json_encode($_meta, JSON_UNESCAPED_UNICODE);
		}

		$option_insert =
		[
			'post_id'       => self::$poll_id,
			'option_cat'    => 'poll_'. self::$poll_id,
			'option_key'    => $_key,
			'limit'			=> 1,
		];

		if($multi_options)
		{
			$option_insert['option_value'] = $_value;
		}

		$check = \lib\db\options::get($option_insert);
		unset($option_insert['limit']);
		if($check)
		{
			$where = $option_insert;
			if($_value)
			{
				if($option_meta)
				{
					$option_insert['option_meta'] = $option_meta;
				}

				$option_insert['option_value']  = $_value;
				$option_insert['option_status'] = 'enable';
			}
			else
			{
				$option_insert['option_status'] = 'disable';
			}

			$option_result = \lib\db\options::update_on_error($option_insert, $where);
		}
		else
		{
			if($_value)
			{
				if($option_meta)
				{
					$option_insert['option_meta'] = $option_meta;
				}

				$option_insert['option_value']  = $_value;
				$option_result = \lib\db\options::insert($option_insert);
			}
		}
	}
}
?>