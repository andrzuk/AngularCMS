<?php

include dirname(__FILE__) . '/../db/connection.php';
include dirname(__FILE__) . '/../db/access.php';

$result = array();

$dbc = connect();

try
{
	if (check_access($dbc)) // if user rights are sufficient, get database content
	{
		$query = 'SELECT * FROM pages WHERE main_page = 1 AND visible = 1 ORDER BY id DESC LIMIT 1';

		$statement = $dbc->prepare($query);

		$statement->execute();

		$result = $statement->fetch(PDO::FETCH_ASSOC);

		$result['main_page'] = $result['main_page'] ? true : false;
		$result['contact_page'] = $result['contact_page'] ? true : false;
		$result['visible'] = $result['visible'] ? true : false;
		$result['category_id'] = intval($result['category_id']);
	}
}
catch (PDOException $e)
{
	$result = array(
		'id' => 1, 
		'main_page' => 1, 
		'contact_page' => 0, 
		'category_id' => 0, 
		'title' => 'Angular CMS - Index', 
		'description' => 'Angular CMS - Index Page',
		'contents' => '<h1>Angular CMS - Home</h1><h2 style="color: red;">Application is not initialized.</h2><h3>Go to <b><a href="/install">Application install page</a></b> and complete its basic settings.</h3>',
		'author_id' => 1,
		'visible' => 1,
		'modified' => '2001-01-01 01:01:01',
	);
}

echo json_encode($result);

?>
