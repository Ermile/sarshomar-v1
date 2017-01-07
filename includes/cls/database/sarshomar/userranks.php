<?php
namespace database\sarshomar;
class userranks
{
	public $id               = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'int@10'];
	public $user_id          = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'user'            ,'type'=>'int@10'                          ,'foreign'=>'users@id!user_displayname'];
	public $reported         = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'reported'        ,'type'=>'int@10'];
	public $usespamword      = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'usespamword'     ,'type'=>'int@10'];
	public $changeprofile    = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'changeprofile'   ,'type'=>'int@10'];
	public $improveprofile   = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'improveprofile'  ,'type'=>'int@10'];
	public $report           = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'report'          ,'type'=>'int@10'];
	public $wrongreport      = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'wrongreport'     ,'type'=>'int@10'];
	public $skip             = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'skip'            ,'type'=>'int@10'];
	public $resetpassword    = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'resetpassword'   ,'type'=>'int@10'];
	public $verification     = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'verification'    ,'type'=>'bit@1!b'0''];
	public $validation       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'validation'      ,'type'=>'bit@1!b'0''];
	public $vip              = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'vip'             ,'type'=>'int@10'];
	public $hated            = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'hated'           ,'type'=>'int@10'];
	public $other            = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'other'           ,'type'=>'int@10'];
	public $value            = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'value'           ,'type'=>'bigint@20'];
	public $pollanswered     = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'pollanswered'    ,'type'=>'int@10'];
	public $pollskipped      = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'pollskipped'     ,'type'=>'int@10'];
	public $surveyanswered   = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'surveyanswered'  ,'type'=>'int@10'];
	public $surveyskipped    = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'surveyskipped'   ,'type'=>'int@10'];
	public $mypollanswered   = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'mypollanswered'  ,'type'=>'int@10'];
	public $mypollskipped    = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'mypollskipped'   ,'type'=>'int@10'];
	public $mysurveyanswered = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'mysurveyanswered','type'=>'int@10'];
	public $mysurveyskipped  = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'mysurveyskipped' ,'type'=>'int@10'];
	public $userreferred     = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'userreferred'    ,'type'=>'int@10'];
	public $userverified     = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'userverified'    ,'type'=>'int@10'];
	public $commentcount     = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'commentcount'    ,'type'=>'int@10'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------foreign
	public function user_id()
	{
		$this->form()->type('select')->name('user_')->required();
		$this->setChild();
	}
	//--------------------------------------------------------------------------------id
	public function reported()
	{
		$this->form()->type('number')->name('reported')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function usespamword()
	{
		$this->form()->type('number')->name('usespamword')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function changeprofile()
	{
		$this->form()->type('number')->name('changeprofile')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function improveprofile()
	{
		$this->form()->type('number')->name('improveprofile')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function report()
	{
		$this->form()->type('number')->name('report')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function wrongreport()
	{
		$this->form()->type('number')->name('wrongreport')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function skip()
	{
		$this->form()->type('number')->name('skip')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function resetpassword()
	{
		$this->form()->type('number')->name('resetpassword')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function verification(){}
	//--------------------------------------------------------------------------------id
	public function validation(){}
	//--------------------------------------------------------------------------------id
	public function vip()
	{
		$this->form()->type('number')->name('vip')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function hated()
	{
		$this->form()->type('number')->name('hated')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function other()
	{
		$this->form()->type('number')->name('other')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function value()
	{
		$this->form()->type('number')->name('value')->max('99999999999999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function pollanswered()
	{
		$this->form()->type('number')->name('pollanswered')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function pollskipped()
	{
		$this->form()->type('number')->name('pollskipped')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function surveyanswered()
	{
		$this->form()->type('number')->name('surveyanswered')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function surveyskipped()
	{
		$this->form()->type('number')->name('surveyskipped')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function mypollanswered()
	{
		$this->form()->type('number')->name('mypollanswered')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function mypollskipped()
	{
		$this->form()->type('number')->name('mypollskipped')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function mysurveyanswered()
	{
		$this->form()->type('number')->name('mysurveyanswered')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function mysurveyskipped()
	{
		$this->form()->type('number')->name('mysurveyskipped')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function userreferred()
	{
		$this->form()->type('number')->name('userreferred')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function userverified()
	{
		$this->form()->type('number')->name('userverified')->min()->max('9999999999')->required();
	}
	//--------------------------------------------------------------------------------id
	public function commentcount()
	{
		$this->form()->type('number')->name('commentcount')->min()->max('9999999999')->required();
	}
}
?>