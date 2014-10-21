<?php
include('common.php');
$calendar = new Calendar('day');

$curmonth = ($calendar->month < 10) ? "0$calendar->month" : $calendar->month;
//Get all the tasks and reminders for a whole month
$all_entries = $t_entry->getMonth($curmonth. "-".date('Y')); 

render();

function day($year, $month, $day) {
	global $all_entries;

	//Find what all will happen on that day.
	$this_day = "$year-$month-$day";

	if(!empty($all_entries[$this_day])) print "<a href='index.php?entry_id=".$all_entries[$this_day]['id']."'>Entry</a>";
}

