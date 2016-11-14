<?php
namespace content\knowledge;

class view extends \mvc\view
{
	function config()
	{
		// $this->include->css_ermile   = false;
		$this->include->js    = true;
		$this->include->chart = true;
		if($this->module() === 'home')
		{
			$this->include->js_main      = true;
		}

		$this->data->stat = T_(":number Questions answered", ["number"=>\lib\utility\stat_polls::get_sarshomar_total_answered()]);
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
	}
}
?>