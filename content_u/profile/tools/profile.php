<?php
namespace content_u\profile\tools;
use \lib\utility;
use \lib\debug;

trait profile
{
	public function set_profile()
	{
		$post = utility::post();
		if(isset($post['displayname']))
		{
			$post['displayname'] = \lib\utility\safe::safe($post['displayname']);
			\lib\db\users::set_displayname($this->login('id'), $post['displayname']);
		}
		unset($post['displayname']);
		utility::set_request_array($post);

		$method = 'post';

		$saved_data = $this->get_user_profile();
		if(!empty($saved_data))
		{
			$method = 'put';
		}

		$this->set_user_profile(['method' => $method]);
		if(\lib\debug::$status)
		{
			debug::title("");
			debug::true(T_("The profile data was update"));
		}
		return true;

	}
}
?>