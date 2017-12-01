<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$message = NULL;
$success = false;

$dbc = connect();

$id = intval($_GET['id']);
$author = intval($_GET['author']);

if ($author == $id) // user allowed to lock himself
{
    $query = 'UPDATE users SET active = 0 WHERE id = :id';

    $statement = $dbc->prepare($query);

    $statement->bindValue(':id', $id, PDO::PARAM_INT); 

    $statement->execute();

    if ($statement->rowCount())
    {
        $message = 'Konto zostało usunięte.';
        $success = true;
    }
    else
    {
        $message = 'Konto nie zostało usunięte.';
        $success = false;
    }
}
else // not allowed to lock another user
{
    $message = 'Brak uprawnień do wykonania uruchomionej akcji.';
    $success = false;
}

$result = array('success' => $success, 'message' => $message);

echo json_encode($result);

?>
