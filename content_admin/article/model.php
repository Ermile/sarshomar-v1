<?php
namespace content_admin\article;
use \lib\utility;

class model extends \mvc\model
{

	/**
	 * get article data to show
	 */
	public function get_article()
	{
		$args =
		[
		'post_type' => 'article'
		];

		return \lib\db\polls::search(null, $args);
	}


	/**
	 * post data and update or insert article data
	 */
	public function post_article()
	{

	}
}
?>