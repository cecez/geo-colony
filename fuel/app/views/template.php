<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php echo $title; ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
	<?php echo Asset::css('main.css'); ?>
	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
</head>
<body>
<?php echo $content; ?>
	
<?php if (Session::get_flash('success')): ?>
				<div class="alert-message success">
					<p>
					<?php echo implode('</p><p>', e((array) Session::get_flash('success'))); ?>
					</p>
				</div>
<?php endif; ?>

<?php if (Session::get_flash('error')): ?>
				<div class="alert-message error">
					<p>
					<?php echo implode('</p><p>', e((array) Session::get_flash('error'))); ?>
					</p>
				</div>
<?php endif; ?>
	
	
<?php 
if ($title == 'Dashboard &raquo; Index') {
?>	
	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<?php 	
	echo Asset::js('main.js'); 
}
	
?>
</body>
</html>
