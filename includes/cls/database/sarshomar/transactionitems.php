<?php
namespace database\sarshomar;
class transactionitems
{
	public $id           = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'int@10'];
	public $title        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'title'           ,'type'=>'varchar@500'];
	public $caller       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'caller'          ,'type'=>'varchar@200'];
	public $unit_id      = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'unit'            ,'type'=>'smallint@5'                      ,'foreign'=>'units@id!id'];
	public $type         = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'type'            ,'type'=>'enum@real,gift,prize,transfer'];
	public $minus        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'minus'           ,'type'=>'double@'];
	public $plus         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'plus'            ,'type'=>'double@'];
	public $autoverify   = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'autoverify'      ,'type'=>'enum@yes,no!no'];
	public $forcechange  = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'forcechange'     ,'type'=>'enum@yes,no!no'];
	public $desc         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'desc'            ,'type'=>'text@'];
	public $meta         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'text@'];
	public $status       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'status'          ,'type'=>'enum@enable,disable,deleted,expired,awaiting,filtered,blocked,spam'];
	public $count        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'count'           ,'type'=>'int@10'];
	public $createdate   = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'createdate'      ,'type'=>'timestamp@!CURRENT_TIMESTAMP'];
	public $datemodified = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'datemodified'    ,'type'=>'timestamp@'];
	public $enddate      = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'enddate'         ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------id
	public function title()
	{
		$this->form('#title')->type('textarea')->name('title')->maxlength('500')->required();
	}
	//--------------------------------------------------------------------------------id
	public function caller()
	{
		$this->form()->type('textarea')->name('caller')->maxlength('200')->required();
	}
	//--------------------------------------------------------------------------------foreign
	public function unit_id()
	{
		$this->form()->type('select')->name('unit_')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function type()
	{
		$this->form()->type('radio')->name('type')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function minus(){}
	//--------------------------------------------------------------------------------id
	public function plus(){}
	//--------------------------------------------------------------------------------id
	public function autoverify()
	{
		$this->form()->type('radio')->name('autoverify')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function forcechange()
	{
		$this->form()->type('radio')->name('forcechange')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function desc()
	{
		$this->form('#desc')->type('textarea')->name('desc');
	}
	//--------------------------------------------------------------------------------id
	public function meta(){}
	//--------------------------------------------------------------------------------id
	public function status()
	{
		$this->form()->type('radio')->name('status')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function count()
	{
		$this->form()->type('number')->name('count')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function createdate(){}
	//--------------------------------------------------------------------------------id
	public function datemodified()
	{
		$this->form()->type('text')->name('datemodified');
	}
	//--------------------------------------------------------------------------------id
	public function enddate()
	{
		$this->form()->type('text')->name('enddate');
	}
}
?>