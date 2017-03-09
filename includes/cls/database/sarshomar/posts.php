<?php
namespace database\sarshomar;
class posts
{
	public $id               = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'bigint@20'];
	public $post_language    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'language'        ,'type'=>'char@2'];
	public $post_title       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'title'           ,'type'=>'varchar@255'];
	public $post_slug        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'slug'            ,'type'=>'varchar@255'];
	public $post_url         = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'url'             ,'type'=>'varchar@255'];
	public $post_content     = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'content'         ,'type'=>'mediumtext@'];
	public $post_meta        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'mediumtext@'];
	public $post_type        = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'type'            ,'type'=>'varchar@100!post'];
	public $post_comment     = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'comment'         ,'type'=>'enum@open,closed'];
	public $post_count       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'count'           ,'type'=>'smallint@5'];
	public $post_order       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'order'           ,'type'=>'int@10'];
	public $post_status      = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'status'          ,'type'=>'enum@stop,pause,trash,publish,draft,enable,disable,deleted,schedule,expired,awaiting,filtered,blocked,spam,violence,pornography,other!draft'];
	public $post_parent      = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'parent'          ,'type'=>'bigint@20'                       ,'foreign'=>'posts@id!post_title'];
	public $user_id          = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'user'            ,'type'=>'int@10'                          ,'foreign'=>'users@id!user_displayname'];
	public $post_survey      = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'survey'          ,'type'=>'bigint@20'];
	public $post_sarshomar   = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'sarshomar'       ,'type'=>'bit@1'];
	public $post_privacy     = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'privacy'         ,'type'=>'enum@public,private'];
	public $post_rank        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'rank'            ,'type'=>'bigint@20'];
	public $post_createdate  = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'createdate'      ,'type'=>'datetime@!CURRENT_TIMESTAMP'];
	public $post_publishdate = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'publishdate'     ,'type'=>'datetime@'];
	public $date_modified    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'modified'        ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function id(){}

	public function post_language()
	{
		$this->form()->type('text')->name('language')->maxlength('2');
	}

	public function post_title()
	{
		$this->form('#title')->type('textarea')->name('title')->maxlength('255')->required();
	}

	public function post_slug()
	{
		$this->form('#slug')->type('textarea')->name('slug')->maxlength('255')->required();
	}

	public function post_url()
	{
		$this->form()->type('textarea')->name('url')->maxlength('255')->required();
	}

	public function post_content()
	{
		$this->form()->type('textarea')->name('content');
	}

	public function post_meta(){}

	public function post_type()
	{
		$this->form()->type('text')->name('type')->maxlength('100')->required();
	}

	public function post_comment()
	{
		$this->form()->type('radio')->name('comment');
		$this->setChild();
	}

	public function post_count()
	{
		$this->form()->type('number')->name('count')->min()->max('99999');
	}

	public function post_order()
	{
		$this->form()->type('number')->name('order')->min()->max('9999999999');
	}

	public function post_status()
	{
		$this->form()->type('radio')->name('status')->required();
		$this->setChild();
	}

	public function post_parent()
	{
		$this->form()->type('select')->name('parent');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------foreign
	public function user_id()
	{
		$this->form()->type('select')->name('user_')->required();
		$this->setChild();
	}

	public function post_survey()
	{
		$this->form()->type('number')->name('survey')->min()->max('99999999999999999999');
	}

	public function post_sarshomar(){}

	public function post_privacy()
	{
		$this->form()->type('radio')->name('privacy');
		$this->setChild();
	}

	public function post_rank()
	{
		$this->form()->type('number')->name('rank')->max('99999999999999999999');
	}

	public function post_createdate(){}

	public function post_publishdate()
	{
		$this->form()->type('text')->name('publishdate');
	}

	public function date_modified(){}
}
?>