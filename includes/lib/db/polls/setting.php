<?php
namespace lib\db\polls;

trait setting
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
		$meta['rangetiming-max']    = true;
		// $meta['rangetiming-min']    = true;
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

}
?>