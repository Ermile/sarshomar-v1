<?php
namespace content\knowledge;

class model extends \mvc\model
{
	public function get_all()
	{
		echo ("All Questions is here || random result is here or other");
	}

	public function get_poll($_args)
	{
		if(isset($_args->match->url[0]))
		{
			$url = $_args->match->url[0];
		}
		else
		{
			$url = [];
		}

		if(isset($url[1]))
		{
			$sp_ = $url[1];
		}
		else
		{
			$sp_ = null;
		}

		if(isset($url[2]))
		{
			$short_url = $url[2];
		}
		else
		{
			$short_url = null;
		}


		if(isset($url[3]))
		{
			$title = $url[3];
		}
		else
		{
			$title = null;
		}
		if($sp_ == "sp_")
		{
			$poll_id = \lib\utility\shortURL::decode($short_url);
		}
	}
}
?>