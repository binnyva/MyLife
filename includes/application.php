<?php
setupBackwardCompatibility();

$t_entry = new Entry;
$t_tag = new Tag;
$user = new User;

if((strpos(iframe\App::$config['PHP_SELF'], '/user/') === false) 
	and (strpos(iframe\App::$config['PHP_SELF'], '/system/') === false) 
	and (strpos(iframe\App::$config['PHP_SELF'], '/api/') === false) 
	and (strpos(iframe\App::$config['PHP_SELF'], '/about/') === false)) checkUser();

function checkUser() {
	if(!isset($_SESSION['user_id']) and isset(iframe\App::$config['single_user'])) {
		$_SESSION['user_id'] = iframe\App::$config['single_user'];
	}
	
	if((!isset($_SESSION['user_id']) or !$_SESSION['user_id']))
		iframe\App::showMessage("Please login to use this feature", iframe\App::$config['site_url'] . 'user/login.php', "error");
}

function email($to, $subject, $body, $from = '') {
	//return true; //:DEBUG:
	require("Mail.php");

	if(!$from) $from = "BinnBot <binnbot@gmail.com>";
	
	// SMTP info here!
	$host = "smtp.gmail.com";

	$username = iframe\App::$config['email_username'];
	$password = iframe\App::$config['email_password'];
	
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
	preg_match_all("/#([\w\-]+)/", $body, $matches);

	if($matches) {
		$t_entry->assignTags($entry_id, $matches[1]);
	}
}

function showTags($tags, $prefix = '') {
	if($tags) {
		print $prefix . '<ul class="tags">';
		foreach ($tags as $id => $tag) {
			print "<li><a class='with-icon tag' href='" . iframe\App::$config['app_url'] . "index.php?tag=$tag[name]' style='background-color:$tag[color]'>$tag[name]</a></li>";
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

// Taken from http://snippetsofcode.wordpress.com/2011/08/15/getting-snippets-as-search-results-in-php/ with minimal changes.
function getSnippet($keyword, $txt) {
	$txt = strip_tags($txt);
	$snippet='';
	$span = 50;

	if(!$keyword) return substr($txt, 0, $span * 2);

	preg_match_all("/(\W.{0,$span}\W)($keyword)(\W.{0,$span}\W)/i", "  $txt  ", $matches);
	foreach($matches[0] as $match) {
		if (!$match = trim($match)) continue;
		if (isset($snippet)) $snippet .= "$match..."; else $snippet = "...$match...";
	}
	$snippet = preg_replace("/($keyword)/i", '<mark>$1</mark>', $snippet);
	return $snippet;
}

function summaryTimeframeText($timeframe) {
	$timeframe_str = strtotime($timeframe);
	
	if(substr($timeframe, 9, 2) == "00") {
		return date('F Y', $timeframe_str); // . " month";
	} else {
		return date('M d', $timeframe_str) . " - " . date('M d', strtotime("next saturday", $timeframe_str)) . " week";
	}

	return false;
}