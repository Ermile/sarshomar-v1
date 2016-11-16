<?php
namespace content\knowledge;
use \lib\utility;

class model extends \mvc\model
{

	public function post_search()
	{

		if(utility::post("type") == 'faivorites')
		{
			if($this->login())
			{
				$cat = 'user_detail_'. $this->login('id');
				$args =
				[
					'post_id'       => utility::post("id"),
					'user_id'       => $this->login('id'),
					'option_cat'    => $cat,
					'option_key'    => 'faivorites',
					'option_value'  => utility::post("id"),
					'option_status' => 'enable'
				];

				$insert_option = \lib\db\options::insert($args);
				if(!$insert_option)
				{
					$where = $args;

					array_splice($where, -1);

					$exist_option_record = \lib\db\options::get($where);

					if(isset($exist_option_record[0]['status']) && $exist_option_record[0]['status'] == 'disable')
					{
						$args['option_status'] = 'enable';
					}
					else
					{
						$args['option_status'] = 'disable';
					}
					\lib\db\options::update_on_error($args, $where);
				}
			}

			return;
		}
		$field = [];
		if($this->login())
		{
			$field = ['login' => $this->login('id')];
		}

		return \lib\db\polls::search(utility::post("search"), $field);
	}

	/**
	 * Gets the search.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_search($_args)
	{
		if(isset($_args->match->url[0][0]) && $_args->match->url[0][0] == '')
		{
			$field = [];
			$field['limit'] = 10;
			if($this->login())
			{
				$field['login'] = $this->login('id');
			}
			return \lib\db\polls::get_last_poll($field);
		}

		$match = $_args;
		unset($_args->match->url);
		unset($_args->method);
		unset($_args->match->property);
		$match  = $match->match;

		$filter = [];

		foreach ($match as $key => $value) {
			if(is_array($value) && isset($value[0]))
			{
				$value = $value[0];
			}
			if(\lib\db\filters::support_filter($key))
			{
				$filter[$key] = $value;
			}
		}

		$meta                   = [];
		$meta['post_sarshomar'] = 1;
		if(!empty($filter))
		{
			$filter_id         = \lib\db\filters::get_id($filter);
			$meta['filter_id'] = $filter_id;
		}
		$search = $_args->get("search")[0];
		$result = \lib\db\polls::search($search, $meta);
		return $result;
	}
}
?>