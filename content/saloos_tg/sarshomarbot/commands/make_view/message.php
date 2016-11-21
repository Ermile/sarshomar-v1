<?php
namespace content\saloos_tg\sarshomarbot\commands\make_view;
class message
{
	public $message = array();
	public function __construct($make_class)
	{
		$this->make_class = $make_class;
	}

	public function add_title($_with_link = true)
	{
		if($_with_link)
		{
			$title = '[' . html_entity_decode($this->make_class->query_result['title']) . ']' .
			'(' . $this->make_class->short_link .')';
		}
		else
		{
			$title = html_entity_decode($this->make_class->query_result['title']);
		}
		$this->message['title'] = $title;
	}
}
?>