<?php
require("./common.php");

$entries = $sql->getAll("SELECT id,body,`date` FROM Entry WHERE user_id=$_SESSION[user_id] ORDER BY `date` DESC LIMIT 0,10");

render();


function getTags($entry_id) {
	global $sql;

	$tags = $sql->getById("SELECT T.id,T.name FROM Tag T INNER JOIN EntryTag ET ON T.id=ET.entry_ID WHERE T.id=$entry_id");

	return $tags;
}

function showTags($tags) {
	print '<ul class="tags">';
	foreach ($tags as $id => $tag) {
		print '<li>'.$tag.'</li>';
	}
	print '</ul>';
}