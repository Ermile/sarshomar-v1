<?php 
namespace content_api\home;

class model extends \mvc\model
{
	public function ready_poll($_poll_data, $_options = [])
	{
		$default_options = 
		[
			'get_filter' => false,
			'get_opts'   => false,	
		];

		$_options = array_merge($default_options, $_options);

		$poll_id = 0;

		if(isset($_poll_data['id']))
		{
			$poll_id          = $_poll_data['id'];
			$_poll_data['id'] = \lib\utility\shortURL::encode($_poll_data['id']);
		}

		if(isset($_poll_data['user_id']))
		{
			$_poll_data['user_id'] = \lib\utility\shortURL::encode($_poll_data['user_id']);
		}

		if(isset($_poll_data['parent']))
		{
			$_poll_data['parent'] = \lib\utility\shortURL::encode($_poll_data['parent']);
		}

		if(isset($_poll_data['survey']))
		{
			$_poll_data['survey'] = \lib\utility\shortURL::encode($_poll_data['survey']);
		}

		if(isset($_poll_data['sarshomar']) && $_poll_data['sarshomar'])
		{
			$_poll_data['sarshomar'] = "yes";
		}
		else
		{
			$_poll_data['sarshomar'] = "no";
		}
		
		if(is_array($_poll_data))
		{
			$_poll_data = array_filter($_poll_data);
		}

		if(isset($_poll_data['parent']) && $_poll_data['parent'] !== null)
		{
			$_poll_data['tree'] = [];
			$_poll_data_tree = \lib\utility\poll_tree::get($_poll_data['id']);

			if($_poll_data_tree && is_array($_poll_data_tree))
			{
				$opt = array_column($_poll_data_tree, 'value');
				$_poll_data['tree']['answers']   = is_array($opt) ? $opt : [$opt];
				$_poll_data['tree']['parent_id'] = \lib\utility\shortURL::encode($_poll_data['parent']);
				$_poll_data['tree']['title']     = \lib\db\polls::get_poll_title($_poll_data['parent']);
			}
		}

		if($_options['get_opts'])
		{
			$answers = \lib\db\pollopts::get($poll_id);
			$_poll_data['answers'] = $answers;
		}

		if($_options['get_filter'])
		{
			$filters = \lib\utility\postfilters::get_filter($poll_id);
			$_poll_data['filters'] = $filters;
			$_poll_data['filters']['member'] = \lib\db\ranks::get($poll_id, 'member');
		}
		return $_poll_data;
	}
}



?>