<?php
require('iframe.php');
/**
 * Automatically generates journal entries that can be sent to Ohlife using the data collected by various collection systems.
 */

$date = date('Y-m-d', strtotime("yesterday"));
if(empty($argv[1])) {
	//die("Usage : php Journaler.php <date>\n");

	$date = i($QUERY, 'date');
} else {
	$date = $argv[1];
}

$journal = "<h2>What I did on $date</h2>";
$locked = false;

$tiker = new Sql("Project_Tiker");
// Things I did that day...
$things_i_did = $tiker->getAll("SELECT T.id,T.name,D.from_time,D.to_time FROM Task T INNER JOIN Duration D ON T.id=D.task_id WHERE DATE(D.from_time)='$date' OR DATE(D.to_time)='$date' ORDER BY D.from_time");
if($things_i_did) $journal .= "<h3>Tasks</h3><ul>";
foreach($things_i_did as $task) {
	$journal .= "<li>$task[name] - " . date("h:i A", strtotime($task['from_time'])) . " to " . date("h:i A", strtotime($task['to_time'])) . "</li>";
}
if($things_i_did) $journal .= "</ul>";


$sql = new Sql('Data');

//Find location from Travel on that date...
$travel = $sql->getAssoc("SELECT TJ.name,TP.name AS place,note,start_on FROM Travel_Journey TJ INNER JOIN Travel_Place TP ON travel_place_id=TP.id WHERE '$date' BETWEEN start_on AND end_on");
if($travel) {
	$journal .= "<h3>At $travel[place]</h3>";
	if($date == $travel['start_on']) {
		$journal .= "<p>" . $travel['note'] . "</p>";
	}
}

//Find the expences for that day.
$expences = $sql->getAll("SELECT info, amount FROM Expense WHERE DATE(added_on)='$date'");
if($expences) {
	$journal .= "<h3>Expenses</h3><ul>";
	foreach($expences as $exp) {
		$journal .= "<li>$exp[info] - $exp[amount]</li>";
	}
	$journal .= "</ul>";
}

// Tasks.
$tasks = $sql->getAll("SELECT task FROM Habitica_Task WHERE DATE(completed_on)='$date' AND type='todo'");
if($tasks) {
	$journal .= "<h3>Habitica Tasks</h3><ul>";
	foreach($tasks as $task) {
		$journal .= "<li>$task[task]</li>";
	}
	$journal .= "</ul>";
}

// Find all the places I visited using FourSquare.
$fs = $sql->getAll("SELECT place,checkin_on FROM Foursquare WHERE DATE(checkin_on)='$date' ORDER BY checkin_on");
if($fs) {
	print "<h3>Foursquare Checkins</h3><ul>";
	foreach($fs as $place) {
		$journal .= "<li>" . $place['place'] . ': ' . date('g A', strtotime($place['checkin_on'])) . '</li>';
	}
	print "</ul>";
}

// Finally, get the list of all the people I met.
$sql = new Sql("Project_Friendlee");
$met = $sql->getAll("SELECT C.id,C.location,C.note FROM Connection C WHERE C.type='met' AND DATE(C.start_on)='$date' AND C.user_id=1");
if($met) {
	$journal .= "<h3>Met</h3><ul>";

foreach($met as $m) {
	$people = $sql->getAll("SELECT P.nickname,P.sex FROM Person P 
		INNER JOIN PersonConnection PC ON P.id=PC.person_id 
		WHERE PC.connection_id='$m[id]'");

	$people_i_met = '';
	while($person = array_pop($people)) {
		$name_parts = explode(" ", $person['nickname']);
		$first_name = reset($name_parts);

		$people_i_met .= $person['nickname'];
		if(count($people) > 1) $people_i_met .= ', ';
		elseif(count($people) == 1) $people_i_met .= ' and ';
	}

	$journal .= "<li>$people_i_met " . ($m['location'] ? ' at ' . $m['location'] : '') . ($m['note'] ? "($m[note])." : '');
}
$journal .= "</ul>";
}


$journal .= "<h3>Things that happened on '$date'</h3><ul>";
$journal .= "<li><a href='https://www.google.co.in/maps/timeline?pb=!1m2!1m1!1s$date'>Timeline</a></li>";
$journal .= "<li><a href='http://localhost/tools/FourSquare/?date=$date'>FourSquare</a></li>";
$journal .= "<li><a href='http://localhost/tools/Expense/index.php?year=".date('Y', strtotime($date)).'&month='.date('Y', strtotime($date)).'&day='.date('d', strtotime($date))."'>Expenses</a></li>";
$journal .= "<li><a href='http://apps.binnyva.com/tiker/reports/day.php?day=$date'>Tiker</a></li>";
$journal .= "<li><a href='http://localhost/tools/Twitter/?date=$date'>Twitter</a></li>";
$journal .= "<li><a href='http://localhost/Projects/Friendlee/?date=$date'>Friendlee</a></li>";
$journal .= "<li><a href='http://localhost/tools/Prod/table.php?date=2017-05-24&type=todo&timeframe=day'>Habitica</a></li>";

$journal .= "</ul>";

print trim($journal);

