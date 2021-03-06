<?php
namespace content_api\v1\home\tools;
use \lib\utility;
use \lib\debug;
use \lib\utility\shortURL;

trait ready
{
	public static $private_user_id;
	public static $private_poll_id;
	public static $private_is_my_poll = false;
	public static $current_language;
	public static $_options;
	public static $host;

	use api_options;
	use get_fields;
	use get_options;
	use get_answers;
	use get_transactions;
	use get_members;

	/**
	 * ready poll record to show
	 * encode id
	 * remove null index
	 * some thing more...
	 * @param      <type>  $_poll_data  The poll data
	 * @param      array   $_options    The options
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function poll_ready($_poll_data, $_options = [])
	{
		if(!is_array($_poll_data))
		{
			return false;
		}

		// set user id in static
		self::$private_user_id = $this->user_id;

		$default_options =
		[
			'get_tags'           => true,
			'get_filter'         => false,
			'get_opts'           => false,
			'get_options'        => false,
			'get_public_result'  => false,
			'get_advance_result' => false,
			'run_options'        => true,
			'check_is_my_poll'   => false,
			'debug'              => true,
			'filter_chart'       => null,
			'load_in_site'       => false,
		];
		// merge settings
		$_options       = array_merge($default_options, $_options);
		self::$_options = $_options;
		$poll_id        = false;

		// encode id
		if(array_key_exists('id', $_poll_data))
		{
			self::$private_poll_id = $poll_id = $_poll_data['id'];
			$_poll_data['id'] = shortURL::encode($_poll_data['id']);
		}

		// check id
		if(!$poll_id)
		{
			if($_options['debug'])
			{
				debug::error(T_("Poll not found"), "id", 'arguments');
			}
			return;
		}

		$my_poll = false;
		if(array_key_exists('user_id', $_poll_data))
		{
			if($this->user_id == $_poll_data['user_id'])
			{
				$my_poll = true;
			}
		}

		/**
		 * load some ting because is my poll
		 * for example load the password poll
		 */
		if($my_poll || self::check_api_permission('admin:admin:view', null, $this->user_id))
		{
			self::$private_is_my_poll = true;
		}

		if($_options['check_is_my_poll'] === true && !$my_poll && !self::check_api_permission('admin:admin:view', null, $this->user_id))
		{
			if($_options['debug'])
			{
				debug::error(T_("Access denied to the poll (This is not your poll)"), "id", 'permission');
			}
			return;
		}
		if(array_key_exists('status', $_poll_data))
		{
			$msg = null;
			$permission_load_poll = false;
			switch ($_poll_data['status'])
			{

				case 'draft':
				case 'awaiting':
					if($my_poll || utility::get('preview'))
					{
						$permission_load_poll = true;
					}
					break;

				case 'trash':
					if($my_poll)
					{
						$permission_load_poll = true;
					}
					break;

				case 'publish':
				case 'stop':
				case 'pause':
				case 'schedule':
				case 'expired':
					$permission_load_poll = true;
					break;

				case 'deleted':
				case 'filtered':
				case 'blocked':
				case 'spam':
				case 'violence':
				case 'pornography':
				case 'disable':
					$permission_load_poll = false;
					if($my_poll)
					{
						$msg = T_("(The poll is :status)", ['status' => $_poll_data['status']]);
					}
					break;

				case 'other':
				case 'enable':
				default:
					$permission_load_poll = false;
					break;
			}

			if(!$permission_load_poll && !self::check_api_permission('admin', null, $this->user_id))
			{
				if($_options['debug'])
				{
					debug::error(T_("Access denied to load this poll :msg",['msg' => $msg]), "id", 'permission');
				}
				return;
			}
		}
		else
		{
			if($_options['debug'])
			{
				debug::error(T_("Invalid parameter status"), 'status', 'system');
			}
			return;
		}

		self::$current_language = $current_language = \lib\define::get_language();

		/**
		 * fix field data
		 */
		self::get_fields($_poll_data);

		// get opts of poll
		if($_options['get_opts'] && $poll_id)
		{
			self::get_answers($_poll_data);
		}

		// get filters of poll
		if($_options['get_filter'] && $poll_id)
		{
			$filters = utility\postfilters::get_filter($poll_id);
			$filters = array_filter($filters);
			// $member  = \lib\db\ranks::get($poll_id, 'member');
			$filters['count'] = (int) $_poll_data['member'];
			unset($_poll_data['member']);
			$_poll_data['filters'] = $filters;
		}

		if(($_options['get_public_result'] || $_options['get_advance_result']) && $poll_id)
		{
			$get_chart_args = [];
			if($_options['filter_chart'])
			{
				$get_chart_args = ['filter' => $_options['filter_chart']];
			}

			$poll_result = utility\stat_polls::get_result($poll_id, $get_chart_args);
			$_poll_data['result'] = $poll_result;
		}

		if($_options['get_options'])
		{
			self::get_options($_poll_data);
		}

		if($_options['get_tags'])
		{
			$tag = \lib\db\terms::usage($poll_id, [], 'tag', 'sarshomar_tag');
			$new_tag = [];

			if($tag && is_array($tag))
			{
				foreach ($tag as $key => $value)
				{
					if(isset($value['term_title']) && isset($value['id']) && isset($value['term_url']))
					{
						$code = shortURL::encode($value['id']);
						$new_tag[$key]['code']  = $code;
						$new_tag[$key]['title'] = $value['term_title'];
						$new_tag[$key]['url']   = self::host('with_language'). '/'. $value['term_url'];
					}
				}
			}
			$_poll_data['tags'] = $new_tag;
		}

		$short_url = self::host('without_language'). '/$'. $_poll_data['id'];
		$_poll_data['short_url'] = $short_url;

		if($_options['run_options'])
		{
			self::get_transactions($_poll_data);
		}

		// unset useless field
		self::unset_useless_field($_poll_data);

		// sort poll data
		krsort($_poll_data);

		// var_dump($_poll_data, func_get_args()); exit();
		return $_poll_data;
	}
}
?>