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

$all_tags = $t_tag->getAll();

$template->addResource('bower_components/jquery-ui/ui/minified/jquery-ui.min.js');
$template->addResource('bower_components/jquery-ui/ui/minified/jquery.ui.autocomplete.min.js');
$template->addResource('bower_components/jquery-ui/themes/flick/jquery-ui.min.css');
$template->addResource('bower_components/jquery-ui/themes/flick/jquery.ui.theme.css');
$template->addResource("library/ajaxify.js", "js");
$template->addResource("_autocomplete.js", "js");
$template->addResource("index.js", "js");

$page_title = 'Create Entry';
render();
