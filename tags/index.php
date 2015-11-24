<?php
require '../common.php';

$show_max_count	= 20;

$all_tags = $sql->getById("SELECT T.name, COUNT(ET.id) AS count 
	FROM Tag T 
	INNER JOIN EntryTag ET ON ET.tag_id=T.id
	WHERE T.user_id=$_SESSION[user_id]
	GROUP BY ET.tag_id
	ORDER BY count DESC, name");

$largest = reset($all_tags);
$smallest = end($all_tags);

$data = array();
$i = 0;
foreach ($all_tags as $tag => $count) {
	$data[$tag] = array(
			'tag'		=> $tag,
			'count'		=> $count,
			'percentage'=> round($count / $largest * 100, 2)
		);
	$i++;
	if($i > $show_max_count) break;
}

dump($data);