<?php
require('../common.php');

$entry_id = $QUERY['entry_id'];
$body = $QUERY['entry-body-'.$entry_id];

$t_entry->edit($entry_id, $body);

print '{"success": "Entry Edited"}';