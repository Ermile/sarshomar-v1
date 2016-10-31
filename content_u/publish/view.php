<?php
namespace content_u\publish;

class view extends \mvc\view
{

	/**
	 * ready to load publish page
	 * get the cat and get the article
	 *
	 * @param      <type>  $_args  The arguments
	 */
	function view_publish($_args)
	{
		// set the short url to data
		$this->data->short_url = $_args->api_callback;
		// get all cat_poll from terms
		$this->data->cat = \lib\db\cats::get_multi("cat_poll");
		// get article
		$args =	['post_type' => 'article'];
		$this->data->article = \lib\db\polls::xget($args);
	}
}
?>