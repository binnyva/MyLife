<?php
require('../common.php');

$entry_id = $QUERY['entry_id'];
if($entry_id) $body = $QUERY['entry-body-'.$entry_id];
else $body = $QUERY['body'];
$date = $QUERY['date'];

if($entry_id) $t_entry->edit($entry_id, $body, $_SESSION['user_id'], $date);
else $t_entry->create($_SESSION['user_id'], $body, $date);

print '{"success": "Entry Edited"}';