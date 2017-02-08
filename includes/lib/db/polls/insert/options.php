<?php
namespace lib\db\polls\insert;
use \lib\debug;
use \lib\utility;
use \lib\utility\shortURL;

trait options
{
	protected static function insert_options()
	{
		// save meta times
		if(isset(self::$args['brand']['title']))
		{
			if(self::$args['brand']['title'] && strlen(self::$args['brand']['title']) > 160)
			{
				return debug::error(T_("Invalid arguments brand title, you must set les than 160 character for brand"), 'title', 'arguments');
			}

			$url = null;
			if(isset(self::$args['brand']['url']))
			{
				if(strlen(self::$args['brand']['url']) > 100)
				{
					return debug::error(T_("Invalid arguments brand url, you must set les than 100 character for brand url "), 'url', 'arguments');
				}

				$url = self::$args['brand']['url'];
			}
			self::save_options('brand', self::$args['brand']['title'], ['url' => $url]);
		}

		// save meta times
		if(isset(self::$args['options']['time']))
		{
			if(self::$args['options']['time'] && !is_numeric(self::$args['options']['time']))
			{
				return debug::error(T_("Invalid arguments time"), 'time', 'arguments');
			}
			self::save_options('time', self::$args['options']['time']);
		}

		$set_multi_min = false;
		$set_multi_max = false;
		$set_multi     = false;
		$ordering      = false;

		// save meta min
		if(isset(self::$args['options']['multi']['min']))
		{
			if(self::$args['options']['multi']['min'] && !is_numeric(self::$args['options']['multi']['min']))
			{
				return debug::error(T_("Invalid arguments min"), 'min', 'arguments');
			}
			self::save_options('multi_min', self::$args['options']['multi']['min']);
			$set_multi_min = true;
		}
		else
		{
			self::save_options('multi_min', false);
		}

		// save meta max
		if(isset(self::$args['options']['multi']['max']))
		{
			if(self::$args['options']['multi']['max'] && !is_numeric(self::$args['options']['multi']['max']))
			{
				return debug::error(T_("Invalid arguments max"),'max', 'arguments');
			}
			self::save_options('multi_max', self::$args['options']['multi']['max']);
			$set_multi_max = true;
		}
		else
		{
			self::save_options('multi_max', false);
		}

		if(isset(self::$args['options']['multi']) && !$set_multi_min && !$set_multi_max)
		{
			self::save_options('multi', true);
			$set_multi = true;
		}
		else
		{
			self::save_options('multi', false);
		}

		// save meta ordering
		if(isset(self::$args['options']['ordering']))
		{
			if(self::$args['options']['ordering'])
			{
				if($set_multi_min)
				{
					return debug::error(T_("Can not use multi:min and ordering"), 'ordering', 'arguments');
				}

				if($set_multi_max)
				{
					return debug::error(T_("Can not use multi:max and ordering"), 'ordering', 'arguments');
				}

				if($set_multi)
				{
					return debug::error(T_("Can not use multi and ordering"), 'ordering', 'arguments');
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
			self::save_options('ordering', false);
		}

		// save meta random_sort
		if(isset(self::$args['options']['random_sort']))
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

		if(self::$args['hidden_result'])
		{
			self::save_options('hidden_result', true);
		}
		else
		{
			self::save_options('hidden_result', false);
		}

		// save meta start_date
		if(isset(self::$args['schedule']['start']))
		{
			if(self::$args['schedule']['start'] && \DateTime::createFromFormat('Y-m-d', self::$args['schedule']['start']) === false)
			{
				return debug::error(T_("Invalid arguments start"), 'schedule', 'arguments');
			}
			self::save_options('start_date', self::$args['schedule']['start']);
		}

		// save meta end_date
		if(isset(self::$args['schedule']['end']))
		{
			if(self::$args['schedule']['end'] && \DateTime::createFromFormat('Y-m-d', self::$args['schedule']['end']) === false)
			{
				return debug::error(T_("Invalid arguments end_date"), 'end_date', 'arguments');
			}
			self::save_options('end_date', self::$args['schedule']['end']);
		}

		// save meta article
		self::save_options('articles', false);
		if(self::$args['articles'])
		{
			if(!is_array(self::$args['articles']))
			{
				return debug::error(T_("Parameter article must be array"), 'article', 'arguments');
			}

			foreach (self::$args['articles'] as $key => $value)
			{
				if(!preg_match("/^[". self::$args['shortURL']. "]+$/", $value))
				{
					return debug::error(T_("Invalid arguments article on index :key", ['key' => $key]), 'articles', 'arguments');
					break;
				}
				self::save_options('articles', \lib\utility\shortURL::decode($value), ['_multi_' => true]);
			}
		}

		/**
		 * upload files of poll title
		 */
		// save meta file
		if(isset(self::$args['file']))
		{
			// remove attachment from this post
			if(!self::$args['file'])
			{
				self::save_options('title_attachment', false);
			}
			else
			{
				$url = self::is_attachment(self::$args['file']);

				if(!debug::$status)
				{
					return;
				}

				self::save_options('title_attachment',  shortURL::decode(self::$args['file']), ['url' => $url]);
			}
		}

		if(self::$args['tags'])
		{
			if(!is_array(self::$args['tags']))
			{
				return debug::error(T_("Parameter tags must be array"), 'tags', 'arguments');
			}

			$tags = self::$args['tags'];

			$check_count = array_filter($tags);

			if(count($check_count) >= 5 && self::$args['permission_sarshomar'] === false)
			{
				return debug::error(T_("You have added so many tags, Please remove some of them"), 'tags', 'arguments');
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
				$value = trim($value);

				if(strlen($value) > 45)
				{
						return debug::error(T_("Invalid tag in index :key, tags must be less than 45 character"), 'tags', 'arguments');
					if(self::$debug)
					{
					}

				}

				$slug  = utility\filter::slug($value);
				$insert_tag[] =
				[
					'term_type'  => 'sarshomar_tag',
					'term_title' => $value,
					'term_url'   => '$/'. $slug,
					'term_slug'  => $slug,
				];
			}

			$tags_id = [];

			if(!empty($insert_tag))
			{
				$insert = \lib\db\terms::insert_multi($insert_tag);

				$tags_title = array_column($insert_tag, 'term_title');

				$tags_title = implode("','", $tags_title);
				$get_ids =
				"
					SELECT
						terms.id  AS `id`
					FROM
						terms
					WHERE
						terms.term_title IN ('$tags_title') AND
						terms.term_type LIKE 'sarshomar%'
				";
				$tags_id = \lib\db::get($get_ids, 'id');
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

		if(self::$args['cat'] && self::$args['permission_sarshomar'] === true)
		{
			if(!preg_match("/^[". self::$args['shortURL']. "]+$/", self::$args['cat']))
			{
				return debug::error(T_("Invalid parameter cats"), 'cat', 'arguments');
			}

			$term_id = shortURL::decode(self::$args['cat']);

			$check = \lib\db\terms::get($term_id);

			if(!$check)
			{
				return debug::error(T_("Cat not found"), 'cat', 'arguments');
			}

			if(!isset($check['term_type']) || (isset($check['term_type']) && $check['term_type'] != 'sarshomar'))
			{
				return debug::error(T_("Invalid cat"), 'cat', 'arguments');
			}

			$temp_poll_id = self::$poll_id;

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
					$new_url = $check['term_url'];
					$new_url .= '/'. shortURL::encode(self::$poll_id);
					self::update(['post_url' => $new_url], self::$poll_id);
				}
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