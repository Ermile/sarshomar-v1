<?php
namespace database\sarshomar;
class transactionitems
{
	public $s             = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'s'               ,'type'=>'int@10'];
	public $title         = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'title'           ,'type'=>'varchar@100'];
	public $caller        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'caller'          ,'type'=>'varchar@100'];
	public $unit_id       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'unit'            ,'type'=>'smallint@5'                      ,'foreign'=>'units@id!id'];
	public $type          = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'type'            ,'type'=>'enum@real,gift,prize,transfer'];
	public $minus         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'minus'           ,'type'=>'double unsigned@'];
	public $plus          = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'plus'            ,'type'=>'double unsigned@'];
	public $autoverify    = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'autoverify'      ,'type'=>'enum@yes,no!no'];
	public $forcechange   = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'forcechange'     ,'type'=>'enum@yes,no!no'];
	public $desc          = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'desc'            ,'type'=>'text@'];
	public $meta          = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'text@'];
	public $status        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'status'          ,'type'=>'enum@enable,disable,deleted,expired,awaiting,filtered,blocked,spam'];
	public $count         = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'count'           ,'type'=>'int@10'];
	public $createdate    = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'createdate'      ,'type'=>'datetime@!CURRENT_TIMESTAMP'];
	public $enddate       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'enddate'         ,'type'=>'datetime@'];
	public $date_modified = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'modified'        ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function s()
	{
		$this->form()->type('number')->name('s')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function title()
	{
		$this->form('#title')->type('text')->name('title')->maxlength('100')->required();
	}
	//--------------------------------------------------------------------------------id
	public function caller()
	{
		$this->form()->type('text')->name('caller')->maxlength('100')->required();
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
	public function enddate()
	{
		$this->form()->type('text')->name('enddate');
	}

	public function date_modified(){}
}
?>