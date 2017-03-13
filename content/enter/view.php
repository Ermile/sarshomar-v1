<?php
namespace content\enter;

class view extends \mvc\view
{
	public function config()
	{
		$this->include->css            = true;
		$this->data->bodyclass         = 'unselectable';
	}
}
?>