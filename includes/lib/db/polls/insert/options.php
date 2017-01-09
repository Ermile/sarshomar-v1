<?php 
namespace lib\db\polls\insert;
use \lib\debug;

trait options
{
	protected static function insert_options()
	{
			

		if(isset(self::$args['options']['tags']) && is_array(self::$args['options']['tags']))
		{
			$remove_tags = \lib\db\tags::remove($poll_id);
			$tags = self::$args['options']['tags'];
			$check_count = array_filter($tags);
			if(count($check_count) > 3 && self::$args['permission_sarshomar'] === false)
			{
				return debug::error(T_("You have added so many tags, Please remove some of them"));
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
					'termusage_id'      => $poll_id
				];
			}
			$useage = \lib\db\termusages::insert_multi($useage_arg);
		}

		$publish_date = [];
		if(isset(self::$args['options']['start_date']) && self::$args['options']['start_date'])
		{	
			$publish_date[] =
			[
				'post_id'      => $poll_id,
				'option_cat'   => "poll_{$poll_id}",
				'option_key'   => "start_date",
				'option_value' => self::$args['options']['start_date']
			];
		}

		if(isset(self::$args['options']['end_date']) && self::$args['options']['end_date'])
		{	
			$publish_date[] =
			[
				'post_id'      => $poll_id,
				'option_cat'   => "poll_{$poll_id}",
				'option_key'   => "end_date",
				'option_value' => self::$args['options']['end_date']
			];
		}
		if(!empty($publish_date))
		{
			$publish_date_query = \lib\db\options::insert_multi($publish_date);
		}


		if(self::$args['permission_sarshomar'] === true)
		{
			if(isset(self::$args['options']['article']) && self::$args['options']['article'])
			{
				$article =
				[
					'post_id'      => $poll_id,
					'option_cat'   => "poll_$poll_id",
					'option_key'   => "article",
					'option_value' => \lib\utility\shortURL::decode(self::$args['options']['article'])
				];
				$article_insert = \lib\db\options::insert($article);
				if(!$article_insert)
				{
					\lib\db\options::update_on_error($article, array_splice($article, 1));
				}
			}

			if(isset(self::$args['options']['cats']))
			{
				\lib\db\cats::set(self::$args['options']['cats'], $poll_id);
			}
		}


	}
}
?>