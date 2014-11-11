<?php
require 'common.php';

$search = trim(i($QUERY,'search'));
if(!$search) showMessage("Please enter a search term", "index.php", "error");

$entries = $t_entry->search($search, 25);

render('index.php');

// Taken from http://snippetsofcode.wordpress.com/2011/08/15/getting-snippets-as-search-results-in-php/ with minimal changes.
function getSnippet($keyword, $txt) {
	$txt = strip_tags($txt);
	$snippet='';
	$span = 50;
	preg_match_all("#(\W.{0,$span}\W)($keyword)(\W.{0,$span}\W)#i", "  $txt  ", $matches);
	foreach($matches[0] as $match) {
		if (!$match = trim($match)) continue;
		if (isset($snippet)) $snippet .= "$match..."; else $snippet = "...$match...";
	}
	$snippet = preg_replace("#($keyword)#i", '<mark>$1</mark>', $snippet);
	return $snippet;
}