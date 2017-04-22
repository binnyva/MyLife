<?php
require 'common.php';

$search = trim(i($QUERY,'search'));
if(!$search) showMessage("Please enter a search term", "index.php", "error");

$entries = $t_entry->search($search, 25);

render('index.php');
