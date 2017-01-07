<?php
namespace database\sarshomar;
class commentdetails
{
	public $user_id    = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'user'            ,'type'=>'int@10'                          ,'foreign'=>'users@id!user_displayname'];
	public $comment_id = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'comment'         ,'type'=>'bigint@20'                       ,'foreign'=>'comments@id!id'];
	public $type       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'type'            ,'type'=>'enum@minus,plus'];

	//--------------------------------------------------------------------------------foreign
	public function user_id()
	{
		$this->form()->type('select')->name('user_')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------foreign
	public function comment_id()
	{
		$this->form()->type('select')->name('comment_')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function type()
	{
		$this->form()->type('radio')->name('type')->required();
		$this->setChild();
	}
}
?>