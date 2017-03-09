<?php
namespace database\sarshomar;
class ranks
{
	public $id         = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'bigint@20'];
	public $post_id    = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'post'            ,'type'=>'bigint@20'                       ,'foreign'=>'posts@id!post_title'];
	public $member     = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'member'          ,'type'=>'int@10'];
	public $public     = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'public'          ,'type'=>'bit@1!b'0''];
	public $filter     = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'filter'          ,'type'=>'int@10'];
	public $ad         = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'ad'              ,'type'=>'int@10'];
	public $money      = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'money'           ,'type'=>'int@10'];
	public $report     = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'report'          ,'type'=>'int@10'];
	public $vote       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'vote'            ,'type'=>'int@10'];
	public $like       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'like'            ,'type'=>'int@10'];
	public $fav        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'fav'             ,'type'=>'int@10'];
	public $skip       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'skip'            ,'type'=>'int@10'];
	public $comment    = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'comment'         ,'type'=>'int@10'];
	public $view       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'view'            ,'type'=>'bigint@20'];
	public $other      = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'other'           ,'type'=>'int@10'];
	public $sarshomar  = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'sarshomar'       ,'type'=>'int@10'];
	public $createdate = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'createdate'      ,'type'=>'datetime@!CURRENT_TIMESTAMP'];
	public $ago        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'ago'             ,'type'=>'int@10'];
	public $admin      = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'admin'           ,'type'=>'int@10'];
	public $vip        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'vip'             ,'type'=>'int@10'];
	public $value      = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'value'           ,'type'=>'bigint@20'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------foreign
	public function post_id()
	{
		$this->form()->type('select')->name('post_')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function member()
	{
		$this->form()->type('number')->name('member')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function public(){}
	//--------------------------------------------------------------------------------id
	public function filter()
	{
		$this->form()->type('number')->name('filter')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function ad()
	{
		$this->form()->type('number')->name('ad')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function money()
	{
		$this->form()->type('number')->name('money')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function report()
	{
		$this->form()->type('number')->name('report')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function vote()
	{
		$this->form()->type('number')->name('vote')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function like()
	{
		$this->form()->type('number')->name('like')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function fav()
	{
		$this->form()->type('number')->name('fav')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function skip()
	{
		$this->form()->type('number')->name('skip')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function comment()
	{
		$this->form()->type('number')->name('comment')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function view()
	{
		$this->form()->type('number')->name('view')->min()->max('99999999999999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function other()
	{
		$this->form()->type('number')->name('other')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function sarshomar()
	{
		$this->form()->type('number')->name('sarshomar')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function createdate(){}
	//--------------------------------------------------------------------------------id
	public function ago()
	{
		$this->form()->type('number')->name('ago')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function admin()
	{
		$this->form()->type('number')->name('admin')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function vip()
	{
		$this->form()->type('number')->name('vip')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function value()
	{
		$this->form()->type('number')->name('value')->max('99999999999999999999')->required();
	}
}
?>