<!DOCTYPE html>
<html><head>
<title><?php echo $config['site_title'] ?></title>
<link href="<?php echo $config['site_url'] ?>css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $config['site_url'] ?>images/silk_theme.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $config['site_url'] ?>bower_components/bootstrap/dist/css/bootstrap-theme-paper.min.css" rel="stylesheet" type="text/css" />
<?php echo $css_includes ?>
</head>
<body>
<div id="loading">loading...</div>

<div id="header" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
<div id="nav" class="container">
	<div class="navbar-header">
	  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
	    <span class="sr-only">Toggle navigation</span>
	    <span class="icon-bar"></span>
	    <span class="icon-bar"></span>
	    <span class="icon-bar"></span>
	  </button>
	  <a class="navbar-brand" href="<?php echo $config['site_url']; ?>"><?php echo $config['site_title'] ?></a>
	</div>
	<div class="collapse navbar-collapse">
		<ul class="nav navbar-nav pull-right">
			<li><form action="search.php" method="post" id="search-area" class="input-group input-group-sm">
<input type="text" name="search" id="search" placeholder="Search..." value="<?php echo i($QUERY, 'search') ?>" class="form-control" />
<span class="input-group-btn"><button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button></span>
</form></li>
		<li><a class="calendar with-icon" href="<?php echo $config['site_url']; ?>calendar.php"> Calendar</a></li>
		<li><a class="previous with-icon" href="<?php echo $config['site_url']; ?>index.php?date=<?php echo date('Y-m-d', strtotime('Yesterday')); ?>"> Yesterday</a></li>
		<li><a class="add with-icon" href="<?php echo $config['site_url']; ?>index.php?date=<?php echo date('Y-m-d'); ?>"> Today</a></li>
		</ul>
	</div>

</div>
</div>

<div id="content" class="container">
<div id="error-message" <?php echo ($QUERY['error']) ? '':'style="display:none;"';?>><?php
	if(i($PARAM, 'error')) print strip_tags($PARAM['error']); //It comes from the URL
	else print $QUERY['error']; //Its set in the code(validation error or something.
?></div>
<div id="success-message" <?php echo ($QUERY['success']) ? '':'style="display:none;"';?>><?php echo strip_tags(stripslashes($QUERY['success']))?></div>

<!-- Begin Content -->
<?php 
/////////////////////////////////// The Template file will appear here ////////////////////////////

include($GLOBALS['template']->template); 

/////////////////////////////////// The Template file will appear here ////////////////////////////
?>
<!-- End Content -->
</div>

<script src="<?php echo $config['site_url'] ?>bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo $config['site_url'] ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo $config['site_url'] ?>js/application.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $config['home_url'] ?>js/library/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="<?php echo $config['home_url'] ?>js/library/tinymce/jquery.tinymce.min.js"></script>

<?php echo $js_includes ?>

</body>
</html>
