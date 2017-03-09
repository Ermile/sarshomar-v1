<?php
namespace database\sarshomar;
class pollopts
{
	public $id             = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'bigint@20'];
	public $post_id        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'post'            ,'type'=>'bigint@20'                       ,'foreign'=>'posts@id!post_title'];
	public $title          = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'title'           ,'type'=>'varchar@100'];
	public $key            = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'key'             ,'type'=>'smallint@3'];
	public $type           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'type'            ,'type'=>'enum@select,emoji,descriptive,upload,range,notification,like,star'];
	public $subtype        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'subtype'         ,'type'=>'varchar@100'];
	public $true           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'true'            ,'type'=>'bit@1!b'0''];
	public $groupscore     = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'groupscore'      ,'type'=>'varchar@100'];
	public $score          = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'score'           ,'type'=>'smallint@5'];
	public $attachment_id  = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'attachment'      ,'type'=>'bigint@20'                       ,'foreign'=>'attachments@id!id'];
	public $attachmenttype = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'attachmenttype'  ,'type'=>'enum@image,audio,video,pdf,other'];
	public $desc           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'desc'            ,'type'=>'varchar@255'];
	public $meta           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'text@'];
	public $status         = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'status'          ,'type'=>'enum@enable,disable,expired,awaiting,filtered,blocked,spam!enable'];
	public $createdate     = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'createdate'      ,'type'=>'datetime@!CURRENT_TIMESTAMP'];
	public $datemodified   = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'datemodified'    ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------foreign
	public function post_id()
	{
		$this->form()->type('select')->name('post_')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function title()
	{
		$this->form('#title')->type('text')->name('title')->maxlength('100');
	}
	//--------------------------------------------------------------------------------id
	public function key()
	{
		$this->form()->type('number')->name('key')->min()->max('999');
	}
	//--------------------------------------------------------------------------------id
	public function type()
	{
		$this->form()->type('radio')->name('type');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function subtype()
	{
		$this->form()->type('text')->name('subtype')->maxlength('100');
	}
	//--------------------------------------------------------------------------------id
	public function true(){}
	//--------------------------------------------------------------------------------id
	public function groupscore()
	{
		$this->form()->type('text')->name('groupscore')->maxlength('100');
	}
	//--------------------------------------------------------------------------------id
	public function score()
	{
		$this->form()->type('number')->name('score')->max('99999');
	}
	//--------------------------------------------------------------------------------foreign
	public function attachment_id()
	{
		$this->form()->type('select')->name('attachment_');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function attachmenttype()
	{
		$this->form()->type('radio')->name('attachmenttype');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function desc()
	{
		$this->form('#desc')->type('textarea')->name('desc')->maxlength('255');
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
	public function createdate(){}
	//--------------------------------------------------------------------------------id
	public function datemodified()
	{
		$this->form()->type('text')->name('datemodified');
	}
}
?>