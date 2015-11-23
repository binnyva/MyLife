
<div id="all-entries">

<?php 
$entry_count = 0; 
foreach ($entries as $entry) { 
	if(!$entry) continue; 
	$entry_count++; ?>

<div class="entry" id="entry-<?php echo $entry['id'] ?>">
<h4><a href="index.php?entry_id=<?php echo $entry['id'] ?>">Entry for <?php echo date('d\<\s\u\p\>S\<\/\s\u\p\> M, Y', strtotime($entry['date'])); ?></a></h4>

<div class="meta">
<?php if(empty($search)) { ?><a href="#" class="edit-entry edit with-icon" data-entry-id="<?php echo $entry['id'] ?>">Edit Entry</a><?php } ?>
<?php showTags($t_entry->getTags($entry['id'])); ?>
</div>

<form action="ajax/save_entry.php" class="ajaxify" method="post">
<div class="body" id="entry-body-<?php echo $entry['id'] ?>">

<?php if(!empty($search)) {
	echo getSnippet($search, $entry['body']);
} else {
	echo para($entry['body']);
}  ?>

</div>
<input type="hidden" name="entry_id" value="<?php echo $entry['id'] ?>" />

<div id="entry-save-<?php echo $entry['id'] ?>" class="entry-save">
<label for="date">Date</label> &nbsp; <input type="text" name="date" value="<?php echo $entry['date'] ?>" /><br />

<input type="submit" name="action" value="Save" class="btn btn-primary" id="entry-save-<?php echo $entry['id'] ?>-button" />
</div>
</form>

</div>

<?php if(count($entries) == 1) { ?>
<div class="container">
<ul class="btn-group btn-group-justified center-block">
<li class="btn btn-default"><a class="previous previous-day with-icon" href="index.php?date=<?php echo date('Y-m-d', strtotime($entry['date']) - (60*60*24)); ?>">Previous Day(<?php echo date('dS M', strtotime($entry['date']) - (60*60*24)); ?>)</a></li>
<li class="btn btn-default"><a class="next next-day with-icon" href="index.php?date=<?php echo date('Y-m-d', strtotime($entry['date']) + (60*60*24)); ?>">Next Day(<?php echo date('dS M', strtotime($entry['date']) + (60*60*24)); ?>)</a></li></ul>
</div>
<?php }
}

if(!$entry_count) print "<div class='error with-icon'>No entry on that date. <a href='create.php?date=$QUERY[date]'>Create one</a>?</div>";
?>

</div>

<?php if($t_entry->pager and $t_entry->pager->total_pages > 1) { ?>
<nav>
<ul class="pagination center-block">
<?php
$t_entry->pager->link_template = '<li><a href="%%PAGE_LINK%%" class="%%CLASS%%">%%TEXT%%</a></li>' . "\n";
$t_entry->pager->text['current_page_indicator']['left'] = '<li class="active"><a href="#" class="sp-current">';
$t_entry->pager->text['current_page_indicator']['right'] = '</a></li>';
$t_entry->pager->text['previous'] = '<span class="glyphicon glyphicon-step-backward"></span>';
$t_entry->pager->text['next'] = '<span class="glyphicon glyphicon-forward"></span>';
$t_entry->pager->text['first'] = '<span class="glyphicon glyphicon-step-backward"></span>';
$t_entry->pager->text['last'] = '<span class="glyphicon glyphicon-step-forward"></span>';
$t_entry->pager->page_link = 'index.php';
if(!empty($search)) $t_entry->pager->page_link = 'search.php?search='.$search;
print $t_entry->pager->getLink("first") . $t_entry->pager->getLink("back");
$t_entry->pager->printPager(); 
print $t_entry->pager->getLink("next") . $t_entry->pager->getLink("last");
?>
</ul>
</nav><br /><br />
<?php } ?>
