<?php
namespace content_u\knowledge;

class view extends \mvc\view
{

	/**
	 * view list of poll
	 * for the user
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_list($_args)
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


	/**
	 * find icon in fontawesome
	 *
	 * @param      <type>  $_type  The type
	 *
	 * @return     string  ( description_of_the_return_value )
	 */
	public static function find_icon($_type = null)
	{
		$explode = explode('_', $_type);
		$type = end($explode);
		switch ($type) {

			case 'select':
				$return = "check";
				break;

			case 'notify':
				$return = "bell";
				break;

			case 'text':
				$return = "file-text";
				break;

			case 'upload':
				$return = "upload";
				break;

			case 'star':
				$return = "star";
				break;

			case 'number':
				$return = "list-ol";
				break;

			case 'media_image':
				$return = "picture-o";
				break;

			case 'media_video':
				$return = "file-video-o";
				break;

			case 'media_audio':
				$return = "file-audio-o";
				break;

			case 'order':
				$return = "sort-amount-asc";
				break;

			default:
				$return = "check";
				break;
		}
		return $return;
	}
}
?>