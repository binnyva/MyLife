<?php
require("./common.php");

if(isset($QUERY['entry_id'])) { // Individual entry
	$entries = array($t_entry->getEntry($QUERY['entry_id']));

} elseif(isset($QUERY['date'])) { // Entry on given date
	$entries = array($t_entry->getByDate($QUERY['date']));

} elseif(isset($QUERY['tag'])) { // Entry by tag
	$entries = $t_entry->getByTag($QUERY['tag']);

} else { // The latest 10 entries.
	$entries = $t_entry->getLatest();
}

if(!$entries) {
	$QUERY['error'] = "Can't find any entries.";
	$entries = $t_entry->getLatest();
}

$all_tags = $t_tag->getAll();

$template->addResource('bower_components/jquery-ui/ui/minified/jquery-ui.min.js');
$template->addResource('bower_components/jquery-ui/ui/minified/jquery.ui.autocomplete.min.js');
$template->addResource('bower_components/jquery-ui/themes/flick/jquery-ui.min.css');
$template->addResource('bower_components/jquery-ui/themes/flick/jquery.ui.theme.css');
$template->addResource("_autocomplete.js", "js");
render();
