<?php
header("Content-Type: text/plain");
if(isset($_GET['error']))
{
	$file = database.'log/error.sql';
}
else
{
	$file = database.'log/log.sql';

}
if(isset($_GET['clear']))
{
	file_put_contents($file, '');
}
echo file_get_contents($file);
exit();
?>