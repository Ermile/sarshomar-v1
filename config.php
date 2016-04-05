<?php
/**
 @ In the name Of Allah
 * The base configurations of the SAMAC.
 * This file has the configurations of MySQL settings and useful core settings
 */

// ** MySQL settings - You can get this info from your web host ** //
 /** The name of the database */
if(!defined('db_name'))
 define("db_name", '_db_');

 /** MySQL database username */
if(!defined('db_user'))
 define("db_user", '_user_');

 /** MySQL database password */
if(!defined('db_pass'))
 define("db_pass", '_pass_');

/**
 * Defaultlanguage
 * Default: 'en_US'
 *
 * If your site support multi language then you can fix default language for visitors
 */
define('DefaultLanguage', 'en_US');


/**
 * LangList
 * Default serialize (['fa_IR' => 'فارسی', 'en_US' => 'English'])
 *
 * List of Site languages
 */
define('LangList', serialize (['fa_IR' => 'فارسی', 'en_US' => 'English']));

?>