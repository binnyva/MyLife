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

function createEntry($body, $user_id, $date, $subject='') {
	// Check if already there.
	$exists = $sql->from('Entry')->find("user_id=$user_id AND `date`='$date'");
	if($exists) {
		// Entry for the date exists. Don't Enter again
		print "Entry for $date Exists\n";
		return $exists[0]['id'];
	}

	$locked = 0;
	if(strpos($body, 'LOCKED') !== false) $locked = 1;

	$insert_id = $sql->insert("Entry", array(
			'body'		=> $body,
			'date'		=> $date,
			'title'		=> $subject,
			'added_on'	=> 'NOW()',
			'locked'	=> $locked,
			'user_id'	=> $user_id,
		));
	parseTags($body, $insert_id);

	return $insert_id;
}

function editEntry($entry_id, $body, $user_id=0, $date='',  $subject='') {
	global $sql;

	$locked = 0;
	if(strpos($body, 'LOCKED') !== false) $locked = 1;

	$data = array(
		'body'	=> $body,
		'locked'=> $locked,
	);

	if($user_id) $data['user_id'] = $user_id;
	if($date) $data['date'] = $date;
	if($subject) $data['subject'] = $subject;

	$sql->update("Entry", $data, "id=$entry_id");
	parseTags($body, $entry_id);

	return $entry_id;
}

function parseTags($body, $entry_id) {
	preg_match_all("/#(\w+)/", $body, $matches);

	if($matches) {
		saveAllTags($entry_id, $matches[1]);
	}
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


// This function will convert the wordwrapped text that Gmail makes to more appropiat HTML formatted text.
function para($text) {
	$lines = explode("\n", $text);

	// First remove all the singular \n's. Do that by removing every \n and putting it back in if the line is empty.
	for($i=0; $i<count($lines); $i++) {
		$lines[$i] = trim($lines[$i]);

		if(!$lines[$i]) $lines[$i] = "\n";
	}
	$text = implode(' ', $lines);

	// Now put a <p> around non emptly lines.
	$lines = explode("\n", $text);
	for($i=0; $i<count($lines); $i++) {
		$lines[$i] = trim($lines[$i]);

		if($lines[$i]) $lines[$i] = "<p>" . $lines[$i] ."</p>\n";
	}
	$formated_text = implode('', $lines);

	return $formated_text;
}