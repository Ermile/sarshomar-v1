<?php
namespace content_u\home;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	/**
	 * Posts a captcha.
	 */
	public function post_captcha()
	{
		if(utility::post("ui-language"))
		{
			return $this->set_ui_language();
		}

		if(utility::post('delete_account'))
		{
			if($this->access('u','delete_account', 'view') && !$this->access('admin','admin', 'view') && $this->login('mobile') && $this->login('id'))
			{
				$this->remove_account();
			}
			else
			{
				return debug::error(T_("Access unavailable to delete your account"));
			}
		}

		$captcha = utility::post("captcha");
		if($captcha)
		{
			if(utility\captcha::check($captcha))
			{
				$signup_inspection = \lib\db\users::signup(['type' => 'inspection', 'port' => 'site_guest']);
				if($signup_inspection)
				{
					\lib\db\users::set_login_session(null, null, $signup_inspection);
					debug::msg("direct", true);
					$this->redirector($this->url("base"). "/@")->redirect();
				}
			}
			else
			{
				debug::error(T_("Invalid captcha"), 'captcha');
				// $url = $this->url('base'). '/features/guest';
				// $this->redirector($url);
			}
		}
	}

	public function remove_account()
	{
		$mobile = $this->login('mobile');
		if(!$mobile)
		{
			return false;
		}
		$first_change_mobile = "SELECT users.id AS `id` FROM users WHERE user_mobile LIKE '$mobile\_1' LIMIT 1";
		$first_change_mobile = \lib\db::get($first_change_mobile, 'id', true);

		if($first_change_mobile)
		{
			$get_count_mobile = "SELECT count(users.id) AS `count` FROM users WHERE user_mobile LIKE '$mobile%' ";
			$get_count_mobile = \lib\db::get($get_count_mobile, 'count', true);
			$get_count_mobile = intval($get_count_mobile);
			$new_mobile       = $mobile . '_'. ($get_count_mobile + 1);
			$user_id          = $this->login('id');

			$query =
			"
			UPDATE users SET
			users.user_mobile = '$new_mobile',
			users.user_status = 'delete'
			WHERE id = $user_id
			LIMIT 1
			";
			\lib\db::query($query);

			$telegram_where =
			[
				'user_id'    => $this->login('id'),
				'option_cat' => 'telegram',
				'option_key' => 'id',
				'limit'      => 1,
			];
			$telegram_id = \lib\db\options::get($telegram_where);
			if(!empty($telegram_id) && isset($telegram_id['value']))
			{
				$new_tg_id  = (string) $telegram_id['value'];
				$new_tg_id .= (string) $new_mobile;
				$new_tg_id .=  (string) rand(1000, 9999);
				unset($telegram_where['limit']);
				\lib\db\options::update_on_error(['options_status' => 'disable', 'option_value' => $new_tg_id], $telegram_where);
			}

			$this->redirector("/logout")->redirect();
		}
		else
		{
			return \lib\debug::error(T_("Access unavailable to run this operation. Please contact administrator"));
		}
	}


	public function set_ui_language()
	{

		$lang = utility::post("ui-language");
		if(!$this->login())
		{
			return debug::error(T_("Please login to set language"));
		}

		if(\lib\utility\location\languages::check($lang))
		{
			$current_lang = \lib\db\users::get_language($this->login("id"));

			if($lang === 'fa' && !$this->view()->data->user_unit)
			{
				\lib\db\units::set_user_unit($this->login('id'), 'toman');
			}

			if($current_lang == $lang)
			{
				return false;
			}

			\lib\db\users::set_language($lang, ['user_id' => $this->login("id")]);

			debug::true(T_("Your default language changed"));

			if(\lib\define::get_language() != $lang)
			{
				if(\lib\define::get_language("default") != $lang)
				{
					$url = $this->url('root'). "/$lang/". $this->url('content');
				}
				else
				{
					$url = $this->url('root'). "/". $this->url('content');
				}
				$this->redirector($url);
				\lib\debug::msg('direct', true);
			}
		}
		else
		{
			debug::error(T_("Invalid language parameter"), 'ui-language');
			return false;
		}

	}

}
?>