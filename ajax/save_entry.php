<?php
require('../common.php');

$entry_id = $QUERY['entry_id'];
$body = '';
if($entry_id) $body = i($QUERY, 'entry-body-'.$entry_id);
if(!$body) $body = $QUERY['body'];

$date = $QUERY['date'];
$title = i($QUERY, 'title');
$tags = preg_split('/\s*,\s*/', i($QUERY, 'tags'));

if($entry_id) $t_entry->edit($entry_id, $body, $_SESSION['user_id'], $date, $tags, $title);
else $t_entry->create($_SESSION['user_id'], $body, $date, $tags, $title);

print '{"success": "Entry Edited"}';