<?php
namespace content_admin\attachments;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	public function post_accept()
	{
		if(utility::post('ids') && is_array(utility::post("ids")))
		{
			$ids = implode(',', utility::post('ids'));
			$query = "UPDATE posts SET post_status = 'publish' WHERE id IN ($ids) AND post_status = 'awaiting' ";
			\lib\db::query($query);
			$row_affected = \lib\db::changed();
			debug::true(T_(":count rows changed on status publish", ['count' => (int) $row_affected]));
			return;
		}

	}


	/**
	 * get log data to show
	 */
	public function get_attachments($_args)
	{
		$meta   = [];
		$search = null;
		if(isset($_args->get("search")[0]))
		{
			$search = $_args->get("search")[0];
		}

		if(isset($_args->get("sort")[0]))
		{
			$meta['sort'] = $_args->get("sort")[0];
		}

		if(isset($_args->get("order")[0]))
		{
			$meta['order'] = $_args->get("order")[0];
		}

		if(isset($_args->get("status")[0]))
		{
			$meta['post_status'] = $_args->get("status")[0];
		}

		$meta['post_type']      = 'attachment';
		$meta['limit']          = 30;
		$meta['check_language'] = false;
		$meta['sort']           = 'id';
		$meta['order']          = 'desc';
		$result = \lib\db\posts::search($search, $meta);

		return $result;
	}


	/**
	 * post data and update or insert log data
	 */
	public function post_attachments()
	{

	}

	public function get_show($_args)
	{
		var_dump(utility::post());exit();
	}


	/**
	 * change post status
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_show($_args)
	{
		if(utility::post('status') && utility::post('id') && is_numeric(utility::post('id')))
		{
			$update = \lib\db\posts::update(['post_status' => utility::post('status')], utility::post('id'));
			if($update)
			{
				debug::true(T_("Post status change on :status", ['status' => utility::post('status')]));
			}
			else
			{
				debug::error(T_("Can not change post status"));
			}

			if(utility::post('status') === 'deleted')
			{
				$md5_id = md5(utility::post('id'));
				$id = utility::post('id');
				$get_post = \lib\db\posts::get_one($id);
				if(isset($get_post['post_meta']['url']))
				{
					$file_url  = $get_post['post_meta']['url'];
					$base_name = basename($file_url);
					$folder    = preg_replace("/(". $base_name . ")$/", '', $file_url);
					$ext       = pathinfo($file_url, PATHINFO_EXTENSION);
					$file_name = preg_replace("/\.". $ext ."$/", '', $base_name);

					$old_url   = root.'public_html/'. $file_url;
					$new_url_raw = $folder .  $file_name .'-'. $md5_id .'.'. $ext;
					$new_url   = root.'public_html/'. $new_url_raw;
					if(\lib\utility\file::rename($old_url, $new_url))
					{
						debug::warn(T_("The file has moved in new url"));
						$new_post_meta = array_merge($get_post['post_meta'], ['delete_url' => $new_url_raw]);
						\lib\db\posts::update(['post_meta' => $new_post_meta], utility::post('id'));
					}
					else
					{
						debug::error(T_("Can not move file to new url"));
					}
				}
			}
		}

	}
}
?>