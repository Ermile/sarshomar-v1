<?php
namespace content_u\add\publish;

class view extends \content_u\home\view
{

	/**
	 * ready to load publish page
	 * get the cat and get the article
	 *
	 * @param      <type>  $_args  The arguments
	 */
	function view_publish($_args)
	{
		$this->data->step =
		[
			'current' => 'publish',
			'add'     => true,
			'filter'  => true,
			'publish' => true
		];
		$this->include->fontawesome = true;

		// set the short url to data
		$poll = $_args->api_callback;
		$this->data->short_url = isset($poll['url']) ? $poll['url'] : '';

		if($this->access('u', 'sarshomar_knowledge', 'add'))
		{
			// get all cat_poll from terms
			$this->data->cat = \lib\db\cats::get_multi("cat_poll");
			// get article
			$args =	['post_type' => 'article'];
			$this->data->article = \lib\db\polls::search(null, $args);

		}
		if(isset($poll['id']))
		{
			$this->data->tags = \lib\db\tags::usage($poll['id']);
			$this->page_progress_url($poll['id'], "publish");
		}
		$this->data->poll = $poll;
	}
}
?>