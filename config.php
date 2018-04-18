<?php
/**
 @ In the name Of Allah
 * The base configurations of the SAMAC.
 * This file has the configurations of MySQL settings and useful core settings
 */

// ** MySQL settings - You can get this info from your web host ** //
 /** The name of the database */
if(!defined('db_name'))
 define("db_name", '__your_db__');

 /** MySQL database username */
if(!defined('db_user'))
 define("db_user", '__your_user__');

 /** MySQL database password */
if(!defined('db_pass'))
 define("db_pass", '__your_pass__');

define('subDevelop', 'dev');

// define short url alphabet
if(!defined('SHORTURL_ALPHABET'))
{
	// define('SHORTURL_ALPHABET', 'SQ2ksPytvBzCDNb4G56cdHJK3wxqLMYZjmn8pWXrRTV79fghF');
	define('SHORTURL_ALPHABET', 'SQ2ksPyRTV79fghFtvBzCDNb4G56cdZjmn8pWXrHJK3wxqLMY');
}
?>