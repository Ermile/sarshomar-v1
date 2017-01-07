<?php
namespace content\saloos_tg\sarshomar_bot\commands;
use \content\saloos_tg\sarshomar_bot\commands\handle;
class make_view
{
	public static $emoji_number = ['0⃣', '1⃣', '2⃣', '3⃣', '4⃣', '5⃣', '6⃣', '7⃣', '8⃣', '9⃣', '🔟'];
	public $short_link, $poll_id, $user_id, $query_result;

	/**
	 * @var $_poll poll short link or poll id & if is null get last_poll
	 * @var $_is_poll_id if $_poll is poll id set true else set false
	 *
	**/
	public function __construct($_user_id, $_poll = null, $_is_poll_id = false){
		$this->user_id = $_user_id;
		if($_poll){
			if(is_array($_poll))
			{
				$this->query_result = $_poll;
				if(isset($this->query_result['id']))
				{
					$this->poll_id = $this->query_result['id'];
					$link_paths = preg_split("[\/]", $this->query_result['url']);
					$this->short_link = $link_paths[1];
				}
				else
				{
					$this->poll_id = 0;
					$this->short_link = 0;
				}
			}
			elseif(!$_is_poll_id)
			{
				$this->poll_id 		= \lib\utility\shortURL::decode((string) $_poll);
				$this->short_link 	= (string) $_poll;
			}
			else
			{
				$this->poll_id 		= $_poll;
				$this->short_link 	= \lib\utility\shortURL::encode($_poll);
			}
		}
		$this->get_poll_result();
		$this->message = new make_view\message($this);
		$this->inline_keyboard = new make_view\inline_keyboard($this);
	}

	public function make()
	{
		$disable_web_page_preview = true;
		if(isset($this->query_result['meta']) && isset($this->query_result['meta']['attachment_id']))
		{
			$disable_web_page_preview = false;
		}
		return [
			"text" => $this->message->make(),
			'reply_markup' => [
				'inline_keyboard' => $this->inline_keyboard->make()
			],
			'parse_mode' 				=> 'HTML',
			'disable_web_page_preview' 	=> $disable_web_page_preview
		];
	}

	/**
	 * @var $_user_id if need get last poll this var must be not null
	 **/
	public function get_poll_result()
	{
		if($this->query_result)
		{
			return true;
		}
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