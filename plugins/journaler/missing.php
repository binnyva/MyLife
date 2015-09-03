<?php
require('../../common.php');

$all_posts = $t_entry->where(array("user_id"=>$_SESSION['user_id']))->sort("date DESC")->get();

$index = keyFormat($all_posts, array('date', 'body'));

$from_date = \DateTime::createFromFormat('Y-m-d', '2014-01-01');
$to_date = \DateTime::createFromFormat('Y-m-d', date('Y-m-d', strtotime("Yesterday")));

$period = new \DatePeriod(
	$from_date,
	new \DateInterval('P1D'),
	$to_date->modify('+1 day')
);

$all_dates = array();
foreach($period as $date) {
    $all_dates[] = $date->format('Y-m-d');
}
$all_dates = array_reverse($all_dates);


$missing_dates = array();
foreach ($all_dates as $date) {
	if(!isset($index[$date])) {
		$missing_dates[] = $date;
	}
}

render(joinPath($config['site_folder'],'plugins/journaler/templates/missing.php'), true, true);