<?php
namespace database\sarshomar;
class pollstats
{
	public $id               = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'bigint@20'];
	public $post_id          = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'post'            ,'type'=>'bigint@20'                       ,'foreign'=>'posts@id!post_title'];
	public $type             = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'type'            ,'type'=>'enum@valid,invalid!invalid'];
	public $port             = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'port'            ,'type'=>'enum@site,telegram,sms,api!site'];
	public $subport          = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'subport'         ,'type'=>'varchar@64'];
	public $total            = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'total'           ,'type'=>'int@10'];
	public $result           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'result'          ,'type'=>'text@'];
	public $gender           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'gender'          ,'type'=>'text@'];
	public $marrital         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'marrital'        ,'type'=>'text@'];
	public $graduation       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'graduation'      ,'type'=>'text@'];
	public $degree           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'degree'          ,'type'=>'text@'];
	public $employmentstatus = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'employmentstatus','type'=>'text@'];
	public $housestatus      = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'housestatus'     ,'type'=>'text@'];
	public $internetusage    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'internetusage'   ,'type'=>'text@'];
	public $range            = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'range'           ,'type'=>'text@'];
	public $age              = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'age'             ,'type'=>'text@'];
	public $country          = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'country'         ,'type'=>'text@'];
	public $province         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'province'        ,'type'=>'text@'];
	public $city             = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'city'            ,'type'=>'text@'];
	public $language         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'language'        ,'type'=>'text@'];
	public $religion         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'religion'        ,'type'=>'text@'];
	public $course           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'course'          ,'type'=>'text@'];
	public $industry         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'industry'        ,'type'=>'text@'];
	public $meta             = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'mediumtext@'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------foreign
	public function post_id()
	{
		$this->form()->type('select')->name('post_')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function type()
	{
		$this->form()->type('radio')->name('type')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function port()
	{
		$this->form()->type('radio')->name('port')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function subport()
	{
		$this->form()->type('text')->name('subport')->maxlength('64');
	}
	//--------------------------------------------------------------------------------id
	public function total()
	{
		$this->form()->type('number')->name('total')->min()->max('9999999999');
	}
	//--------------------------------------------------------------------------------id
	public function result()
	{
		$this->form()->type('textarea')->name('result');
	}
	//--------------------------------------------------------------------------------id
	public function gender()
	{
		$this->form()->type('textarea')->name('gender');
	}
	//--------------------------------------------------------------------------------id
	public function marrital()
	{
		$this->form()->type('textarea')->name('marrital');
	}
	//--------------------------------------------------------------------------------id
	public function graduation()
	{
		$this->form()->type('textarea')->name('graduation');
	}
	//--------------------------------------------------------------------------------id
	public function degree()
	{
		$this->form()->type('textarea')->name('degree');
	}
	//--------------------------------------------------------------------------------id
	public function employmentstatus()
	{
		$this->form()->type('textarea')->name('employmentstatus');
	}
	//--------------------------------------------------------------------------------id
	public function housestatus()
	{
		$this->form()->type('textarea')->name('housestatus');
	}
	//--------------------------------------------------------------------------------id
	public function internetusage()
	{
		$this->form()->type('textarea')->name('internetusage');
	}
	//--------------------------------------------------------------------------------id
	public function range()
	{
		$this->form()->type('textarea')->name('range');
	}
	//--------------------------------------------------------------------------------id
	public function age()
	{
		$this->form()->type('textarea')->name('age');
	}
	//--------------------------------------------------------------------------------id
	public function country()
	{
		$this->form()->type('textarea')->name('country');
	}
	//--------------------------------------------------------------------------------id
	public function province()
	{
		$this->form()->type('textarea')->name('province');
	}
	//--------------------------------------------------------------------------------id
	public function city()
	{
		$this->form()->type('textarea')->name('city');
	}
	//--------------------------------------------------------------------------------id
	public function language()
	{
		$this->form()->type('textarea')->name('language');
	}
	//--------------------------------------------------------------------------------id
	public function religion()
	{
		$this->form()->type('textarea')->name('religion');
	}
	//--------------------------------------------------------------------------------id
	public function course()
	{
		$this->form()->type('textarea')->name('course');
	}
	//--------------------------------------------------------------------------------id
	public function industry()
	{
		$this->form()->type('textarea')->name('industry');
	}
	//--------------------------------------------------------------------------------id
	public function meta(){}
}
?>