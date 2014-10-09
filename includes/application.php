<?php
function email($to, $subject, $body, $from = '') {
	//return true; //:DEBUG:
	global $config;
	require("Mail.php");

	if(!$from) $from = "BinnBot <binnbot@gmail.com>";
	
	// SMTP info here!
	$host = "smtp.gmail.com";

	$username = $config['email_username'];
	$password = $config['email_password'];
	
	$headers = array ('From' => $from,
		'To' => $to,
		'Subject' => $subject);
	$smtp = Mail::factory('smtp',
		array ('host' => $host,
			'auth' => true,
			'username' => $username,
			'password' => $password));
	
	$mail = $smtp->send($to, $headers, $body);
	
	if (PEAR::isError($mail)) {
		echo("<p>" . $mail->getMessage() . "</p>");
		return false;
	}
	
	return true;
}

function saveAllTags($entry_id, $all_tags) {
	global $sql;

	$sql->remove('EntryTag', array('entry_id' => $entry_id));
	foreach ($all_tags as $tag) {
		saveTag($entry_id, $tag);
	}
}

function saveTag($entry_id, $tag) {
	global $sql;

	$user_id = $_SESSION['user_id'];
	$tag_id = $sql->getOne("SELECT id FROM Tag WHERE name='$tag' AND user_id=$user_id");
	if(!$tag_id) {
		$sql->insert("Tag", array('name'=>$tag, 'user_id'=>$user_id));
	}

	$sql->insert('EntryTag', array(
		'tag_id' 	=> $tag_id,
		'entry_id'	=> $entry_id,
	));
}