<?php
require('../common.php');

$entry_id = $QUERY['entry_id'];
$body = '';
if($entry_id) $body = i($QUERY, 'entry-body-'.$entry_id);
if(!$body) $body = $QUERY['body'];

$date = i($QUERY, 'date', null);
$summary_timeframe = i($QUERY, 'summary_timeframe', null);
$title = i($QUERY, 'title');
$tags = preg_split('/\s*,\s*/', i($QUERY, 'tags'));



if($entry_id) $t_entry->edit($entry_id, $body, $_SESSION['user_id'], $date, $tags, $title, $summary_timeframe);
else $t_entry->create($_SESSION['user_id'], $body, $date, $tags, $title, $summary_timeframe);

print '{"success": "Entry Edited"}';