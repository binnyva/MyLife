<?php
require("./common.php");

$date = i($QUERY, 'date', date("Y-m-d", strtotime('yesterday')));
$entry = array(
	'id'	=> 0,
	'date'	=> $date,
	'body'	=> '',
);
if($date) { // Entry on given date
	$entry_option = $t_entry->getByDate($date);
	if($entry_option) $entry = $entry_option;
}

$template->addResource("library/ajaxify.js", "js");
render();
