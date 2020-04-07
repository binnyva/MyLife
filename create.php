<?php
require("./common.php");

$date = i($QUERY, 'date', date("Y-m-d", strtotime('yesterday')));
$entry = [
	'id'	=> 0,
	'date'	=> $date,
	'title'	=> '',
	'body'	=> '',
	'tags'	=> []
];
if($date) { // Entry on given date
	$entry_option = $t_entry->getByDate($date);
	if($entry_option) $entry = $entry_option;
}

$all_tags = $t_tag->getAll();

iframe\App::$template->addResource('bower_components/jquery-ui/ui/minified/jquery-ui.min.js');
iframe\App::$template->addResource('bower_components/jquery-ui/ui/minified/jquery.ui.autocomplete.min.js');
iframe\App::$template->addResource('bower_components/jquery-ui/themes/flick/jquery-ui.min.css');
iframe\App::$template->addResource('bower_components/jquery-ui/themes/flick/jquery.ui.theme.css');
iframe\App::$template->addResource("library/ajaxify.js", "js");
iframe\App::$template->addResource("_autocomplete.js", "js");
iframe\App::$template->addResource("index.js", "js");

$page_title = 'Create Entry';
render();
