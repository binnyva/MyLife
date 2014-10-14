
<div id="all-entries">

<?php foreach ($entries as $entry) { ?>
<div class="entry" id="entry-<?php echo $entry['id'] ?>">
<h4>Entry for <?php echo date('d\<\s\u\p\>S\<\/\s\u\p\> M, Y', strtotime($entry['date'])); ?></h4>

<div class="meta">
<a href="#" class="edit-entry edit with-icon" data-entry-id="<?php echo $entry['id'] ?>">Edit Entry</a> |
<?php showTags(getTags($entry['id'])); ?>
</div>

<form action="ajax/save_entry.php" class="ajaxify" method="post">
<div class="body" id="entry-body-<?php echo $entry['id'] ?>">
<p>Blah, Blah, Blah</p>

<p>More Blah, Blah, Blah</p>

<?php //echo para($entry['body']); ?>
</div>
<input type="hidden" name="entry_id" value="<?php echo $entry['id'] ?>" />
<input type="submit" name="action" value="Save" class="btn btn-primary entry-save" id="entry-save-<?php echo $entry['id'] ?>" />
</form>

</div>
<?php } ?>
</div>
