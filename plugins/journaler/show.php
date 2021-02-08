<?php
require('../../common.php');
/**
 * Shows other places where daily data is recorded.
 */

$date = date('Y-m-d', strtotime("yesterday"));
if(!empty($QUERY['date'])) {
	$date = i($QUERY, 'date');
} elseif(!empty($argv[1])) {
	$date = $argv[1];
}
$journal = '';

// Finally, get the list of all the people I met.
$sql = new \iframe\DB\Sql("Project_Friendlee");
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
// $journal .= "<li><a href='http://localhost/tools/FourSquare/?date=$date'>FourSquare</a></li>";
// $journal .= "<li><a href='http://localhost/tools/Expense/index.php?year=".date('Y', strtotime($date)).'&month='.date('Y', strtotime($date)).'&day='.date('d', strtotime($date))."'>Expenses</a></li>";
// $journal .= "<li><a href='http://apps.binnyva.com/tiker/reports/day.php?day=$date'>Tiker</a></li>";
// $journal .= "<li><a href='http://localhost/tools/Twitter/?date=$date'>Twitter</a></li>";
// $journal .= "<li><a href='http://localhost/tools/Prod/table.php?date=2017-05-24&type=todo&timeframe=day'>Habitica</a></li>";
$journal .= "<li><a href='http://localhost/Projects/Friendlee/?date=$date'>Friendlee</a></li>";
$journal .= "<li><a href='https://www.rescuetime.com/browse/activities/by/hour/for/the/day/of/$date'>RescueTime</a></li>";

$journal .= "</ul>";

print trim($journal);

