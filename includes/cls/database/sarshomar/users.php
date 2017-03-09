<?php
namespace database\sarshomar;
class users
{
	public $id               = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'int@10'];
	public $user_mobile      = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'mobile'          ,'type'=>'varchar@15'];
	public $user_email       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'email'           ,'type'=>'varchar@100'];
	public $user_username    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'username'        ,'type'=>'varchar@50'];
	public $user_pass        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'pass'            ,'type'=>'varchar@64'];
	public $user_displayname = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'displayname'     ,'type'=>'varchar@100'];
	public $user_meta        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'mediumtext@'];
	public $user_status      = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'status'          ,'type'=>'enum@active,awaiting,deactive,removed,filter,spam,block,delete!awaiting'];
	public $user_permission  = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'permission'      ,'type'=>'smallint@5'];
	public $user_createdate  = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'createdate'      ,'type'=>'datetime@!CURRENT_TIMESTAMP'];
	public $user_parent      = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'parent'          ,'type'=>'int@10'                          ,'foreign'=>'users@id!user_title'];
	public $user_validstatus = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'validstatus'     ,'type'=>'enum@valid,invalid!invalid'];
	public $filter_id        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'filter'          ,'type'=>'bigint@20'                       ,'foreign'=>'filters@id!id'];
	public $user_port        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'port'            ,'type'=>'enum@site,api,guest,android,telegram,instagram,google,linkedin,github,facebook,twitter,other,ios,wp'];
	public $user_trust       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'trust'           ,'type'=>'enum@valid,invalid,unknown'];
	public $user_verify      = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'verify'          ,'type'=>'enum@mobile,complete,unknown,uniqueid'];
	public $date_modified    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'modified'        ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function id(){}

	public function user_mobile()
	{
		$this->form('#mobile')->type('text')->name('mobile')->maxlength('15')->required();
	}

	public function user_email()
	{
		$this->form('#email')->type('email')->name('email')->maxlength('100');
	}

	public function user_username()
	{
		$this->form()->type('text')->name('username')->maxlength('50');
	}

	public function user_pass()
	{
		$this->form('#pass')->type('password')->name('pass')->maxlength('64');
	}

	public function user_displayname()
	{
		$this->form()->type('text')->name('displayname')->maxlength('100');
	}

	public function user_meta(){}

	public function user_status()
	{
		$this->form()->type('radio')->name('status');
		$this->setChild();
	}

	public function user_permission()
	{
		$this->form()->type('number')->name('permission')->min()->max('99999');
	}

	public function user_createdate(){}

	public function user_parent()
	{
		$this->form()->type('select')->name('parent');
		$this->setChild();
	}

	public function user_validstatus()
	{
		$this->form()->type('radio')->name('validstatus')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------foreign
	public function filter_id()
	{
		$this->form()->type('select')->name('filter_');
		$this->setChild();
	}

	public function user_port()
	{
		$this->form()->type('radio')->name('port');
		$this->setChild();
	}

	public function user_trust()
	{
		$this->form()->type('radio')->name('trust');
		$this->setChild();
	}

	public function user_verify()
	{
		$this->form()->type('radio')->name('verify');
		$this->setChild();
	}

	public function date_modified(){}
}
?>