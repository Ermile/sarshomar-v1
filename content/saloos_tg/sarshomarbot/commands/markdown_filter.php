<?php
namespace content\saloos_tg\sarshomarbot\commands;
class markdown_filter
{
	public static function tag($_string)
	{
		$string = preg_replace("#<#", "&lt;", $_string);
		$string = preg_replace("#>#", "&gt;", $string);
		return $string;
	}

	public static function remove_external_link($_string)
	{
		return preg_replace("#([^\s\.]+)\.([^\s\.]{2,})#i", "$1​<code>.</code>$2", $_string);
		return $_string;
	}

	public static function line_trim($_string)
	{
		return preg_replace("/\n+/", "\n", $_string);
	}
}
?>