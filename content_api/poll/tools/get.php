<?php 
namespace content_api\poll\tools;

trait get
{
	/**
	 * Gets the poll.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     array   The poll.
	 */
	public function get($_args)
	{
		$result  = [];
		$poll_id = $this->check_poll_url($_args);

		if(!$poll_id)
		{
			$poll_id = \lib\utility::request("id");
		}

		if(!$poll_id)
		{
			\lib\debug::error(T_("poll id not found"));
		}

		$poll    = \lib\db\polls::get_poll($poll_id);

		$options = 
		[
			'get_filter' => true,
			'get_opts'   => true,
		];
		$result = $this->ready_poll($poll, $options);

		return $result;
	}
}

?>