<?php
include('common.php');
$calendar = new iframe\HTML\Calendar('day');

$calendar->setAfterWeekCell('postWeekCell');

$show_tags_raw = i($QUERY, 'tags');
$show_tags = array();
if($show_tags_raw) {
	$show_tags = preg_split('/\s*,\s*/', $show_tags_raw);
	$calendar->link_template = "?year=%YEAR%&amp;month=%MONTH%&tags=$show_tags_raw";
}

$curmonth = ($calendar->month < 10) ? "0$calendar->month" : $calendar->month;
$curyear  = $calendar->year;

//Get all the tasks and reminders for a whole month
$all_entries = $t_entry->getMonth($curmonth. "-".$curyear);

$page_title = 'Calendar';
render();

function day($year, $month, $day) {
	global $all_entries, $t_entry, $show_tags;

	//Find what all will happen on that day.
	$this_day = "$year-$month-$day";

	if(!empty($all_entries[$this_day])) {
		$title = 'Entry';
		if($all_entries[$this_day]['title']) $title = $all_entries[$this_day]['title'];

		print "<a href='index.php?entry_id=".$all_entries[$this_day]['id']."' class='calendar with-icon'>" . $title . "</a><br />";
		$tags = $t_entry->getTags($all_entries[$this_day]['id']);
		foreach ($tags as $id => $t) {
			if($show_tags and (!in_array($t['name'], $show_tags))) unset($tags[$id]);
		}
		showTags($tags);
	}
	elseif($this_day <= date('Y-m-d')) print "<a href='create.php?date=".$this_day."' class='with-icon edit'>Create Entry...</a>";
}

function postWeekCell($date) {
	global $t_entry;
	$summary = $t_entry->getBySummaryTimeframe($date);

	if(!count($summary)) print "<a href='create.php?summary_timeframe=".$date."' class='with-icon edit'>Write Summary...</a>";
	else print "<a href='create.php?summary_timeframe=".$date."' class='with-icon done'>{$summary['title']}</a>";
}