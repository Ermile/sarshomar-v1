<?php
namespace lib\db\polls;

trait setting
{

		/**
	 * set the options for every poll type
	 *
	 * @param      <type>  $_poll_type  The poll type
	 */
	private static function support_answer_object($_answer_type)
	{
		$support_options = [];
		switch ($_answer_type) 
		{
			case "select" : 
				$support_options["is_true"] 		= "/^(true|false)$/";
				$support_options["score"]  			= "/^(|\d+)$/";		
				break;

			case "emoji" : 
				$support_options["type"]            = "/^(star|like|)$/";
				$support_options["is_true"]         = "/^(true|false)$/";
				$support_options["score"]           = "/^(|\d+)$/" ;
				$support_options["star_size_min"]   = "/^(\d+|)$/";
				$support_options["star_size_max"]   = "/^(\d+|)$/";
				break;

			case "descriptive" : 
				$support_options["text_format"]     = "/^(any|tel|email|website|number|password|custom)$/";
				$support_options["text_length_min"] = "/^(\d+|)$/";
				$support_options["text_length_max"] = "/^(\d+|)$/";
				break;

			case "upload" : 			
				$support_options["file_format"]     = "/^(string)$/";
				$support_options["file_size_min"]   = "/^(\d+|)$/";
				$support_options["file_size_max"]   = "/^(\d+|)$/";
				break;

			case "range" :
				$support_options["number_size_min"] = "/^(\d+)$/";
				$support_options["number_size_max"] = "/^(\d+)$/";
				
			default:
					$support_options = [];
				break;
		}
		return $support_options;
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