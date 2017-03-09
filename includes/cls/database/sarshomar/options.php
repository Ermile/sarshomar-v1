<?php
namespace database\sarshomar;
class options
{
	public $id                = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'bigint@20'];
	public $user_id           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'user'            ,'type'=>'int@10'                          ,'foreign'=>'users@id!user_displayname'];
	public $post_id           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'post'            ,'type'=>'bigint@20'                       ,'foreign'=>'posts@id!post_title'];
	public $parent_id         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'parent'          ,'type'=>'bigint@20'                       ,'foreign'=>'parents@id!id'];
	public $option_cat        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'cat'             ,'type'=>'varchar@100'];
	public $option_key        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'key'             ,'type'=>'varchar@100'];
	public $option_value      = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'value'           ,'type'=>'varchar@100'];
	public $option_meta       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'mediumtext@'];
	public $option_status     = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'status'          ,'type'=>'enum@enable,disable,expire!enable'];
	public $option_createdate = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'createdate'      ,'type'=>'datetime@!CURRENT_TIMESTAMP'];
	public $date_modified     = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'modified'        ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------foreign
	public function user_id()
	{
		$this->form()->type('select')->name('user_');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------foreign
	public function post_id()
	{
		$this->form()->type('select')->name('post_');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------foreign
	public function parent_id()
	{
		$this->form()->type('select')->name('parent_');
		$this->setChild();
	}

	public function option_cat()
	{
		$this->form()->type('text')->name('cat')->maxlength('100')->required();
	}

	public function option_key()
	{
		$this->form()->type('text')->name('key')->maxlength('100')->required();
	}

	public function option_value()
	{
		$this->form()->type('text')->name('value')->maxlength('100');
	}

	public function option_meta(){}

	public function option_status()
	{
		$this->form()->type('radio')->name('status')->required();
		$this->setChild();
	}

	public function option_createdate(){}

	public function date_modified(){}
}
?>