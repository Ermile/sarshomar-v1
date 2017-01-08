<?php 
namespace content_api\upload\tools;

trait post 
{
	/**
	 * Posts a upload.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function post_upload($_args)
	{		
		var_dump($_FILES);
		var_dump($_POST);
		var_dump(\lib\utility::files());
		exit();
	}

}

?>