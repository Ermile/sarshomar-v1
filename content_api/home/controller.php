<?php
namespace content_api\home;

class controller extends  \mvc\controller
{	
	public function __construct()
	{
		\lib\storage::set_api(true);
		parent::__construct();
	}

	/**
	 * route url like this:
	 * post > poll/ to add poll
	 * get poll/[shorturl] to get poll
	 * put poll/[shorturl] to edit poll
	 * delete poll/[shorturl] to delete poll
	 */
	public function _route()
	{
		$url = \lib\router::get_url(0);
		
		switch ($url) 
		{
			case 'addPoll':
			case 'getPoll':
			case 'editPoll':
			case 'removePoll':
				\lib\router::set_controller("\\content_api\\poll\\controller");
				return;
				break;

			case 'addFile':
			case 'getFile':
				\lib\router::set_controller("\\content_api\\file\\controller");
				return;
				break;

			case 'addAnswer':
			case 'getAnswer':
			case 'editAnswer':
				\lib\router::set_controller("\\content_api\\answer\\controller");
				return;
				break;

			case 'sendFeedback':
				\lib\router::set_controller("\\content_api\\feedback\\controller");
				return;
				break;

			case 'search':
				\lib\router::set_controller("\\content_api\\search\\controller");
				return;
				break;

			case 'fav':
				\lib\router::set_controller("\\content_api\\favorites\\controller");
				return;
				break;

			case 'like':
				\lib\router::set_controller("\\content_api\\like\\controller");
				return;
				break;

			default:
				\lib\error::page("API PAGE NOT FOUND");
				return;
				break;
		}

	}
}
?>