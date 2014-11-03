
<div id="all-entries">

<?php foreach ($entries as $entry) { ?>

<div class="entry" id="entry-<?php echo $entry['id'] ?>">
<h4>Entry for <?php echo date('d\<\s\u\p\>S\<\/\s\u\p\> M, Y', strtotime($entry['date'])); ?></h4>

<div class="meta">
<a href="#" class="edit-entry edit with-icon" data-entry-id="<?php echo $entry['id'] ?>">Edit Entry</a>
<?php showTags(getTags($entry['id'])); ?>
</div>

<form action="ajax/save_entry.php" class="ajaxify" method="post">
<div class="body" id="entry-body-<?php echo $entry['id'] ?>">

<?php echo para($entry['body']); ?>

</div>
<input type="hidden" name="entry_id" value="<?php echo $entry['id'] ?>" />
<input type="submit" name="action" value="Save" class="btn btn-primary entry-save" id="entry-save-<?php echo $entry['id'] ?>" />
</form>

</div>

<?php if(count($entries) == 1) { ?>
<div class="container">
<ul class="btn-group btn-group-justified center-block">
<li class="btn btn-default"><a class="previous previous-day with-icon" href="index.php?date=<?php echo date('Y-m-d', strtotime($entry['date']) - (60*60*24)); ?>">Previous Day(<?php echo date('dS M', strtotime($entry['date']) - (60*60*24)); ?>)</a></li>
<li class="btn btn-default"><a class="next next-day with-icon" href="index.php?date=<?php echo date('Y-m-d', strtotime($entry['date']) + (60*60*24)); ?>">Next Day(<?php echo date('dS M', strtotime($entry['date']) + (60*60*24)); ?>)</a></li></ul>
</div>
<?php } ?>
<?php } ?>
</div>
