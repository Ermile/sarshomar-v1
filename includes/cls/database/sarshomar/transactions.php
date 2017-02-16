<?php
namespace database\sarshomar;
class transactions
{
	public $id                 = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'bigint@20'];
	public $title              = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'title'           ,'type'=>'varchar@500'];
	public $transactionitem_id = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'transactionitem' ,'type'=>'int@10'                          ,'foreign'=>'transactionitems@id!id'];
	public $user_id            = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'user'            ,'type'=>'int@10'                          ,'foreign'=>'users@id!user_displayname'];
	public $type               = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'type'            ,'type'=>'enum@real,gift,prize,transfer'];
	public $unit_id            = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'unit'            ,'type'=>'smallint@5'                      ,'foreign'=>'units@id!id'];
	public $plus               = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'plus'            ,'type'=>'double@'];
	public $minus              = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'minus'           ,'type'=>'double@'];
	public $budgetbefore        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'budgetbefore'     ,'type'=>'double@'];
	public $budget             = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'budget'          ,'type'=>'double@'];
	public $exchange_id        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'exchange'        ,'type'=>'bigint@20'                       ,'foreign'=>'exchanges@id!id'];
	public $status             = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'status'          ,'type'=>'enum@enable,disable,deleted,expired,awaiting,filtered,blocked,spam'];
	public $meta               = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'text@'];
	public $desc               = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'desc'            ,'type'=>'text@'];
	public $related_user_id    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'user_id'         ,'type'=>'int@10'];
	public $parent_id          = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'parent'          ,'type'=>'bigint@20'                       ,'foreign'=>'parents@id!id'];
	public $finished           = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'finished'        ,'type'=>'enum@yes,no!no'];
	public $createdate         = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'createdate'      ,'type'=>'timestamp@!CURRENT_TIMESTAMP'];
	public $datemodified       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'datemodified'    ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------id
	public function title()
	{
		$this->form('#title')->type('textarea')->name('title')->maxlength('500')->required();
	}
	//--------------------------------------------------------------------------------foreign
	public function transactionitem_id()
	{
		$this->form()->type('select')->name('transactionitem_')->required();
		$this->setChild();
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
	//--------------------------------------------------------------------------------foreign
	public function unit_id()
	{
		$this->form()->type('select')->name('unit_')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function plus(){}
	//--------------------------------------------------------------------------------id
	public function minus(){}
	//--------------------------------------------------------------------------------id
	public function budgetbefore(){}
	//--------------------------------------------------------------------------------id
	public function budget(){}
	//--------------------------------------------------------------------------------foreign
	public function exchange_id()
	{
		$this->form()->type('select')->name('exchange_');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function status()
	{
		$this->form()->type('radio')->name('status')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function meta(){}
	//--------------------------------------------------------------------------------id
	public function desc()
	{
		$this->form('#desc')->type('textarea')->name('desc');
	}

	public function related_user_id()
	{
		$this->form()->type('number')->name('user_id')->min()->max('9999999999');
	}
	//--------------------------------------------------------------------------------foreign
	public function parent_id()
	{
		$this->form()->type('select')->name('parent_');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function finished()
	{
		$this->form()->type('radio')->name('finished')->required();
		$this->setChild();
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