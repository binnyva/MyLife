<?php
require '../common.php';

$date = date('l, dS M, Y');

$body = <<<END
Hi %NAME%

What happened on $date?

Just reply to this email with your entry.

--
MyLife
END;

$users = $sql->getAll("SELECT id,email,name FROM User");
foreach ($users as $user) {
	$replaces = array(
		'%NAME%'	=> $user['name'],
	);

	$body = str_replace(array_keys($replaces), array_values($replaces), $body);

	@email($user['email'], "What Happened on $date", $body);	
}
