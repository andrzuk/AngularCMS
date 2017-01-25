<?php

function get_setting_value($dbc, $setting_key)
{
	$key_name = $setting_key;

	$query = 'SELECT key_value FROM settings WHERE key_name = :key_name';

	$statement = $dbc->prepare($query);

	$statement->bindValue(':key_name', $key_name, PDO::PARAM_STR); 

	$statement->execute();

	$result = $statement->fetch(PDO::FETCH_ASSOC);

	return $result['key_value'];
}

?>
