<?php
	if(function_exists('sys_getloadavg'))
	{
		echo "cpu:".sys_getloadavg()[0] . "%\n";
	}
	$free = shell_exec('free');
	if($free)
	{
		$free = shell_exec('free -t');
		$free = (string)trim($free);
		$free_arr = explode("\n", $free);
		$free_arr = preg_replace("/\s{2,}/", " ", $free_arr);
		$mem = explode(" ", $free_arr[3]);
		$mem = array_filter($mem);
		$mem = array_merge($mem);
		$memory_usage = ($mem[2]*100)/$mem[1];
		echo "memory:". round($memory_usage) . "%\n";
	}
	else
	{
		echo "Hi";
	}
?>