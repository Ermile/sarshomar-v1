<?php
namespace content_api\v1\file;

class model extends \content_api\v1\home\model
{
	use tools\get;
	use tools\link;


	/**
	 * Links an upload.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function link_upload($_args)
	{
		return $this->upload_file();
	}


	/**
	 * Gets the upload.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The upload.
	 */
	public function get_upload($_args)
	{
		return "get";
	}
}
?>