<?php
	if(function_exists('sys_getloadavg'))
	{
		echo "cpu:".sys_getloadavg()[0] . "%\n";
	}
	$free = shell_exec('free');
	if($free)
	{
		$free = (string)trim($free);
		$free_arr = explode("\n", $free);
		$mem = explode(" ", $free_arr[1]);
		$mem = array_filter($mem);
		$mem = array_merge($mem);
		$memory_usage = $mem[2]/$mem[1]*100;
		echo "memory:". round($memory_usage) . "%\n";
	}
	else
	{
		echo "Hi";
	}
?>