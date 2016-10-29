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

		$this->data->stat = T_(":number Questions answered", ["number"=>\lib\db\stat_polls::get_sarshomar_total_answered()]);
		$this->include->fontawesome = true;
	}


	public function view_all()
	{
		$list = \lib\db\polls::xget();
		if($list)
		{
			// get fontawesome class
			foreach ($list as $key => $value)
			{
				$list[$key]['type'] = \content_u\knowledge\view::find_icon($value['type']);
			}
			$this->data->poll_list = $list;
		}
	}
}
?>