<?php
namespace content_u\question;
use \lib\utility;

class model extends \mvc\model
{

	/**
	 * get users question
	 *
	 * @return     <type>  The question.
	 */
	function get_question()
	{

		// in one page can be display 10 record of polls
		$page   = 1;
		$lenght = 10;

		$user_id = 1;
		$user_id = $this->login('id');

		// set args to load query
		$_args =[
				'user_id'   => $user_id,
				// 'post_type' =>  $user_id,
				// 'post_status' => "draft",
				'page'      => $page,
				'lenght'    => $lenght
				];

		return \lib\db\polls::xget($_args);
	}


	/**
	 * get data to add new question
	 */
	function post_question_add()
	{

		var_dump(utility::post());exit();

		$args = [
				'user_id'     => $this->login('id'),
				'title'        => utility::post("title"),
				'type'         => 'private',
				'language'     => utility::post("language"),
				'content'      => utility::post("content"),
				'publish_date' => utility::post("publish_date"),
				'status'		=> 'draft',
				'answers' 	   => utility::post("answers")
				];


		$result  = \lib\db\polls::insert($args);


		if($result)
		{
			\lib\debug::true(T_("Add Question Success"));
		}
		else
		{
			\lib\debug::error(T_("Error in add question"));
		}

	}


	/**
	 * get one question id and return data of this question
	 * ready for edit form
	 *
	 * @param      <type>  $o      { parameter_description }
	 *
	 * @return     <type>  The question edit.
	 */
	function get_question_edit($o)
	{
		$poll_id = $o->match->url[0][1];
		return \lib\db\polls::get_one($poll_id);
	}


	/**
	 * post edited value of question and update question
	 *
	 * @param      <type>  $o      { parameter_description }
	 */
	function post_question_edit($o){

		$poll_id = $o->match->url[0][1];
			$args = [
				'post_title'       => utility::post("title"),
				'post_language'    => utility::post("language"),
				'post_content'     => utility::post("content"),
				'post_publishdate' => utility::post("publish_date"),
				];

		$result = \lib\db\polls::update($args, $poll_id);

		foreach (utility::post('answers') as $key => $value)
		{
			\lib\db\answers::update(['option_value' => $value], $key);
		}

		if($result)
		{
			\lib\debug::true(T_("Edit Question Success"));
		}
		else
		{
			\lib\debug::error(T_("Error in Edit question"));
		}
	}


	/**
	 * delete question
	 */
	function get_question_delete()
	{

	}
}
?>