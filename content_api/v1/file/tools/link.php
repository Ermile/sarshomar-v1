<?php
namespace content_api\v1\file\tools;
use \lib\utility;
use \lib\debug;
use \lib\utility\upload;

trait link
{
	use check;

	public function upload_file($_options = [])
	{
		debug::title(T_("Can not upload file"));

		$default_options =
		[
			'upload_name' => utility::request('upload_name'),
			'poll_id'     => utility::request('id'),
			'opt'         => utility::request('answer'),
			'url'         => null,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		$file_path = false;

		if($_options['url'])
		{
			$file_path = true;
		}
		elseif(!utility::files($_options['upload_name']))
		{
			return debug::error(T_("Unable to upload, because of selected upload name"), 'upload_name', 'arguments');
		}

		if(!$_options['poll_id'])
		{
			return debug::error(T_("Parameter 'poll' not set"), 'poll', 'arguments');
		}

		$poll_get =
		[
			'check_is_my_poll'   => true,
			'get_filter'         => false,
			'get_opts'           => true,
			'get_options'	     => false,
			'get_public_result'  => false,
			'get_advance_result' => false,
			'type'               => null, // ask || random
		];
		$poll = $this->poll_get($poll_get);

		if(!debug::$status)
		{
			return false;
		}

		// if($_options['opt'])
		// {
		// 	$opt = intval($_options['opt']);
		// 	if(!isset($poll['answers'][$opt - 1]))
		// 	{
		// 		return debug::error(T_("This poll have not option :opt", ['opt' => $opt]), 'opt', 'arguments');
		// 	}
		// }

		$ready_upload            = [];
		$ready_upload['user_id'] = $this->user_id;

		if($file_path)
		{
			$ready_upload['file_path'] = $_options['url'];
		}
		else
		{
			$ready_upload['upload_name'] = $_options['upload_name'];
		}

		$ready_upload['post_status'] = 'awaiting';

		$ready_upload['user_size_remaining'] = self::remaining($this->user_id);

		$upload      = upload::upload($ready_upload);

		if(!debug::$status)
		{
			return false;
		}

		$file_detail = \lib\storage::get_upload();
		$file_id     = null;

		if(isset($file_detail['size']))
		{
			self::user_size_plus($this->user_id, $file_detail['size']);
		}

		if(isset($file_detail['id']) && is_numeric($file_detail['id']))
		{
			$file_id = $file_detail['id'];
		}
		else
		{
			return debug::error(T_("Can not upload file. undefined error"));
		}

		$file_id_code = null;

		if($file_id)
		{
			$file_id_code = utility\shortURL::encode($file_id);
		}

		$url = null;

		if(isset($file_detail['url']))
		{
			$url = Protocol."://" . \lib\router::get_root_domain() . '/'. $file_detail['url'];
		}

		debug::title(T_("File upload completed"));
		return ['code' => $file_id_code, 'url' => $url];
	}
}

?>