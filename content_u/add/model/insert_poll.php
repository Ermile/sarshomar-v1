<?php
namespace content_u\add\model;
use \lib\utility;
use \lib\debug;

trait insert_poll
{

	/**
	 * search in $_POST
	 * and return all answer data in post
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	function answers_in_post()
	{
		$answers      = [];
		$i = 0;
		$max_post = count(utility::post());

		for ($j = 0; $j <= $max_post; $j++)
		{
			if(utility::post("answer$j"))
			{
				$i++;
				$answers[$i]['txt']         = utility::post("answer$j");
				$answers[$i]['true']        = (utility::post("true$j")  != '') 		? utility::post("true$j")  		: null;
				$answers[$i]['type']        = (utility::post("type$j")  != '') 		? utility::post("type$j")  		: null;
				$answers[$i]['score']       = (utility::post("score$j") != '') 		? utility::post("score$j") 		: null;
				$answers[$i]['desc']        = (utility::post("desc$j")  != '') 		? utility::post("desc$j")  		: null;
				$answers[$i]['file']        = (utility::post("saved-file$j") != '') ? utility::post("saved-file$j") : null;
				$answers[$i]['upload_name'] = (utility::files("file$j")) ? "file$j" : null;
			}
		}
		return $answers;
	}


	/**
	 * insert poll
	 * get data from utility::post()
	 *
	 * @param      array    $_options  The options
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function insert_poll($_options = [])
	{
		// insert args
		$args = [];
		$args['user']                 = $this->login('id');
		$args['language']             = \lib\define::get_language();
		$args['type']                 = utility::post("poll_type");
		$args['title']                = utility::post("title");
		$args['upload_name']          = (utility::files("file_title")) ? "file_title" : null;
		$args['description']          = utility::post("description");
		$args['summary']              = utility::post("summary");
		$args['tree']                 = utility::post("parent_tree_id");
		$args['tree_answers']         = utility::post("parent_tree_opt");
		$args['permission_sarshomar'] = $this->sarshomar;
		$args['update']               = $this->update;
		// get the answers in $_POST
		$args['answers']              = $this->answers_in_post();
		$args['permission_profile']   = $this->access('u', 'complete_profile', 'admin');

		$options = [];
		foreach (utility::post() as $key => $value)
		{
			if(substr($key, 0, 5) == 'meta_')
			{
				$options[substr($key, 5)] = $value;
			}
		}
		$args['options'] = $options;
		return \lib\db\polls::create($args);
		// // check the suevey id to set in post_parent
		// if(isset($_options['survey_id']) && $_options['survey_id'])
		// {
		// 	$survey_id = $_options['survey_id'];
		// }
		// else
		// {
		// 	$survey_id = null;
		// }
	}
}
?>