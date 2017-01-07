<?php
namespace database\sarshomar;
class units
{
	public $id    = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'smallint@5'];
	public $title = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'title'           ,'type'=>'varchar@500'];
	public $desc  = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'desc'            ,'type'=>'text@'];
	public $meta  = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'text@'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------id
	public function title()
	{
		$this->form('#title')->type('textarea')->name('title')->maxlength('500')->required();
	}
	//--------------------------------------------------------------------------------id
	public function desc()
	{
		$this->form('#desc')->type('textarea')->name('desc');
	}
	//--------------------------------------------------------------------------------id
	public function meta(){}
}
?>