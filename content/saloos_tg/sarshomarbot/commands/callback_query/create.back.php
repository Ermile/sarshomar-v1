<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \content\saloos_tg\sarshomarbot\commands\utility;
use content\saloos_tg\sarshomarbot\commands\make_view;
use \lib\telegram\step;
use \content\saloos_tg\sarshomarbot\commands\menu;

class create
{
	public static function start($_query, $_data_url)
	{
		if(count($_data_url) > 1)
		{
			$method = $_data_url[1];
			$return = self::$method($_query, $_data_url);
		}
		if(is_array($return))
		{
			return $return;
		}
		return [];
	}

	public static function home($_query = null, $_data_url = null){
		$txt_text = "📍 " . T_("Enter question's title");
		$txt_text .= "\n\n";
		$txt_text .= "✳ " . T_("send /cancel on each step to cancel operation.");
		$txt_text .= "\n" . utility::tag(T_("Create new poll"));
		$result   =
		[
			'text'         => $txt_text,
			'reply_markup' => [
				"remove_keyboard" => true
			]
		];
		return $result;
	}

	public static function cancel($_query = null, $_data_url = null)
	{
		session::remove_back('expire', 'inline_cache', 'create');
		session::remove('expire', 'inline_cache', 'create');
		step::stop();
		if($_query)
		{
			callback_query::edit_message(['text' => utility::tag(T_("Add poll canceled"))]);
			return [];
		}
		return null;
	}

	public static function close()
	{
		step::stop();
		session::remove('poll');
		bot::sendResponse(['text' => T_("Return to main menu"), 'reply_markup' => menu::main(true)]);
	}

	public static function back()
	{
		step::stop();
		session::remove('poll');
	}

	public static function type($_query, $_data_url)
	{
		session::set('poll_options' , 'type', $_data_url[2]);
		session::remove_back('expire', 'inline_cache', 'create');
		$poll_request = ['id' => session::get('poll'), 'answers' => [["type" => $_data_url[2]]]];
		if($_data_url[2] == 'like')
		{
			$poll_request['answers'][0]['title'] = T_("Do you like it!");
		}
		elseif($_data_url[2] == 'descriptive')
		{
			$poll_request['answers'][0]['title'] = T_("Please type your answer");
		}
		\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => $poll_request]);
		$poll_type_change = \lib\main::$controller->model()->poll_add(['method' => 'patch']);
		callback_query::edit_message(\content\saloos_tg\sarshomarbot\commands\step_create::make_draft(session::get('poll')));
	}

	public static function remove_type($_query, $_data_url)
	{
		\lib\storage::set_disable_edit(true);
		session::remove('poll_options', 'type');
		$poll_request = ['id' => session::get('poll'), 'answers' => []];
		\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => $poll_request]);
		\lib\main::$controller->model()->poll_add(['method' => 'patch']);
		callback_query::edit_message(\content\saloos_tg\sarshomarbot\commands\step_create::make_draft(session::get('poll')));

	}

	public static function edit_title($_query, $_data_url)
	{
		\lib\storage::set_disable_edit(true);
		$poll_request = ['id' => session::get('poll'), 'title' => ''];
		\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => $poll_request]);
		$change = \lib\main::$controller->model()->poll_add(['method' => 'patch']);
		callback_query::edit_message(\content\saloos_tg\sarshomarbot\commands\step_create::make_draft(session::get('poll')));

	}

	public static function access_profile($_query, $_data_url)
	{
		\lib\storage::set_disable_edit(true);
		$poll_request = ['id' => session::get('poll')];
		if($_data_url[2] == 'add')
		{
			$poll_request['access_profile'] = ['displayname'];
		}
		else
		{
			$poll_request['access_profile'] = null;
		}
		\lib\utility::$REQUEST = new \lib\utility\request(['method' => 'array', 'request' => $poll_request]);
		$change = \lib\main::$controller->model()->poll_add(['method' => 'patch']);
		callback_query::edit_message(\content\saloos_tg\sarshomarbot\commands\step_create::make_draft(session::get('poll')));
	}
}
?>