<?php
namespace database\sarshomar;
class exchanges
{
	public $id              = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'bigint@20'];
	public $exchangerate_id = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'exchangerate'    ,'type'=>'bigint@20'                       ,'foreign'=>'exchangerates@id!id'];
	public $valuefrom       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'valuefrom'       ,'type'=>'double@'];
	public $valueto         = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'valueto'         ,'type'=>'double@'];
	public $meta            = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'text@'];
	public $desc            = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'desc'            ,'type'=>'text@'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------foreign
	public function exchangerate_id()
	{
		$this->form()->type('select')->name('exchangerate_')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function valuefrom(){}
	//--------------------------------------------------------------------------------id
	public function valueto(){}
	//--------------------------------------------------------------------------------id
	public function meta(){}
	//--------------------------------------------------------------------------------id
	public function desc()
	{
		$this->form('#desc')->type('textarea')->name('desc');
	}
}
?>