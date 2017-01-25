<?php

header('Access-Control-Allow-Origin: *');
header('content-type: application/text; charset=utf-8');

if (isset($_POST['id']))
{
	echo $_POST['id'];
}

?>
