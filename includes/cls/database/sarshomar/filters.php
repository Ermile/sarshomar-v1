<?php
namespace database\sarshomar;
class filters
{
	public $id               = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'bigint@20'];
	public $usercount        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'usercount'       ,'type'=>'int@10'];
	public $gender           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'gender'          ,'type'=>'enum@male,female'];
	public $marrital         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'marrital'        ,'type'=>'enum@single,married'];
	public $internetusage    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'internetusage'   ,'type'=>'enum@low,mid,high'];
	public $graduation       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'graduation'      ,'type'=>'enum@illiterate,undergraduate,graduate'];
	public $degree           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'degree'          ,'type'=>'enum@under diploma,diploma,2 year college,bachelor,master,phd,other'];
	public $course           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'course'          ,'type'=>'varchar@200'];
	public $age              = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'age'             ,'type'=>'smallint@3'];
	public $agemin           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'agemin'          ,'type'=>'smallint@3'];
	public $agemax           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'agemax'          ,'type'=>'smallint@3'];
	public $range            = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'range'           ,'type'=>'enum@-13,14-17,18-24,25-30,31-44,45-59,60+'];
	public $country          = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'country'         ,'type'=>'varchar@64'];
	public $province         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'province'        ,'type'=>'varchar@64'];
	public $city             = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'city'            ,'type'=>'varchar@64'];
	public $employmentstatus = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'employmentstatus','type'=>'enum@employee,unemployed,retired,unemployee'];
	public $housestatus      = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'housestatus'     ,'type'=>'enum@owner,tenant,homeless'];
	public $religion         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'religion'        ,'type'=>'varchar@64'];
	public $language         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'language'        ,'type'=>'varchar@2'];
	public $industry         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'industry'        ,'type'=>'varchar@200'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------id
	public function usercount()
	{
		$this->form()->type('number')->name('usercount')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function gender()
	{
		$this->form()->type('radio')->name('gender');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function marrital()
	{
		$this->form()->type('radio')->name('marrital');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function internetusage()
	{
		$this->form()->type('radio')->name('internetusage');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function graduation()
	{
		$this->form()->type('radio')->name('graduation');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function degree()
	{
		$this->form()->type('radio')->name('degree');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function course()
	{
		$this->form()->type('textarea')->name('course')->maxlength('200');
	}
	//--------------------------------------------------------------------------------id
	public function age()
	{
		$this->form()->type('number')->name('age')->max('999');
	}
	//--------------------------------------------------------------------------------id
	public function agemin()
	{
		$this->form()->type('number')->name('agemin')->max('999');
	}
	//--------------------------------------------------------------------------------id
	public function agemax()
	{
		$this->form()->type('number')->name('agemax')->max('999');
	}
	//--------------------------------------------------------------------------------id
	public function range()
	{
		$this->form()->type('radio')->name('range');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function country()
	{
		$this->form()->type('text')->name('country')->maxlength('64');
	}
	//--------------------------------------------------------------------------------id
	public function province()
	{
		$this->form()->type('select')->name('province')->maxlength('64');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function city()
	{
		$this->form()->type('text')->name('city')->maxlength('64');
	}
	//--------------------------------------------------------------------------------id
	public function employmentstatus()
	{
		$this->form()->type('radio')->name('employmentstatus');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function housestatus()
	{
		$this->form()->type('radio')->name('housestatus');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function religion()
	{
		$this->form()->type('text')->name('religion')->maxlength('64');
	}
	//--------------------------------------------------------------------------------id
	public function language()
	{
		$this->form()->type('text')->name('language')->maxlength('2');
	}
	//--------------------------------------------------------------------------------id
	public function industry()
	{
		$this->form()->type('textarea')->name('industry')->maxlength('200');
	}
}
?>