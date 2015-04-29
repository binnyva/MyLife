<?php
require('../common.php');

$entry_id = $QUERY['entry_id'];
$body = $QUERY['entry-body-'.$entry_id];
$date = $QUERY['date'];

$t_entry->edit($entry_id, $body, $_SESSION['user_id'], $date);

print '{"success": "Entry Edited"}';