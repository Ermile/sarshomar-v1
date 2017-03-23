<?php
namespace content\referer\token;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{
	function post_token()
	{
		$referer_ok = \lib\utility\token::verify(utility::post('token'), (int) $_SESSION['user']['id']);
		if($referer_ok)
		{
			if(isset($referer_ok['meta']['callback_url']))
			{
				$this->redirector($referer_ok['meta']['callback_url'] .'?token=' . utility::post('token'));
				debug::msg('direct', true);
			}
			return true;
		}
	}
}