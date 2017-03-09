<?php
namespace database\sarshomar;
class units
{
	public $id           = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'smallint@5'];
	public $title        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'title'           ,'type'=>'varchar@100'];
	public $desc         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'desc'            ,'type'=>'text@'];
	public $meta         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'text@'];
	public $createdate   = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'createdate'      ,'type'=>'datetime@!CURRENT_TIMESTAMP'];
	public $datemodified = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'datemodified'    ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------id
	public function title()
	{
		$this->form('#title')->type('text')->name('title')->maxlength('100')->required();
	}
	//--------------------------------------------------------------------------------id
	public function desc()
	{
		$this->form('#desc')->type('textarea')->name('desc');
	}
	//--------------------------------------------------------------------------------id
	public function meta(){}
	//--------------------------------------------------------------------------------id
	public function createdate(){}
	//--------------------------------------------------------------------------------id
	public function datemodified()
	{
		$this->form()->type('text')->name('datemodified');
	}
}
?>