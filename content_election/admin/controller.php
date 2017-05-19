<?php
namespace content_election\admin;

class controller extends \content_election\main\controller
{
	public $change = 0;
	public $all = 0;
	public function _route()
	{

		parent::_route();

		$this->access('election:admin:admin', 'block');
		if(\lib\utility::get("fix_file"))
		{
			$query = "SELECT candidas.*, elections.*, candidas.id AS `xid` FROM candidas  LEFT JOIN elections ON elections.id = candidas.election_id";
			$candida = \lib\db::get($query, null, false, 'election');
			foreach ($candida as $key => $value)
			{
				if(isset($value['file_url']))
				{

					$this->move_file($value, 'file_url');
				}

				if(isset($value['file_url_2']))
				{
					$this->move_file($value, 'file_url_2');
				}

				if(isset($value['win_url']))
				{
					$this->move_file($value, 'win_url');
				}
			}
			var_dump($this->change . '/' . $this->all . " file and record is changed");
			// var_dump("ff");
			exit();
		}
	}


	public function move_file($_data, $_type)
	{
		$base         = root."public_html/files/election";

		if(!\lib\utility\file::exists($base))
		{
			\lib\utility\file::makeDir($base);
		}

		$new_file_url = "";

		$new_file_url .= '/files/election/iran-president-'. $_data['jalali_year'] . '-';

		$name   = mb_strtolower($_data['en_name']);
		$family = mb_strtolower($_data['en_family']);
		$fame   = mb_strtolower($_data['en_fame']);

		$new_file_url .= "$name $family";

		if($fame != $family)
		{
			$new_file_url .= "($fame)";
		}

		$new_file_url = str_replace(' ', "_", $new_file_url);

		if($_type === 'file_url_2')
		{
			$new_file_url .= '-vs';
		}
		elseif($_type === 'win_url')
		{
			$new_file_url .= '-winner';
		}

		$ext          = basename($_data[$_type]);
		$ext          = explode('.', $ext);
		$ext          = end($ext);
		$new_file_url .= '.'. $ext;
		$old_file_url = root. "public_html/static/". $_data[$_type];
		if($name === 'unacceptable' || $family === 'unacceptable' || $fame === 'unacceptable')
		{
			$new_file_url = '/files/election/unacceptable.jpg';
		}
		elseif($name === 'other' || $family === 'other' || $fame === 'other')
		{
			$new_file_url = '/files/election/other.jpg';
		}

		$xx = root. 'public_html' . $new_file_url;
		var_dump($old_file_url, $xx);
		$this->all++;
		$moved = \lib\utility\file::copy($old_file_url, $xx);
		if($moved)
		{
			\lib\db::query("UPDATE candidas SET $_type = '$new_file_url' where id = $_data[xid] LIMIT 1", 'election' );
			$this->change++;
		}
	}
}
?>