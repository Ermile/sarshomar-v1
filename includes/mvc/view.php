<?php
namespace mvc;

class view extends \lib\mvc\view
{
	function _construct()
	{
		// define default value for global

		$this->data->site['title']   = T_("Ermile");
		$this->data->site['desc']    = T_("Ermile is new");
		$this->data->site['slogan']  = T_("Ermile is our company");

		$this->data->page['desc']    = T_("Ermile is Inteligent.");

		if(! ($this->url('sub') === 'cp' || $this->url('sub') === 'account') )
			$this->url->MainStatic       = false;

		// add language list for use in display
		// $this->global->langlist		= array(
		// 										'fa_IR' => 'فارسی',
		// 										'en_US' => 'English',
		// 										'de_DE' => 'Deutsch'
		// 										);


		// if you need to set a class for body element in html add in this value
		// $this->data->bodyclass      = null;

		if(method_exists($this, 'options')){
			$this->options();
		}
	}
}
?>