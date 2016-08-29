<?php
namespace content_u\polls;
use \lib\utility;

class model extends \mvc\model
{
	function get_polls($o){


		$poll_id = \lib\router::get_url(1);


		var_dump($poll_id);
	}

}
?>