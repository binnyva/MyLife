<?php
require("./common.php");

if(isset($QUERY['entry_id'])) {
	$entries = array($t_entry->getEntry($QUERY['entry_id']));
} else {
	$entries = $t_entry->getLatest();
}

render();


function getTags($entry_id) {
	global $sql;

	$tags = $sql->getById("SELECT T.id,T.name FROM Tag T INNER JOIN EntryTag ET ON T.id=ET.tag_id WHERE ET.entry_id=$entry_id");

	return $tags;
}

function showTags($tags) {
	global $config;

	if($tags) {
		print ' | Tags: <ul class="tags">';
		foreach ($tags as $id => $tag) {
			print "<li><a href='$config[home_url]tag.php?tag=$tag'>$tag</a></li>";
		}
		print '</ul>';
	}
}