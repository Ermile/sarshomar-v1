<?php
namespace lib\utility\answers;

trait insert
{
	/**
	 * insert answers to pollopts table
	 *
	 * @param      array  $_args  list of answers and post id
	 *
	 * @return     <type>  mysql result
	 */
	public static function insert($_args)
	{
		if(!isset($_args['poll_id']) || !isset($_args['answers']) || !is_array($_args['answers']))
		{
			return false;
		}

		$update = false;
		if(isset($_args['update']) && $_args['update'])
		{
			$update = true;
		}


		$old_answers = [];
		$old_opt_key = [];
		if($update)
		{
			$field = ['id', 'key','post_id','true','title','desc','score','profile','attachment_id', 'attachmenttype'];
			$old_answers = \lib\db\pollopts::get_all($_args['poll_id'], $field);
			$old_opt_key = array_column($old_answers, 'key', 'id');
			if(is_array($old_answers))
			{
				$tmp_old_answer = [];
				foreach ($old_answers as $key => $value)
				{
					array_shift($value);
					$tmp_old_answer[$value['key']] = $value;
				}
				$old_answers = $tmp_old_answer;
			}
		}

		$answers  = [];
		$opt_meta = [];
		// answers key : opt_1, opt_2, opt_[$i], ...
		$i = 0;
		foreach ($_args['answers'] as $key => $value)
		{
			$old_attachment = isset($value['file']) ? $value['file'] : null;

			$i++;
			$tmp_answers =
			[
				'key'           => $i,
				'post_id'       => $_args['poll_id'],
				'true'          => isset($value['true'])  			? 1 						: 0,
				'title'         => isset($value['title'])  			? $value['title']  			: null,
				'type'          => isset($value['type'])  			? $value['type']  			: null,
				'meta'          => isset($value['meta'])  			? $value['meta']			: null,
				'desc'          => isset($value['desc'])    		? $value['desc']  			: null,
				'score'         => isset($value['score']) 			? $value['score'] 			: null,
				'groupscore'    => isset($value['groupscore']) 		? $value['groupscore'] 		: null,
				'profile'       => isset($value['profile'])  		? $value['profile']  		: null,
				'attachment_id' => isset($value['attachment_id'])  	? $value['attachment_id'] 	: null,
			];

			if($update && in_array($i, $old_opt_key))
			{
				$all_old_attachment = array_column($old_answers, 'attachment_id');
				if($old_attachment)
				{
					$old_attachment = \lib\utility\shortURL::decode($old_attachment);

					if(in_array($old_attachment, $all_old_attachment))
					{
						$tmp_answers['attachment_id'] = $old_attachment;
					}
				}

				if(isset($old_answers[$i]) && $old_answers[$i] == $tmp_answers)
				{
					\lib\db\pollopts::update(['status' => 'enable'], $_args['poll_id'], $i);

				}
				else
				{
					$tmp_answers['status'] = 'enable';
					\lib\db\pollopts::update($tmp_answers, $_args['poll_id'], $i);
				}
			}
			else
			{
				$answers[] = $tmp_answers;
			}
		}

		$return = true;
		
		if(!empty($answers))
		{
			$return = \lib\db\pollopts::insert_multi($answers);
		}

		if(count($old_answers) > $i)
		{
			for ($delete = $i + 1; $delete <= count($old_answers); $delete++)
			{
				\lib\db\pollopts::update(['status' => 'disable'], $_args['poll_id'], $delete);
			}
		}
		return $return;
	}
}
?>