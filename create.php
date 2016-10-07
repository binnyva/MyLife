<?php
require("./common.php");

$date = i($QUERY, 'date', date("Y-m-d", strtotime('yesterday')));
$entry = array(
	'id'	=> 0,
	'date'	=> $date,
	'body'	=> '',
	'tags'	=> array()
);
if($date) { // Entry on given date
	$entry_option = $t_entry->getByDate($date);
	if($entry_option) $entry = $entry_option;
}

$all_tags = $sql->getCol("SELECT T.name
	FROM Tag T 
	WHERE T.user_id=$_SESSION[user_id]
	ORDER BY name");

$template->addResource('bower_components/jquery-ui/ui/minified/jquery-ui.min.js');
$template->addResource('bower_components/jquery-ui/ui/minified/jquery.ui.autocomplete.min.js');
$template->addResource('bower_components/jquery-ui/themes/flick/jquery-ui.min.css');
$template->addResource('bower_components/jquery-ui/themes/flick/jquery.ui.theme.css');
$template->addResource("library/ajaxify.js", "js");
$template->addResource("_autocomplete.js", "js");
$template->addResource("index.js", "js");
render();
