<?php
namespace database\sarshomar;
class polldetails
{
	public $id            = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'bigint@20'];
	public $post_id       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'post'            ,'type'=>'bigint@10'                       ,'foreign'=>'posts@id!post_title'];
	public $user_id       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'user'            ,'type'=>'int@10'                          ,'foreign'=>'users@id!user_displayname'];
	public $port          = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'port'            ,'type'=>'enum@site,telegram,sms,api!site'];
	public $validstatus   = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'validstatus'     ,'type'=>'enum@valid,invalid'];
	public $subport       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'subport'         ,'type'=>'varchar@100'];
	public $opt           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'opt'             ,'type'=>'tinyint@3'];
	public $answertype    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'answertype'      ,'type'=>'varchar@50'];
	public $type          = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'type'            ,'type'=>'varchar@50'];
	public $txt           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'txt'             ,'type'=>'text@'];
	public $profile       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'profile'         ,'type'=>'bigint@20'];
	public $visitor_id    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'visitor'         ,'type'=>'bigint@20'                       ,'foreign'=>'visitors@id!id'];
	public $status        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'status'          ,'type'=>'enum@enable,disable!enable'];
	public $insertdate    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'insertdate'      ,'type'=>'datetime@!CURRENT_TIMESTAMP'];
	public $date_modified = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'modified'        ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------foreign
	public function post_id()
	{
		$this->form()->type('select')->name('post_')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------foreign
	public function user_id()
	{
		$this->form()->type('select')->name('user_')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function port()
	{
		$this->form()->type('radio')->name('port')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function validstatus()
	{
		$this->form()->type('radio')->name('validstatus')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function subport()
	{
		$this->form()->type('text')->name('subport')->maxlength('100');
	}
	//--------------------------------------------------------------------------------id
	public function opt()
	{
		$this->form()->type('number')->name('opt')->min()->max('999');
	}
	//--------------------------------------------------------------------------------id
	public function answertype()
	{
		$this->form()->type('text')->name('answertype')->maxlength('50');
	}
	//--------------------------------------------------------------------------------id
	public function type()
	{
		$this->form()->type('text')->name('type')->maxlength('50');
	}
	//--------------------------------------------------------------------------------id
	public function txt()
	{
		$this->form()->type('textarea')->name('txt');
	}
	//--------------------------------------------------------------------------------id
	public function profile()
	{
		$this->form()->type('number')->name('profile')->min()->max('99999999999999999999');
	}
	//--------------------------------------------------------------------------------foreign
	public function visitor_id()
	{
		$this->form()->type('select')->name('visitor_');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function status()
	{
		$this->form()->type('radio')->name('status');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function insertdate()
	{
		$this->form()->type('text')->name('insertdate');
	}

	public function date_modified(){}
}
?>