<?php
namespace content\saloos_tg\sarshomar_bot\commands;
use \content\saloos_tg\sarshomar_bot\commands\handle;
class make_view
{
	public static $emoji_number = ['0⃣', '1⃣', '2⃣', '3⃣', '4⃣', '5⃣', '6⃣', '7⃣', '8⃣', '9⃣', '🔟'];
	public static $max_emoji_list = 10;
	public $poll_id, $user_id, $query_result;

	/**
	 * @var $_poll poll short link or poll id & if is null get last_poll
	 * @var $_is_poll_id if $_poll is poll id set true else set false
	 *
	**/
	public function __construct($_poll){
		if($_poll){
			if(is_array($_poll))
			{
				$this->query_result = $_poll;
				$this->poll_id = $this->query_result['id'];
			}
			else
			{
				$this->poll_id 		= $_poll;
			}
		}
		$this->get_poll_result();

		$this->set_poll_type();

		$this->message = new make_view\message($this);
		$this->inline_keyboard = new make_view\inline_keyboard($this);
	}

	public function make()
	{
		$disable_web_page_preview = true;
		if(isset($this->query_result['file']))
		{
			$disable_web_page_preview = false;
		}

		$inline_keyboard = $this->inline_keyboard->make();
		$return = [
			"text" => $this->message->make(),

			'parse_mode' 				=> 'HTML',
			'disable_web_page_preview' 	=> $disable_web_page_preview
		];
		if($inline_keyboard)
		{
		$return['reply_markup'] = [
				'inline_keyboard' => $inline_keyboard
			];
		}
		return $return;
	}

	public function set_poll_type()
	{
		$answers = $this->query_result['answers'];
		if(count($answers) >= 1)
		{
			$answer = current($answers);
			$this->poll_type = $answer['type'];
		}
		else
		{
			$this->poll_type = 'select';
		}
	}

	public function get_poll_result()
	{
		if($this->query_result)
		{
			return true;
		}
		if($this->poll_id)
		{
			\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => ['id' => $this->poll_id]]);
			$this->query_result = \lib\main::$controller->model()->poll_get([]);
		}
		else
		{
			\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => []]);
			$this->query_result = \lib\main::$controller->model()->poll_get(['type' => 'ask']);
			$this->poll_id = $this->query_result['id'];
		}
	}
}
?>