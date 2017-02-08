<?php
namespace content_api\v1\file\tools;
use \lib\utility;
use \lib\debug;
use \lib\utility\upload;

trait link
{
	public function upload_file($_options = [])
	{
		debug::title(T_("Can not upload file"));

		$default_options =
		[
			'upload_name' => utility::request('upload_name'),
			'poll_id'     => utility::request('poll_id'),
			'opt'         => utility::request('opt'),
		];

		$_options = array_merge($default_options, $_options);

		if(!utility::files($_options['upload_name']))
		{
			return debug::error(T_("File not upload in upload_name"), 'upload_name', 'arguments');
		}

		if(!$_options['poll_id'])
		{
			return debug::error(T_("Parameter poll not set"), 'poll', 'arguments');
		}

		utility::set_request_array(['id' => $_options['poll_id']]);

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
			return;
		}

		if($_options['opt'])
		{
			$opt = intval($_options['opt']);
			if(!isset($poll['answers'][$opt - 1]))
			{
				return debug::error(T_("This poll have not opt :opt", ['opt' => $opt]), 'opt', 'arguments');
			}
		}

		$ready_upload =
		[
			'upload_name' => $_options['upload_name'],
			'user_id'     => $this->user_id,
		];

		$upload      = upload::upload($ready_upload);

		$file_detail = \lib\storage::get_upload();
		$file_id     = null;

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

		debug::title(T_("File upload complete"));
		return ['code' => $file_id_code, 'url' => $url];
	}
}

?>