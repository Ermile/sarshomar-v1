<?php
namespace content_api\v1\file;

class controller extends  \content_api\v1\home\controller
{

	public function route_file()
	{
		/**
		 * link to upload
		 */
		$this->link("upload")->ALL("v1/file");

		/**
		 * get to load upload details
		 */
		$this->get("upload")->ALL("v1/file");

	}
}
?>