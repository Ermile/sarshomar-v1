<?php
namespace content\knowledge;

class view extends \mvc\view
{
	function config()
	{
		// add all template of knowledge into new file
		$this->data->template['knowledge']['layout'] = 'content/knowledge/layout.html';

		$this->data->display['result']     = "content/knowledge/layout-xhr.html";
		// $this->include->css_ermile   = false;
		if($this->module() === 'home')
		{
			$this->include->js_main      = true;
		}
		$this->data->page['special'] = true;
		$this->data->page['title']   = T_('Sarshomar Knowledge');
		$this->data->page['desc']    = T_("Enjoy Sarshomar's comprehensive and valuable knowledge as a valid source in line with your broad objectives");

		$this->user_answered_to_all_poll();

		// $this->data->share['image']   =  $this->url->static. 'images/miniature/knowledge-base.png';
	}

	/**
	 * check notify to user or no
	 */
	public function user_answered_to_all_poll()
	{
		if(isset($_SESSION['user_answered_to_all_poll']) && $_SESSION['user_answered_to_all_poll'] === true)
		{
			unset($_SESSION['user_answered_to_all_poll']);
			$this->data->user_answered_to_all_poll = T_("You have answered to all the polls");
		}

		if(isset($_SESSION['ask_me_limit']) && $_SESSION['ask_me_limit'] === true)
		{
			unset($_SESSION['ask_me_limit']);
			$this->data->user_answered_to_all_poll = T_("You can answer 20 poll in 24 hours");
		}
	}


	/**
	 * show search result
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_search($_args)
	{
		$this->data->isPersonal = false;

		if(\lib\storage::get('rep') == 'u')
		{
			$this->data->isPersonal = true;
			$this->data->my_poll = true;
		}

		$this->data->search_value =  $_args->get("search")[0];
		$list = $_args->api_callback;
		$this->data->poll_list = $list;

		// $match = $_args;
		// unset($_args->match->url);
		// unset($_args->method);
		// unset($_args->match->property);
		// $match  = $match->match;
		// $checkbox = [];
		// foreach ($match as $key => $value) {
		// 	if(is_array($value) && isset($value[0]))
		// 	{
		// 		$value = $value[0];
		// 	}
		// 	$checkbox[$key] = $value;
		// }
		// $this->data->checkbox = $checkbox;
	}


	/**
	 * [pushState description]
	 * @return [type] [description]
	 */
	function pushState()
	{
		if(\lib\utility::get('onlySearch'))
		{
			$this->data->display['main'] = "content/knowledge/layout-xhr.html";
		}
	}
}
?>