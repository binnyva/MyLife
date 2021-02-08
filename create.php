<?php
require("./common.php");

$date = i($QUERY, 'date', date("Y-m-d", strtotime('yesterday')));
$summary_timeframe = i($QUERY, 'summary_timeframe');
$entry = [
	'id'	=> 0,
	'date'	=> $date,
	'summary_timeframe'	=> null,
	'title'	=> '',
	'body'	=> '',
	'tags'	=> []
];

$existing_entry = null;
if($summary_timeframe) {
	$existing_entry = $t_entry->getBySummaryTimeframe($summary_timeframe);
	if(!$existing_entry) {
		$entry['date'] = null;
		$entry['summary_timeframe'] = $summary_timeframe;
	}

} elseif($date) { // Entry on given date
	$existing_entry = $t_entry->getByDate($date);
}

if($existing_entry) $entry = $existing_entry;

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
