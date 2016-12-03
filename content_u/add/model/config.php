<?php
namespace content_u\add\model;
use \lib\utility;
use \lib\debug;

trait config
{

	/**
	 * set the meta for every poll type
	 *
	 * @param      <type>  $_poll_type  The poll type
	 */
	private static function meta($_poll_type)
	{
		// the meta list
		$meta                  = [];
		$meta['random_sort']   = false;
		$meta['score']         = false;
		$meta['true_answer']   = false;
		$meta['descriptive']   = false;
		$meta['profile']       = false;
		$meta['tree']          = true;
		$meta['hidden_result'] = false;
		$meta['comment']	   = false;

		switch ($_poll_type)
		{
			// in html: multiple_choice
			case 'select':
				$meta['random_sort']   = true;
				$meta['score']         = true;
				$meta['true_answer']   = true;
				$meta['descriptive']   = true;
				$meta['profile']       = true;
				$meta['hidden_result'] = true;
				break;

			// in html: descriptive
			case 'text':

				break;
			// in html: notification
			case 'notify':

				break;
			// in html: upload
			case 'upload':

				break;
			// in html: starred
			case 'star':

				break;
			// in html: numerical
			case 'number':

				break;
			// in html: sort
			case 'order':

				break;
			// default we have no meta
			default:
				$meta = [];
				break;
		}

		// check the value and return if the value is true
		$return = [];
		foreach ($meta as $key => $value)
		{
			if($value === true)
			{
				array_push($return, $key);
			}
		}
		return $return;
	}

	/**
	 * check the posted poll type and return the db poll type
	 *
	 * @param      boolean|string  $_poll_type  The poll type
	 *
	 * @return     boolean|string  ( description_of_the_return_value )
	 */
	public static function change_type($_poll_type)
	{
		$type = false;

		switch ($_poll_type)
		{
			case 'multiple_choice':
			case 'multiplechoice':
				$type = 'select';
				break;

			case 'descriptive':
				$type = 'text';
				break;

			case 'notification':
				$type = 'notify';
				break;

			case 'upload':
				$type = 'upload';
				break;

			case 'starred':
				$type = 'star';
				break;

			case 'numerical':
				$type = 'number';
				break;

			case 'sort':
				$type = 'order';
				break;

			// $poll_type = 'media_image';
			// $poll_type = 'media_video';
			// $poll_type = 'media_audio';

			default:
				$type = false;
				break;
		}
		return $type;
	}

		/**
	 * search in $_POST
	 * and return all answer data in post
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	function answers_in_post()
	{
		$answers      = [];
		$answer_true  = [];
		$answer_type  = [];
		$answer_point = [];
		$answer_desc  = [];

		$i = 0;
		do
		{
			$do = false;
			$i++;
			if(utility::post("answer$i"))
			{
				$do = true;
				$answers[$i]      = utility::post("answer$i");
				$answer_true[$i]  = (utility::post("true$i")  != '') ? utility::post("true$i") : '';
				$answer_type[$i]  = (utility::post("type$i")  != '') ? utility::post("type$i") : 'select';
				$answer_point[$i] = (utility::post("point$i") != '') ? utility::post("point$i"): '';
				$answer_desc[$i]  = (utility::post("desc$i")  != '') ? utility::post("desc$i") : '';
			}
		}
		while($do);

		return
		[
			'answers'      => $answers,
			'answer_true'  => $answer_true,
			'answer_type'  => $answer_type,
			'answer_point' => $answer_point,
			'answer_desc'  => $answer_desc,
		];
	}


	/**
	 * remove all meta in option table of this poll
	 *
	 * @param      <type>  $_key    The key
	 * @param      string  $_value  The value
	 */
	function remove_meta()
	{
		$cat = "poll_". $this->poll_id;
		$where =
		[
			'post_id'    => $this->poll_id,
			'option_cat' => $cat,
			'option_key' => 'meta'
		];
		return \lib\utility\answers::hard_delete($where);
	}


	/**
	 * Removes an answer.
	 */
	function remove_answers()
	{
		$cat = "poll_". $this->poll_id;
		$where =
		[
			'post_id'    => $this->poll_id,
			'option_cat' => $cat,
			'option_key' => 'opt%'
		];
		return \lib\utility\answers::hard_delete($where);
	}
}
?>