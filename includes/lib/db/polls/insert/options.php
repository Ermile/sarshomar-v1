<?php
namespace lib\db\polls\insert;
use \lib\debug;

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

		if(isset(self::$args['options']['multi']) && !$set_multi_min && !$set_multi_max)
		{
			self::save_options('multi', true);
			$set_multi = true;
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
			}
			else
			{
				self::save_options('ordering', false);
			}
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
		if(self::$args['article'])
		{
			if(!is_array(self::$args['article']))
			{
				return debug::error(T_("Parameter article must be array"), 'article', 'arguments');
			}

			foreach (self::$args['article'] as $key => $value)
			{
				if(!preg_match("/^[". self::$args['shortURL']. "]+$/", $value))
				{
					return debug::error(T_("Invalid arguments article on index :key", ['key' => $key]), 'article', 'arguments');
					break;
				}
				self::save_options('article', \lib\utility\shortURL::decode($value));
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
				// upload new file
				$upload_name_path = 'upload_name';

				if(substr(self::$args['file'], 0, 7) == 'http://' ||
					substr(self::$args['file'], 0, 8) == 'https://'
				)
				{
					$upload_name_path = 'file_path';
				}

				$upload_args =
				[
					'user_id'         => self::$args['user'],
					$upload_name_path => self::$args['file']
				];

				$file_title = \lib\utility\upload::upload($upload_args);

				if(\lib\debug::get_msg("result"))
				{
					self::save_options('title_attachment',  \lib\debug::get_msg("result"));
				}
			}
		}

		if(self::$args['tags'])
		{
			if(!is_array(self::$args['tags']))
			{
				return debug::error(T_("Parameter tags must be array"), 'tags', 'arguments');
			}

			$remove_tags = \lib\db\tags::remove(self::$poll_id);

			$tags = self::$args['tags'];

			$check_count = array_filter($tags);

			if(count($check_count) > 3 && self::$args['permission_sarshomar'] === false)
			{
				return debug::error(T_("You have added so many tags, Please remove some of them"), 'tags', 'arguments');
			}

			$tags = implode(",", $tags);

			$insert_tag = \lib\db\tags::insert_multi($tags);

			$tags_id    = \lib\db\tags::get_multi_id($tags);

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
					'termusage_foreign' => 'posts',
					'term_id'           => $value,
					'termusage_id'      => self::$poll_id
				];
			}
			$useage = \lib\db\termusages::insert_multi($useage_arg);
		}

		if(self::$args['cats'])
		{
			if(!preg_match("/^[". self::$args['shortURL']. "]+$/", self::$args['cats']))
			{
				return debug::error(T_("Invalid parameter cats"), 'cats', 'arguments');
			}
			\lib\db\cats::set(utility\shortURL::decode(self::$args['cats']), self::$poll_id);
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