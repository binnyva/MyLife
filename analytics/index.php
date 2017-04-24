<?php
require('../common.php');

$from = i($QUERY,'from', date('Y-m-d', strtotime('last month')));
$to = i($QUERY,'to', date('Y-m-d'));
$search_term = i($QUERY,'search_term', '');
$tags = i($QUERY,'tags', '');
$posts = array();
$total = 0;

if(i($QUERY, 'action')) {
	$interval = date_diff(date_create($from), date_create($to));
	$gap = $interval->format('%a');
	$weeks = $gap / 7;
	$months = $gap / 30;

	// dump($gap);exit;

	$checks = array('user_id' => "E.user_id=$_SESSION[user_id]");
	$table_joins = array();
	if($from) {
		$checks['from'] = "E.date >= '$from'";
	}
	if($to) {
		$checks['to'] = "E.date <= '$to'";
	}
	if($search_term) {
		$checks['search_term'] = "MATCH(E.body) AGAINST('$search_term' IN BOOLEAN MODE)";
		// $checks['search_term'] = "E.body LIKE '%$search_term%'";
	}
	if($tags) {
		// $table_joins['tags'] = "INNER JOIN EntryTag ET ON E.id=ET.entry_id INNER JOIN Tag T ON ET.tag_id=T.id";
		$tag_arr = preg_split("/\s*,\s*/", $tags);
		// $checks['tag'] = "T.name IN ('" . implode("','", $tag_arr) . "')";
		$checks['tag'] = "E.id IN (SELECT ET.entry_id
									FROM EntryTag ET 
									JOIN Tag T ON T.id=ET.tag_id
									WHERE T.name IN ('" . implode("','", $tag_arr) . "')
									GROUP BY ET.entry_id
									HAVING COUNT(DISTINCT T.name) = " . count($tag_arr) . ")";
		print $checks['tag'];

		// foreach ($tag_arr as $t) {
		// 	$checks['tag_'.$t] = "T.name='$t'";
		// }
	}

	if($checks and i($QUERY, 'action')) {
		$posts = $sql->getAll("SELECT E.* FROM Entry E
								" . implode(" ", array_values($table_joins)) . "
								WHERE " . implode(" AND ", array_values($checks)));
		$total = count($posts);

		// dump($posts); exit;
	}
}

$all_tags = $t_tag->getAll();
$html = new HTML;

$template->addResource('bower_components/jquery-ui/ui/minified/jquery-ui.min.js');
$template->addResource('bower_components/jquery-ui/ui/minified/jquery.ui.autocomplete.min.js');
$template->addResource('bower_components/jquery-ui/themes/flick/jquery-ui.min.css');
$template->addResource('bower_components/jquery-ui/themes/flick/jquery.ui.theme.css');
$template->addResource("_autocomplete.js", "js");
$template->addResource("index.js", "js");

render();
