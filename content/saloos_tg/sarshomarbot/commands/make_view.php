<?php
namespace content\saloos_tg\sarshomarbot\commands;
use \content\saloos_tg\sarshomarbot\commands\handle;
class make_view
{
	public $short_link, $poll_id, $user_id, $query_result;

	/**
	 * @var $_poll poll short link or poll id & if is null get last_poll
	 * @var $_is_poll_id if $_poll is poll id set true else set false
	 *
	**/
	public function __construct($_user_id, $_poll = null, $_is_poll_id = false){
		if($_poll){
			if(!$_is_poll_id)
			{
				$this->poll_id 		= \lib\utility\shortURL::decode($_poll);
				$this->short_link 	= $_poll;
			}
			else
			{
				$this->poll_id 		= $_poll;
				$this->short_link 	= \lib\utility\shortURL::encode($_poll);
			}
		}
		elseif ($_user_id) {
			$this->user_id = $_user_id;
		}
		$this->get_poll_result();
		$this->message = new make_view\message($this);
		$this->inline_keyboard = new make_view\inline_keyboard($this);
	}

	/**
	 * @var $_user_id if need get last poll this var must be not null
	 **/
	public function get_poll_result()
	{
		if(is_int($this->poll_id))
		{
			$this->query_result = \lib\db\polls::get_poll($this->poll_id);
		}
		else
		{
			$this->query_result = \lib\db\polls::get_last($this->user_id);
			$this->poll_id = $this->query_result['id'];
			$link_paths = preg_split("[\/]", $this->query_result['url']);
			$this->short_link = $link_paths[1];
		}
	}
}
?>