<?php
namespace content\contact;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{

	/**
	 * save contact form
	 */
	public function post_contact()
	{
		// check login
		if($this->login())
		{
			$user_id     = $this->login("id");
			// get display name from user profile
			$displayname = $this->login("displayname");
			// user not set users display name, we get display name from contact form
			if(!$displayname)
			{
				$displayname = utility::post("name");
			}
			// get email from user profile
			$email = \lib\db\users::get_email($user_id);
			// user not set users email, we get email from contact form
			if(!$email)
			{
				$email = utility::post("email");
			}
		}
		else
		{
			// users not registered
			$user_id     = null;
			$displayname = utility::post("name");
			$email       = utility::post("email");
		}
		// get the content
		$content = utility::post("content");
		// check content
		if($content == '')
		{
			debug::error(T_("Please fill the content box"), "content");
			return false;
		}
		// ready to insert comments
		$args =
		[
			'comment_author'  => $displayname,
			'comment_email'   => $email,
			'comment_type'    => 'comment',
			'comment_content' => $content,
			'user_id'         => $user_id
		];
		// insert comments
		$result = \lib\db\comments::insert($args);
		if($result)
		{
			debug::true(T_("Thank You For contact to us"));
		}
		else
		{
			debug::error(T_("We could not save contact"));
		}
	}
}