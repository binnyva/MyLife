<h1>Analytics</h1>

<script type="text/javascript">
var tags = <?php echo json_encode($all_tags); ?>;
</script>

<form method="get" action="" class="form-area">
<?php 
$html->buildInput("from", "From Date", 'date', $from);
$html->buildInput("to", "To Date", 'date', $to);
$html->buildInput("search_term", "Search Term", 'text', $search_term);
$html->buildInput("tags", "Tag", 'text', $tags);
$html->buildInput("action", "&nbsp;", 'submit', 'Analyze');
?>
</form>

<div id="content">
<?php if($posts) { ?>
Total Instances: <strong><?php echo $total; ?></strong><br />
Timeframe: <strong><?php echo $gap ?> days</strong><br />
Weekly Average: <strong><?php echo round($total / $weeks, 2); ?></strong><br />
Monthly Average: <strong><?php echo round($total / $months, 2); ?></strong><br />

<h3>Posts...</h3>
<?php foreach ($posts as $post) { ?>
<div class="entry">
<span class="entry-date"><a href="../index.php?entry_id=<?php echo $post['id'] ?>"><?php echo date($config['date_format_php'], strtotime($post['date'])); ?></a></span>:
<span class="entry-body"><?php echo getSnippet($search_term, $post['body']); ?></span>
<span class="entry-tags"><?php showTags($t_entry->getTags($post['id'])); ?></span>
</div>
<?php } ?>
<br /><br />

<?php } ?>
</div>