<?php
require("./common.php");

$entry = array(
	'id'	=> 0,
	'date'	=> i($QUERY, 'date', date("Y-m-d", strtotime('yesterday'))),
	'body'	=> '',
);
if(isset($QUERY['date'])) { // Entry on given date
	$entry_option = $t_entry->getByDate($QUERY['date']);
	if($entry_option) $entry = $entry_option;
}

render();
