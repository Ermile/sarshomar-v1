<?php
include 'core.php';
$x = 2;
$listen = core('https://sarshomar.com/static/usage/resource.php');
echo "\n";
$alert = [];
$cr = false;
if(!isset($listen['cpu']))
{
	$alert['cpu'] = "not found";
}
else
{
	$cpu = floatval(str_replace("%", "", $listen['cpu']));
	if($cpu > 80)
	{
		$alert['cpu'] = "🖥 CPU *$cpu%*";
		$cr = true;
	}
	elseif($cpu > 50)
	{
		$alert['cpu'] = "🖥 CPU *$cpu%*";
	}
}
if(!isset($listen['memory']))
{
	$alert['memory'] = "not found";
}
else
{
	$memory = floatval(str_replace("%", "", $listen['memory']));
	if($memory > 90)
	{
		if(!isset($alert['cpu']))
		{
			$alert['cpu'] = "🖥 CPU *$cpu%*";
		}
		$alert['memory'] = "📱 RAM *$memory%*";
		$cr = true;
	}
	elseif($memory > 60)
	{
		$alert['memory'] = "📱 RAM *$memory%*";
	}
}
if(!empty($alert))
{
	if(!isset($alert['memory']))
	{
		$alert['memory'] = "📱 RAM: *$memory%*";
	}
	$text = $cr ? "🆘 " : "❗";
	$text .= "*Sarshomar server*\n\n";
	foreach ($alert as $key => $value) {
		$text .= "$value \n";
	}
	$data = [
	'chat_id' => "-1001107234508",
	"text" => $text,
	"parse_mode" => 'markdown'
	];
	$callback = \lib\utility\error_logger::log($data);
	print_r($callback);
}
?>