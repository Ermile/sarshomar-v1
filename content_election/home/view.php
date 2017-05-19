<?php
namespace content_election\home;

class view extends \content_election\main\view
{

	/**
	 * { function_description }
	 */
	public function config()
	{
		$running  = [];
		$election = \content_election\lib\elections::search();

		$this->data->page['title']   = T_('Iranian president election result');
		$this->data->page['desc']    = T_('Live and complete result of iran election after revolution until now.'). ' '. T_('Live result of iran 12 president election');

		$this->data->election_list = $election;

		foreach ($election as $key => $value)
		{
			if(isset($value['status']) && $value['status'] === 'running')
			{
				$running[] = $value;
			}
		}

		$this->data->running = $running;

		if($this->access('election:admin:admin'))
		{
			$this->data->perm_admin = true;
		}

		if($this->access('election:data:admin'))
		{
			$this->data->perm_data = true;
		}
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_load($_args)
	{
		$result = $_args->api_callback;
		$this->data->result = $result;

		if($this->data->site['currentlang'] == 'fa')
		{
			if(isset($this->data->result['election']['title']))
			{
				$title_of_el = $this->data->result['election']['title'];
				$this->data->page['title'] = $title_of_el;
				$this->data->page['desc'] = 'نتایج لحظه‌ای '. $title_of_el. '. آخرین نتایج انتخابات ریاست جمهوری را بررسی کنید';
			}
		}
		else
		{
			if(isset($this->data->result['election']['en_title']))
			{
				$title_of_el = $this->data->result['election']['en_title'];
				$this->data->page['title'] = $title_of_el;
				$this->data->page['desc'] = T_('Live result of '). $title_of_el;
			}
		}

		if(isset($this->data->result['candida'][0]['file_url']))
		{
			$image_of_winner = $this->url('root'). $this->data->result['candida'][0]['file_url'];

			$this->data->share['twitterCard'] = 'summary_large_image';
			$this->data->share['image']       = $image_of_winner;
		}
		// var_dump($this->data->result);
	}


	/**
	 * view candida
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_home($_args)
	{
		$this->data->result = $_args->api_callback;
	}


	/**
	 * view candida
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_candida($_args)
	{
		$this->data->result = $_args->api_callback;
	}


	public function view_comment($_args)
	{
		$this->data->result = $_args->api_callback;
	}
}
?>