<?php
namespace lib\db\polls\insert;
use \lib\debug;

trait options
{
	protected static function insert_options()
	{
		// save meta range_timing_maxs
		if(isset(self::$args['options']['range_timing_max']))
		{
			if(self::$args['options']['range_timing_max'] && !is_numeric(self::$args['options']['range_timing_max']))
			{
				return debug::error(T_("Invalid arguments range_timing_max"), 'range_timing_max', 'arguments');
			}
			self::save_options('range_timing_max', self::$args['options']['range_timing_max']);
		}

		// save meta choice_count_min
		if(isset(self::$args['options']['choice_count_min']))
		{
			if(self::$args['options']['choice_count_min'] && !is_numeric(self::$args['options']['choice_count_min']))
			{
				return debug::error(T_("Invalid arguments choice_count_min"), 'choice_count_min', 'arguments');
			}
			self::save_options('choice_count_min', self::$args['options']['choice_count_min']);
		}

		// save meta choice_count_max
		if(isset(self::$args['options']['choice_count_max']))
		{
			if(self::$args['options']['choice_count_max'] && !is_numeric(self::$args['options']['choice_count_max']))
			{
				return debug::error(T_("Invalid arguments choice_count_max"), 'choice_count_max', 'arguments');
			}
			self::save_options('choice_count_max', self::$args['options']['choice_count_max']);
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

		// save meta hidden_result
		if(isset(self::$args['options']['hidden_result']))
		{
			if(self::$args['options']['hidden_result'])
			{
				self::save_options('hidden_result', true);
			}
			else
			{
				self::save_options('hidden_result', false);
			}
		}

		// save meta ordering
		if(isset(self::$args['options']['ordering']))
		{
			if(self::$args['options']['ordering'])
			{
				self::save_options('ordering', true);
			}
			else
			{
				self::save_options('ordering', false);
			}
		}

		// save meta start_date
		if(isset(self::$args['options']['start_date']))
		{
			if(self::$args['options']['start_date'] && \DateTime::createFromFormat('Y-m-d', self::$args['options']['start_date']) === false)
			{
				return debug::error(T_("Invalid arguments start_date"), 'start_date', 'arguments');
			}
			self::save_options('start_date', self::$args['options']['start_date']);
		}

		// save meta end_date
		if(isset(self::$args['options']['end_date']))
		{
			if(self::$args['options']['end_date'] && \DateTime::createFromFormat('Y-m-d', self::$args['options']['end_date']) === false)
			{
				return debug::error(T_("Invalid arguments end_date"), 'end_date', 'arguments');
			}
			self::save_options('end_date', self::$args['options']['end_date']);
		}

		// save meta article
		if(isset(self::$args['options']['article']) && self::$args['permission_sarshomar'] === true)
		{
			if(self::$args['options']['article'] && !preg_match("/^[". self::$args['shortURL']. "]+$/", self::$args['options']['article']))
			{
				return debug::error(T_("Invalid arguments article"), 'article', 'arguments');
			}
			self::save_options('article', \lib\utility\shortURL::decode(self::$args['options']['article']));
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

		if(isset(self::$args['options']['tags']) && is_array(self::$args['options']['tags']))
		{
			$remove_tags = \lib\db\tags::remove(self::$poll_id);

			$tags = self::$args['options']['tags'];

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

		if(self::$args['permission_sarshomar'] === true)
		{
			if(isset(self::$args['options']['cats']))
			{
				\lib\db\cats::set(self::$args['options']['cats'], self::$poll_id);
			}
		}

	}


	/**
	 * save options record for option of poll
	 *
	 * @param      <type>  $_key    The key
	 * @param      <type>  $_value  The value
	 */
	private static function save_options($_key, $_value)
	{
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
			$where                          = $option_insert;
			if($_value)
			{
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
				$option_insert['option_value']  = $_value;
				$option_result = \lib\db\options::insert($option_insert);
			}
		}
	}
}
?>