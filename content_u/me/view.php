<?php
namespace content_u\me;

class view extends \mvc\view
{
	public function view_me($_args)
	{
		$user_id                    = $this->login("id");
		// get total poll this users answered to that
		$this->data->total_answered = \lib\db\polldetails::user_total_answered($user_id);
		// get total poll this users skipped that
		$this->data->total_skipped   = \lib\db\polldetails::user_total_skipped($user_id);
		// get total answere + skipped polls of this users
		$this->data->total          = $this->data->total_answered + $this->data->total_skipped;
		// the point of all answered
		$this->data->point          = $this->data->total_answered * 100;
		// get count of polls of this users (this user creat it)
		$this->data->user_polls     = \lib\db\polls::get_count(['user_id' => $user_id]);
		// get poll id of this users to find count of people answere
		$poll_list = \lib\db\polls::xget(['user_id' => $user_id]);
		if($poll_list && is_array($poll_list))
		{
			$poll_ids = array_column($poll_list, "id");
			$this->data->people_answered = \lib\db\polldetails::people_answered($poll_ids);
			$this->data->people_skipped = \lib\db\polldetails::people_skipped($poll_ids);
		}
		// count referral users
		$referral = \lib\db\referral::count_children($user_id);
		if(isset($referral['active']))
		{
			$this->data->active_referral = $referral['active'];
		}
		if(isset($referral['awaiting']))
		{
			$this->data->awaiting_referral = $referral['awaiting'];
		}

		$this->data->me = $_args->api_callback;
	}
}
?>