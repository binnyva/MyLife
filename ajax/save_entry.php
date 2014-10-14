<?php
require('../common.php');

$entry_id = $QUERY['entry_id'];
$body = $QUERY['entry-body-'.$entry_id];

editEntry($entry_id, $body);

