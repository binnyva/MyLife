<h3>Entry for <?php echo date('d\<\s\u\p\>S\<\/\s\u\p\> M, Y', strtotime($entry['date'])); ?></h3>

<form action="ajax/save_entry.php" class="ajaxify" method="post">
<div class="body">
<textarea name="body" rows="10" cols="70" id="body">
<?php echo $entry['body']; ?>
</textarea>
</div>
<div class="entry-save">
<label for="date">Date</label> &nbsp; <input type="text" name="date" id="date" value="<?php echo $entry['date'] ?>" /><br />
<input type="hidden" name="entry_id" value="<?php echo $entry['id'] ?>" />
<input type="submit" name="action" value="Save" class="btn btn-primary" id="entry-save-button" />
</div>
</form>
<br />
<input type="button" class="btn btn-warning" value="Guess What I did" id="guess" />