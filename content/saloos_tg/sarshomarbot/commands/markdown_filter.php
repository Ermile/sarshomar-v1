<?php
namespace content\saloos_tg\sarshomarbot\commands;
class markdown_filter
{
	public static function link($_string)
	{
		return preg_replace_callback("#\[([^\]]+)\](\([^())]+\))#Ui", function($_str){
		  $_text = $_str[1];
		  $_link = $_str[2];
		  $_text = addcslashes($_text, "[");
		  return '\[' . $_text .']'.$_link;
		}, $_string);
	}

	public static function remove_external_link($_string)
	{
		return preg_replace("#([^\s]+\.[^\s]{2,})#i", "`$0`", $_string);
	}

	public static function bold($_string)
	{
		return addcslashes($_string, "*");
	}

	public static function italic($_string)
	{
		return addcslashes($_string, "_");
	}

	public static function line_trim($_string)
	{
		return preg_replace("/\n+/", "\n", $_string);
	}
}
?>