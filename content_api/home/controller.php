<?php
namespace content_api\home;
use \lib\utility;
use \lib\debug;
use \lib\utility\token;

class controller extends  \mvc\controller
{

	/**
	 * the short url
	 *
	 * @var        string
	 */
	public static $shortURL = \lib\utility\shortURL::ALPHABET;


	/**
	 * check api key
	 * set user id
	 * set permission
	 */
	public function __construct()
	{
		\lib\storage::set_api(true);
		parent::__construct();
	}
}
?>