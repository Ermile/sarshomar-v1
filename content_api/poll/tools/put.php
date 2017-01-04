<?php 
namespace content_api\poll\tools;

trait put
{
	
	/**
	 * Puts a poll.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function put_poll($_args)
	{
		$url       = $_args->get("url");
		$update_id = false;
		if(isset($url[0][1]))
		{
			$update_id = $url[0][1];
		}
		return $this->add($_args, ['update' => $update_id]);
	}

}

?>