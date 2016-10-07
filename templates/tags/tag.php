<!--Load the AJAX API-->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load('visualization', '1.0', {'packages':['corechart', 'calendar']});
google.setOnLoadCallback(drawChart);

// Calender Chart
function drawChart() {
	var dataTable = new google.visualization.DataTable();
	dataTable.addColumn({ type: 'date', id: 'Date' });
	dataTable.addColumn({ type: 'number', id: 'Count' });
	dataTable.addRows([
		<?php
		$dates = array();
		foreach($freq as $f) { 
			$dates[] = "[ new Date('".$f['date']."'), 1]"; 
		}
		$dates[] = "[ new Date('".date('Y-m-d')."'), 0]";
		print implode(",\n", $dates);
		?>
	]);

	var chart = new google.visualization.Calendar(document.getElementById('chart_div'));

	var options = {
		title: "<?php echo $page_title ?>",
		height: 700,
	};

	chart.draw(dataTable, options);
}
</script>
<h2><?php echo $page_title ?></h2>

<h4>Longest Streak: <?php echo $longest_streak ?> days</h4>
<?php showFromTo($longest_streak, $longest_streak_to); ?>

<h5>Longest Gap: <?php echo $longest_gap ?> days</h5>
<?php showFromTo($longest_gap, $longest_gap_to); ?>

<div id="chart_div"></div>

<?php
function showFromTo($length, $last_date) {
	$from = strtotime("-" . ($length - 1) . " days", strtotime($last_date));
	$to = strtotime($last_date);
	print "<p>From <a href='../index.php?date=" . date('Y-m-d', $from) . "'>" . date("dS M, Y", $from) . '</a> ';
	print "to <a href='../index.php?date=" . date('Y-m-d', $to) . "'>" . date("dS M, Y", $to). '</a></p>';
}
