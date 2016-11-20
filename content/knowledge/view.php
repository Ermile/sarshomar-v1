<?php
namespace content\knowledge;

class view extends \mvc\view
{
	function config()
	{
		$this->data->display['result']     = "content/knowledge/layout-xhr.html";
		// $this->include->css_ermile   = false;
		$this->include->js    = true;
		$this->include->chart = true;
		if($this->module() === 'home')
		{
			$this->include->js_main      = true;
		}
		$this->include->fontawesome = true;
	}


	/**
	 * show search result
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_search($_args)
	{

		$this->data->search_value =  $_args->get("search")[0];
		// get fontawesome class
		$list = $_args->api_callback;
		if($list && is_array($list))
		{
			foreach ($list as $key => $value)
			{
				$list[$key]['type'] = \content_u\knowledge\view::find_icon($value['type']);
			}
		}
		$this->data->poll_list = $list;
		$match = $_args;
		unset($_args->match->url);
		unset($_args->method);
		unset($_args->match->property);
		$match  = $match->match;
		$checkbox = [];
		foreach ($match as $key => $value) {
			if(is_array($value) && isset($value[0]))
			{
				$value = $value[0];
			}
			$checkbox[$key] = $value;
		}
		$this->data->checkbox = $checkbox;
	}

	/**
	 * [pushState description]
	 * @return [type] [description]
	 */
	function pushState()
	{
		if($this->module() !== 'home')
		{
			$this->data->display['result']     = "content/knowledge/layout-xhr.html";
		}
	}
}
?>