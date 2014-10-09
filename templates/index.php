
<div id="all-entries">

<?php foreach ($entries as $entry) { ?>
<div class="entry">
<?php echo $entry['body']; ?>
</div>

<div class="meta">
<a href="edit.php?entry_id=<?php echo $entry['id'] ?>">Edit</a>

</div>
<?php } ?>
</div>

