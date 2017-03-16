<?php
namespace content\enter;

class view extends \mvc\view
{

	/**
	 * config
	 */
	public function config()
	{
		$this->include->css    = true;
		$this->include->js     = false;
		$this->data->bodyclass = 'unselectable';
	}

	/**
	 * view enter
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_enter($_args)
	{
		$mobile = \lib\utility::get('mobile');
		if($mobile)
		{
			$this->data->get_mobile = \lib\utility\filter::mobile($mobile);
		}
	}
}
?>