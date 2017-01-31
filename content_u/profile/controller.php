<?php
namespace content_u\profile;

class controller extends  \content_u\home\controller
{
	public function _route()
	{
		if(\lib\utility::get("import"))
		{

			$file = \lib\utility\file::read(root.'content_u/profile/profile/cat.csv');
			$line = preg_split("/\n/", $file);
			foreach ($line as $key => $value)
			{
				$split = preg_split("/\,/", $value);
				if($key === 0)
				{
					continue;
				}
				$cat1 = null;
				if(isset($split[1]))
				{
					$cat1 = trim($split[1]);
				}
				$cat2 = null;
				if(isset($split[2]))
				{
					$cat2 = trim($split[2]);
				}
				$cat3 = null;
				if(isset($split[3]))
				{
					$cat3 = trim($split[3]);
				}
				$cat4 = null;
				if(isset($split[4]))
				{
					$cat4 = trim($split[4]);
				}
				$cat5 = null;
				if(isset($split[5]))
				{
					$cat5 = trim($split[5]);
				}
				$cat6 = null;
				if(isset($split[6]))
				{
					$cat6 = trim($split[6]);
				}
				$cat7 = null;
				if(isset($split[7]))
				{
					$cat7 = trim($split[7]);
				}

			}

		}
		die(':)');
		parent::check_login();

		$this->get("profile", "profile")->ALL();
		$this->post("profile")->ALL();
	}
}

?>