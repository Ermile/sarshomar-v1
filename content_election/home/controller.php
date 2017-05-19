<?php
namespace content_election\home;

class controller extends \content_election\main\controller
{
	public function _route()
	{
		parent::_route();

		$url = \lib\router::get_url();

		if($id = $this->model()->check_url($url))
		{
			$this->get("load", "load")->ALL("/.*/");
		}
		else
		{
			switch ($url)
			{
				case 'election':
				case 'انتخابات':
				case 'انتخاب':
				case '':
					$this->redirector($this->url('base'). '/election/iran')->redirect();
					return;
					# code...
					break;
				case 'iran':
					$this->redirector($this->url('base'). '/election/iran/president')->redirect();
					return;
					break;
				default:
					# code...
					break;
			}

			$this->get('home', 'home')->ALL("/iran\/president/");
			$this->display_name = 'content_election\home\home.html';
		}
	}
}
?>