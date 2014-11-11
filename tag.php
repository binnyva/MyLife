<?php
require 'common.php';

$tag = i($QUERY,'tag');
if(!$search) showMessage("Tag not specified.", "index.php", "error");

$entries = $t_entry->getByTag($tag);

render('index.php');
