<?php
namespace mvc;

class view extends \lib\mvc\view
{
	function _construct()
	{
		// define default value for global

		$this->data->site['title']   = T_("Sarshomar");
		$this->data->site['desc']    = T_("Distinct Attitude");
		$this->data->site['slogan']  = T_("Ask Anyone Anywhere");

		$this->data->page['desc']    = T_("Sarshomar is intelligent");

		$this->data->template['register']    = 'content/template/register.html';

		// if(! ($this->url('sub') === 'cp' || $this->url('sub') === 'account') )
		// 	$this->url->MainStatic       = false;

		/*
		// add language list for use in display
		$this->global->langlist		= array(
												'fa_IR' => 'فارسی',
												'en_US' => 'English',
												'de_DE' => 'Deutsch'
												);


		// if you need to set a class for body element in html add in this value
		$this->data->bodyclass      = null;
		*/

		if(method_exists($this, 'options')){
			$this->options();
		}
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