<?php
namespace content\enter;

class view extends \mvc\view
{

	/**
	 * config
	 */
	public function config()
	{
		$this->include->css            = true;
		$this->data->bodyclass         = 'unselectable';
	}

	/**
	 * view enter
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_enter($_args)
	{

	}
}
?>