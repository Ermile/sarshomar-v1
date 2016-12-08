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
		$meta                       = [];
		$meta['tree']               = true;
		$meta['rangetiming-min']    = true;
		$meta['rangetiming-max']    = true;
		// $meta['filesize-min']       = false;
		// $meta['filesize-max']       = false;
		// $meta['textlength-min']     = false;
		// $meta['textlength-max']     = false;
		// $meta['numbersize-min']     = false;
		// $meta['numbersize-max']     = false;
		// $meta['starsize-min']       = false;
		// $meta['starsize-max']       = false;
		// $meta['answer']             = false; // just in multichoise we need to answer
		// $meta['choice-count-min']   = false;
		// $meta['choice-count-max']   = false;
		// $meta['random_sort']        = false;
		// $meta['score']              = false;
		// $meta['true_answer']        = false;
		// $meta['descriptive']        = false;
		// $meta['profile']            = false;
		// $meta['hidden_result']      = false;
		// $meta['comment']            = false;
		// $meta['ordering']           = false;
		// $meta['choicemode']         = false; // one|multi|ordering
		// $meta['text_format']        = false;
		// $meta['file_format']        = false;
		// $meta['rangemode']          = false; //number|star|like
		// $meta['text_format_custom'] = false;
		// $meta['file_format_custom'] = false;

		switch ($_poll_type)
		{
			//-------------- in html: multiple
			case 'select':
				$meta['answer']           = true;
				$meta['choice-count-min'] = true;
				$meta['choice-count-max'] = true;
				$meta['random_sort']      = true;
				$meta['score']            = true;
				$meta['true_answer']      = true;
				$meta['descriptive']      = true;
				$meta['profile']          = true;
				$meta['hidden_result']    = true;
				$meta['choicemode']       = true;
				$meta['choicemode']       = true;
				break;

			//-------------- in html: descriptive
			case 'text':
				$meta['text_format']        = true;
				$meta['text_format_custom'] = true;
				$meta['textlength-min']     = true;
				$meta['textlength-max']     = true;
				break;

			//-------------- in html: notification
			case 'notify':
				// no thing...
				break;

			//-------------- in html: upload
			case 'upload':
				$meta['file_format']        = true;
				$meta['file_format_custom'] = true;
				$meta['filesize-min']       = true;
				$meta['filesize-max']       = true;
				break;

			//-------------- in html: starred
			case 'star':
				$meta['rangemode']      = true;
				$meta['numbersize-min'] = true;
				$meta['numbersize-max'] = true;
				$meta['starsize-min']   = true;
				$meta['starsize-max']   = true;
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
	public static function set_db_type($_poll_type)
	{
		$type = false;

		switch ($_poll_type)
		{
			case 'multiple':
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

			case 'range':
				$type = 'star';
				break;

			default:
				$type = false;
				break;
		}
		return $type;
	}


	/**
	 * check the saved poll type and return the html poll type
	 *
	 * @param      boolean|string  $_poll_type  The poll type
	 *
	 * @return     boolean|string  ( description_of_the_return_value )
	 */
	public static function set_html_type($_poll_type)
	{
		$type = false;

		switch ($_poll_type)
		{
			case 'select':
				$type = 'multiple';
				break;

			case 'text':
				$type = 'descriptive';
				break;

			case 'notify':
				$type = 'notification';
				break;

			case 'upload':
				$type = 'upload';
				break;

			case 'star':
				$type = 'range';
				break;

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
		$answer_score = [];
		$answer_desc  = [];

		$i = 0;
		$max_post = count(utility::post());

		for ($j = 0; $j <= $max_post; $j++)
		{
			if(utility::post("answer$j"))
			{
				$i++;
				$answers[$i]      = utility::post("answer$j");
				$answer_true[$i]  = (utility::post("true$j")  != '') ? utility::post("true$j") : '';
				$answer_type[$i]  = (utility::post("type$j")  != '') ? utility::post("type$j") : 'select';
				$answer_score[$i] = (utility::post("score$j") != '') ? utility::post("score$j"): '';
				$answer_desc[$i]  = (utility::post("desc$j")  != '') ? utility::post("desc$j") : '';
			}
		}

		return
		[
			'answers'      => $answers,
			'answer_true'  => $answer_true,
			'answer_type'  => $answer_type,
			'answer_score' => $answer_score,
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
		$cat   = "poll_". $this->poll_id;
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
		$cat   = "poll_". $this->poll_id;
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