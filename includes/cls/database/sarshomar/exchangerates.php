<?php
namespace database\sarshomar;
class exchangerates
{
	public $id           = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'bigint@20'];
	public $from         = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'from'            ,'type'=>'smallint@5'];
	public $to           = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'to'              ,'type'=>'smallint@5'];
	public $rate         = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'rate'            ,'type'=>'double@'];
	public $roundtype    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'roundtype'       ,'type'=>'enum@up,down,round'];
	public $round        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'round'           ,'type'=>'double@'];
	public $wagestatic   = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'wagestatic'      ,'type'=>'double@'];
	public $wage         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'wage'            ,'type'=>'double@'];
	public $status       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'status'          ,'type'=>'enum@enable,disable,deleted,expired,awaiting,filtered,blocked,spam'];
	public $desc         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'desc'            ,'type'=>'text@'];
	public $meta         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'text@'];
	public $createdate   = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'createdate'      ,'type'=>'timestamp@!CURRENT_TIMESTAMP'];
	public $datemodified = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'datemodified'    ,'type'=>'timestamp@'];
	public $enddate      = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'enddate'         ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------id
	public function from()
	{
		$this->form()->type('number')->name('from')->min()->max('99999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function to()
	{
		$this->form()->type('number')->name('to')->min()->max('99999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function rate(){}
	//--------------------------------------------------------------------------------id
	public function roundtype()
	{
		$this->form()->type('radio')->name('roundtype');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function round(){}
	//--------------------------------------------------------------------------------id
	public function wagestatic(){}
	//--------------------------------------------------------------------------------id
	public function wage(){}
	//--------------------------------------------------------------------------------id
	public function status()
	{
		$this->form()->type('radio')->name('status')->required();
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