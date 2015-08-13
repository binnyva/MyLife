<?php
require '../common.php';

 // Reassign tags to all the entries. 

$entries = $t_entry->find('user_id=1');
foreach($entries as $entry) {
	parseTags($entry['body'], $entry['id']);

}