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
			'poll'        => utility::request('poll'),
			'opt'         => utility::request('opt'),
		];

		$_options = array_merge($default_options, $_options);

		if(!utility::files($_options['upload_name']))
		{
			return debug::error(T_("File not upload in upload_name"), 'upload_name', 'arguments');
		}

		if(!$_options['poll'])
		{
			return debug::error(T_("Parameter poll not set"), 'poll', 'arguments');
		}

		// $poll_id =

		$ready_upload =
		[
			'upload_name' => $_options['upload_name'],
			'user_id'     => $this->user_id,
		];
		$upload = upload::upload($ready_upload);
		$file_id = \lib\storage::get_upload();
		if(isset($file_id['id']) && is_numeric($file_id['id']))
		{
			$file_id = $file_id['id'];
		}
		else
		{
			return debug::error(T_("Can not upload file. undefined error"));
		}

		$file_id_code = utility\shortURL::encode($file_id);

		debug::msg("file_code", $file_id_code);


	}
}

?>