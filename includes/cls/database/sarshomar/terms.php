<?php
namespace database\sarshomar;
class terms
{
	public $id              = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'int@10'];
	public $term_language   = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'language'        ,'type'=>'char@2'];
	public $term_type       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'type'            ,'type'=>'varchar@100!tag'];
	public $term_caller     = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'caller'          ,'type'=>'varchar@100'];
	public $term_title      = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'title'           ,'type'=>'varchar@100'];
	public $term_slug       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'slug'            ,'type'=>'varchar@100'];
	public $term_url        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'url'             ,'type'=>'varchar@100'];
	public $term_desc       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'desc'            ,'type'=>'mediumtext@'];
	public $term_meta       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'mediumtext@'];
	public $term_parent     = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'parent'          ,'type'=>'int@10'                          ,'foreign'=>'terms@id!term_title'];
	public $user_id         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'user'            ,'type'=>'int@10'                          ,'foreign'=>'users@id!user_displayname'];
	public $term_status     = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'status'          ,'type'=>'enum@enable,disable,expired,awaiting,filtered,blocked,spam,violence,pornography,other!awaiting'];
	public $term_count      = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'count'           ,'type'=>'int@10'];
	public $term_usercount  = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'usercount'       ,'type'=>'int@10'];
	public $term_createdate = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'createdate'      ,'type'=>'datetime@!CURRENT_TIMESTAMP'];
	public $date_modified   = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'modified'        ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function id(){}

	public function term_language()
	{
		$this->form()->type('text')->name('language')->maxlength('2');
	}

	public function term_type()
	{
		$this->form()->type('text')->name('type')->maxlength('100')->required();
	}

	public function term_caller()
	{
		$this->form()->type('text')->name('caller')->maxlength('100');
	}

	public function term_title()
	{
		$this->form('#title')->type('text')->name('title')->maxlength('100')->required();
	}

	public function term_slug()
	{
		$this->form('#slug')->type('text')->name('slug')->maxlength('100')->required();
	}

	public function term_url()
	{
		$this->form()->type('text')->name('url')->maxlength('100')->required();
	}

	public function term_desc()
	{
		$this->form('#desc')->type('textarea')->name('desc');
	}

	public function term_meta(){}

	public function term_parent()
	{
		$this->form()->type('select')->name('parent');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------foreign
	public function user_id()
	{
		$this->form()->type('select')->name('user_');
		$this->setChild();
	}

	public function term_status()
	{
		$this->form()->type('radio')->name('status')->required();
		$this->setChild();
	}

	public function term_count()
	{
		$this->form()->type('number')->name('count')->min()->max('9999999999');
	}

	public function term_usercount()
	{
		$this->form()->type('number')->name('usercount')->min()->max('9999999999')->required();
	}

	public function term_createdate(){}

	public function date_modified(){}
}
?>