<?php
namespace database\sarshomar;
class postfilters
{
	public $post_id   = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'post'            ,'type'=>'bigint@20'                       ,'foreign'=>'posts@id!post_title'];
	public $filter_id = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'filter'          ,'type'=>'bigint@20'                       ,'foreign'=>'filters@id!id'];

	//--------------------------------------------------------------------------------foreign
	public function post_id()
	{
		$this->form()->type('select')->name('post_')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------foreign
	public function filter_id()
	{
		$this->form()->type('select')->name('filter_')->required();
		$this->setChild();
	}
}
?>