<?php
require("./common.php");

if(isset($QUERY['entry_id'])) { // Individual entry
	$entries = array($t_entry->getEntry($QUERY['entry_id']));

} elseif(isset($QUERY['date'])) { // Entry on given date
	$entries = $t_entry->find(array('date'=>$QUERY['date'], 'user_id'=> $_SESSION['user_id']));

} else { // The latest 10 entries.
	$entries = $t_entry->getLatest();
}

render();
