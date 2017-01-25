<?php

include dirname(__FILE__) . '/../db/connection.php';
include dirname(__FILE__) . '/../db/setting.php';
include dirname(__FILE__) . '/../db/config.php';

$credentials = $_POST;

$message = NULL;
$success = false;

if (!empty($credentials['email'])) 
{
	$email = $credentials['email'];
	
	$db_connection = connect();

	$query = ' SELECT * FROM users' .
	'          WHERE email = :email' .
	'          AND active = 1';

	$statement = $db_connection->prepare($query);

	$statement->bindParam(':email', $email, PDO::PARAM_STR);
	
	$statement->execute();
	
	$row_item = $statement->fetch(PDO::FETCH_ASSOC);

	if (is_array($row_item))
	{
		if (array_key_exists('id', $row_item))
		{
			$id = $row_item['id'];
			$login = $row_item['login'];

			$length = 8;
			$code = md5(uniqid(rand(), true));
			$new_password = substr($code, 0, $length);
			$password = sha1($new_password);

			$query = ' UPDATE users' .
			'          SET password = :password' .
			'          WHERE id = :id';

			$statement = $db_connection->prepare($query);

			$statement->bindParam(':password', $password, PDO::PARAM_STR);
			$statement->bindParam(':id', $id, PDO::PARAM_INT);

			$statement->execute();

			$email_host = get_setting_value($db_connection, 'email_host');
			$email_port = get_setting_value($db_connection, 'email_port');
			$email_sender_address = get_setting_value($db_connection, 'email_sender_address');
			$email_password = get_setting_value($db_connection, 'email_password');
			$email_password_subject = get_setting_value($db_connection, 'email_password_subject');
			$email_password_body = get_setting_value($db_connection, 'email_password_body');
			$email_sender_name = get_setting_value($db_connection, 'email_sender_name');
			$email_admin_name = get_setting_value($db_connection, 'email_admin_name');
			$app_brand = get_setting_value($db_connection, 'app_brand');
			$app_domain = get_setting_value($db_connection, 'app_domain');

			$mail_body = "Szanowny Użytkowniku,<br><br>". $email_password_body ."<br><br> login: <b>". $login ."</b><br> e-mail: <b>". $email ."</b><br> hasło: <b>". $new_password ."</b><br><br>Pozdrawiamy,<br><br>". $app_brand ."<br>". $app_domain ."<br>";

			include MAILER_DIR . 'class.phpmailer.php';
			include MAILER_DIR . 'class.smtp.php';

			$mail = new PHPMailer();

			$mail->IsSMTP();
			$mail->SMTPDebug = 0;
			$mail->SMTPAuth = true;
			$mail->Host = $email_host;
			$mail->Port = $email_port;
			$mail->Username = $email_sender_address;
			$mail->Password = $email_password;
			$mail->Subject = $email_password_subject;
			$mail->Body = $mail_body;
			$mail->AltBody = $mail_body;
			$mail->CharSet = "UTF-8";
			$mail->SetFrom($email_sender_address, $email_admin_name);
			$mail->AddAddress($email, "Konto serwisu ". $app_brand);
			$mail->Send();

			$success = true;
			$message = 'Twoje hasło do serwisu zostało poprawnie zmienione.';
		}
	}
	else
	{
		$message = 'Konto o podanym adresie e-mail nie zostało znalezione.';
	}
}

$result = array('user_id' => $id, 'success' => $success, 'message' => $message);

echo json_encode($result);

?>
