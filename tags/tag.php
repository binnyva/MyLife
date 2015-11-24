<?php
require '../common.php';

$tag = i($QUERY, 'tag');
if(!$tag) die("No Tag provided");

$page_title = "Distribution for '$tag'";

$freq =  $sql->getAll("SELECT E.date
	FROM Tag T 
	INNER JOIN EntryTag ET ON ET.tag_id=T.id
	INNER JOIN Entry E ON ET.entry_id=E.id
	WHERE T.user_id=$_SESSION[user_id] AND T.name='$tag'
	ORDER BY E.date");

// Finds the longest streak and the longest gaps.
$longest_streak = 0;
$longest_streak_to = '';
$longest_gap = 0;
$longest_gap_to = '';

$last_date = '';
$current_streak = 1; // Because first day is included in the streak
$i = 0;
foreach ($freq as $row) {
	$date = $row['date'];
	$yesterday = date("Y-m-d", strtotime($date) - (24 * 60 * 60));
	if($yesterday == $last_date) { // Streak goes on
		$current_streak++;

	} else { // Streak break.
		// Find the gap between the break and the last day.
		$datetime1 = date_create(date('Y-m-d', strtotime($last_date) + (24 * 60 * 60)));// One day after the event happened...
		$datetime2 = date_create(date('Y-m-d', strtotime($yesterday)));// to one day before the event happened next.
		$interval = date_diff($datetime1, $datetime2);
		$gap = $interval->format('%a') + 1; // Because the first day is included in the gap.
		if($gap > $longest_gap and $i) { // 'and $i' to make sure that the first day is ignored. 
			$longest_gap = $gap;
			$longest_gap_to = $yesterday; // Gap was till yesterday. Today we had a hit.
		}

		if($current_streak > $longest_streak) {
			$longest_streak = $current_streak;
			$longest_streak_to = $last_date; // Streak record was for the last event streak.
		}
		$current_streak = 1;
	}

	$last_date = $date;
	$i++;
}


render();
