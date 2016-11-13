<?php
namespace content_admin\knowledge;

class view extends \mvc\view
{

	/**
	 * view list of poll
	 * for the user
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_search($_args)
	{
		$this->include->fontawesome = true;

		$poll_list = $_args->api_callback;

		$new_poll_list = [];
		foreach ($poll_list as $key => $value) {
			$new_poll_list[$key]['id']            = $value['id'];
			$new_poll_list[$key]['url']           = $value['url'];
			$new_poll_list[$key]['title']         = $value['title'];
			$new_poll_list[$key]['total']         = $value['total'];
			$new_poll_list[$key]['status']        = $value['status'];
			$new_poll_list[$key]['type']          = self::find_icon($value['type']);
			$new_poll_list[$key]['date_modified'] = date("Y-m-d", strtotime($value['date_modified']));
		}
		$this->data->poll_list = $new_poll_list;
	}

}
?>