<?php

function get_token()
{
	if (!function_exists('getallheaders')) 
	{
		function getallheaders() 
		{
			$headers = array();

			foreach ($_SERVER as $name => $value) 
			{
				if (substr($name, 0, 5) == 'HTTP_')
				{
					$headers[str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($name, 5))))] = $value;
				} 
			} 
			return $headers; 
		} 
	}

	$all_headers = getallheaders();
	$headers = array_change_key_case($all_headers, CASE_LOWER);
	
	$token = NULL;
	$access_token_key = 'x-access-token';
	
	if (array_key_exists($access_token_key, $headers))
	{
		// check token:
		$token = $headers[$access_token_key];
	}
	
	return $token;
}

function clear_token($dbc, $token)
{
	$mask = 'Logged out';
	
	$query =	'UPDATE users' .
				' SET token = :mask' .
				' WHERE token = :token';

	$statement = $dbc->prepare($query);
	
	$statement->bindParam(':mask', $mask, PDO::PARAM_STR);
	$statement->bindParam(':token', $token, PDO::PARAM_STR);

	$statement->execute();
}

?>
