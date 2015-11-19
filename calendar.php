<?php
include('common.php');
$calendar = new Calendar('day');

$curmonth = ($calendar->month < 10) ? "0$calendar->month" : $calendar->month;
$curyear  = $calendar->year;

//Get all the tasks and reminders for a whole month
$all_entries = $t_entry->getMonth($curmonth. "-".$curyear); 

render();

function day($year, $month, $day) {
	global $all_entries, $t_entry;

	//Find what all will happen on that day.
	$this_day = "$year-$month-$day";

	if(!empty($all_entries[$this_day])) {
		print "<a href='index.php?entry_id=".$all_entries[$this_day]['id']."'>Entry</a>";
		$tags = $t_entry->getTags($all_entries[$this_day]['id']);
		if($tags) {
			print "<br /><ul class='tags'>";
			foreach($tags as $tag) {
				print "<li><a href='index.php?tag=$tag' rel='tag' class='with-icon tag'>$tag</a></li>";
			}
			print "</ul>";
		}
	}
	elseif($this_day < date('Y-m-d')) print "<a href='create.php?date=".$this_day."' class='with-icon edit'>Create Entry...</a>";
}

