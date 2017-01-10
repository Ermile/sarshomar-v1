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
				$support_options["is_true"] 		= true;
				// $support_options["group"]           = (string) 'string';
				// $support_options["value"]           = (int) 1;
				break;

			case "emoji" : 
				$support_options["type"]            = (array) ['star','like'];
				$support_options["is_true"]         = true;
				// $support_options["group"]           = (string) 'string';
				// $support_options["value"]           = (int) 1;
				$support_options["star_size_min"]   = (int) 1;
				$support_options["star_size_max"]   = (int) 1;
				break;

			case "descriptive" : 
				$support_options["text_format"]     = (array) ['any','tel','email','website','number','password','custom'];
				$support_options["text_length_min"] = (int) 1;
				$support_options["text_length_max"] = (int) 1;
				break;

			case "upload" : 			
				$support_options["file_format"]     = (string) 'string';
				$support_options["file_size_min"]   = (int) 1;
				$support_options["file_size_max"]   = (int) 1;
				break;

			case "range" :
				$support_options["number_size_min"] = (int) 1;
				$support_options["number_size_max"] = (int) 1;
				
		}	
		return $support_options;
	}
}
?>