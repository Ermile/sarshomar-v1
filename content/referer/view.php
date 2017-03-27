<?php
namespace content\referer;

class view extends \content\home\view
{
	function config()
	{

		// $this->data->page['title'] = 'Help Center';
		// $this->data->page['title'] = 'Help Center';
	}

	public function view_ref($_args)
	{
		$result = $_args->api_callback;
		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				$this->data->$key = $value;
			}
		}
	}
}
?>