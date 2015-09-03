<h3>Missing Entries</h3>

<ul>
<?php foreach($missing_dates as $date) { ?>
<li><a href="../../create.php?date=<?php echo $date ?>"><?php echo date("dS M, Y", strtotime($date)); ?></a></li>
<?php } ?>
</ul>