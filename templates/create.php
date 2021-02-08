<script type="text/javascript">
var tags = <?php echo json_encode($all_tags); ?>;
</script>

<?php if($entry['summary_timeframe']) { ?>
	<h3>Summary for <?php echo summaryTimeframeText($entry['summary_timeframe']) ?></h3>
<?php } else { ?>
	<h3>Entry for <?php echo date('d\<\s\u\p\>S\<\/\s\u\p\> M, Y (l)', strtotime($entry['date'])); ?></h3>
<?php } ?>

<form action="ajax/save_entry.php" class="ajaxify" method="post">
<input type="text" name="title" id="title" value="<?php echo $entry['title'] ?>" placeholder="Title / Life Event" /><br />

<div class="body">
<textarea name="body" rows="10" cols="70" id="body" autofocus="autofocus">
<?php echo $entry['body']; ?>
</textarea>
</div>
<div class="entry-save">
<label for="tags">Tags</label> &nbsp; <input type="text" name="tags" id="tags" value="<?php echo implode(',', $entry['tags']) ?>" /><br />

<?php if($entry['summary_timeframe']) { ?>
	<label for="summary_timeframe">Timeframe</label>
	<input type="text" name="summary_timeframe" id="summary_timeframe" value="<?php echo $entry['summary_timeframe'] ?>" /><br />
<?php } else {  ?>
	<label for="date">Date</label>
	<input type="text" name="date" id="date" value="<?php echo $entry['date'] ?>" /><br />
<?php } ?> 
<input type="hidden" name="entry_id" value="<?php echo $entry['id'] ?>" />
<input type="submit" name="action" value="Save" class="btn btn-primary" id="entry-save-button" />
<div id="ajaxify-message"></div>
</div>
</form>
<br />
<!-- Should go in to the journaler plugin -->
<!-- <input type="button" class="btn btn-success" value="Guess What I did" id="guess" /> -->
<input type="button" class="btn btn-warning" value="Show What I did" id="show" />
<div id="what-i-did"></div>