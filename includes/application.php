<?php
$t_user = new DBTable("User");
$t_entry = new Entry;

if((strpos($config['PHP_SELF'], '/user/') === false) 
	and (strpos($config['PHP_SELF'], '/system/') === false) 
	and (strpos($config['PHP_SELF'], '/about/') === false)) checkUser();

function checkUser() {
	global $config;

	if(!isset($_SESSION['user_id'])) {
		$_SESSION['user_id'] = $config['single_user'];
	}
	
	if((!isset($_SESSION['user_id']) or !$_SESSION['user_id']))
		showMessage("Please login to use this feature", $config['site_url'] . 'user/login.php', "error");
}

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
		$tag_id = $sql->insert("Tag", array('name'=>$tag, 'user_id'=>$user_id));
	}

	$sql->insert('EntryTag', array(
		'tag_id' 	=> $tag_id,
		'entry_id'	=> $entry_id,
	));
}

function showTags($tags) {
	global $config;

	if($tags) {
		print ' | Tags: <ul class="tags">';
		foreach ($tags as $id => $tag) {
			print "<li><a class='with-icon tag' href='$config[home_url]index.php?tag=$tag'>$tag</a></li>";
		}
		print '</ul>';
	}
}

// This function will convert the wordwrapped text that Gmail makes to more appropiat HTML formatted text.
function para($text) {
	if(stripos($text,'<p>') !== false or stripos($text,'<br') !== false ) return $text; // Its already HTML format. No further formatting necessary.

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