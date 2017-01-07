<?php
namespace database\sarshomar;
class socialapi
{
	public $uniqueid     = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'uniqueid'        ,'type'=>'varchar@200'];
	public $user_id      = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'user'            ,'type'=>'int@10'                          ,'foreign'=>'users@id!user_displayname'];
	public $type         = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'type'            ,'type'=>'enum@telegram,facebook,twitter'];
	public $request      = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'request'         ,'type'=>'text@'];
	public $response     = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'response'        ,'type'=>'text@'];
	public $createdate   = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'createdate'      ,'type'=>'timestamp@!CURRENT_TIMESTAMP'];
	public $datemodified = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'datemodified'    ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function uniqueid()
	{
		$this->form()->type('textarea')->name('uniqueid')->maxlength('200')->required();
	}
	//--------------------------------------------------------------------------------foreign
	public function user_id()
	{
		$this->form()->type('select')->name('user_')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function type()
	{
		$this->form()->type('radio')->name('type')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function request()
	{
		$this->form()->type('textarea')->name('request');
	}
	//--------------------------------------------------------------------------------id
	public function response()
	{
		$this->form()->type('textarea')->name('response');
	}
	//--------------------------------------------------------------------------------id
	public function createdate(){}
	//--------------------------------------------------------------------------------id
	public function datemodified()
	{
		$this->form()->type('text')->name('datemodified');
	}
}
?>