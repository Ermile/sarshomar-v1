<?php
namespace database\sarshomar;
class apilogs
{
	public $id             = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'bigint@20'];
	public $user_id        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'user'            ,'type'=>'int@10'                          ,'foreign'=>'users@id!user_displayname'];
	public $url            = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'url'             ,'type'=>'varchar@760'];
	public $method         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'method'          ,'type'=>'varchar@50'];
	public $responseheader = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'responseheader'  ,'type'=>'text@'];
	public $requestheader  = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'requestheader'   ,'type'=>'text@'];
	public $request        = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'request'         ,'type'=>'text@'];
	public $response       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'response'        ,'type'=>'text@'];
	public $pagestatus     = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'pagestatus'      ,'type'=>'varchar@50'];
	public $status         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'status'          ,'type'=>'varchar@255'];
	public $debug          = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'debug'           ,'type'=>'mediumtext@'];
	public $apikey         = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'apikey'          ,'type'=>'varchar@200'];
	public $apikeyuserid   = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'apikeyuserid'    ,'type'=>'int@10'];
	public $token          = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'token'           ,'type'=>'varchar@200'];
	public $meta           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'meta'            ,'type'=>'mediumtext@'];
	public $desc           = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'desc'            ,'type'=>'text@'];
	public $visit_id       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'visit'           ,'type'=>'bigint@20'                       ,'foreign'=>'visits@id!id'];
	public $clientip       = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'clientip'        ,'type'=>'int@50'];
	public $createdate     = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'createdate'      ,'type'=>'datetime@!CURRENT_TIMESTAMP'];
	public $datemodified   = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'datemodified'    ,'type'=>'timestamp@'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------foreign
	public function user_id()
	{
		$this->form()->type('select')->name('user_');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function url()
	{
		$this->form()->type('textarea')->name('url')->maxlength('760');
	}
	//--------------------------------------------------------------------------------id
	public function method()
	{
		$this->form()->type('text')->name('method')->maxlength('50');
	}
	//--------------------------------------------------------------------------------id
	public function responseheader()
	{
		$this->form()->type('textarea')->name('responseheader');
	}
	//--------------------------------------------------------------------------------id
	public function requestheader()
	{
		$this->form()->type('textarea')->name('requestheader');
	}
	//--------------------------------------------------------------------------------id
	public function request()
	{
		$this->form()->type('textarea')->name('request');
	}
	//--------------------------------------------------------------------------------id
	public function response()
	{
		$this->form()->type('textarea')->name('response');
	}
	//--------------------------------------------------------------------------------id
	public function pagestatus()
	{
		$this->form()->type('text')->name('pagestatus')->maxlength('50');
	}
	//--------------------------------------------------------------------------------id
	public function status()
	{
		$this->form()->type('textarea')->name('status')->maxlength('255');
	}
	//--------------------------------------------------------------------------------id
	public function debug()
	{
		$this->form()->type('textarea')->name('debug');
	}
	//--------------------------------------------------------------------------------id
	public function apikey()
	{
		$this->form()->type('textarea')->name('apikey')->maxlength('200');
	}
	//--------------------------------------------------------------------------------id
	public function apikeyuserid()
	{
		$this->form()->type('number')->name('apikeyuserid')->min()->max('9999999999');
	}
	//--------------------------------------------------------------------------------id
	public function token()
	{
		$this->form()->type('textarea')->name('token')->maxlength('200');
	}
	//--------------------------------------------------------------------------------id
	public function meta(){}
	//--------------------------------------------------------------------------------id
	public function desc()
	{
		$this->form('#desc')->type('textarea')->name('desc');
	}
	//--------------------------------------------------------------------------------foreign
	public function visit_id()
	{
		$this->form()->type('select')->name('visit_');
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function clientip()
	{
		$this->form()->type('number')->name('clientip')->min()->max('99999999999999999999999999999999999999999999999999');
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