<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Error</title>
	<?php echo $html->css('rox'); ?>
</head>
<body>
	<div id="header">
		<p><?php echo $html->image('rox_logo.png', 'RoxPHP'); ?></p>
	</div>
	<div id="container">
		<?php echo $rox_layout_content; ?>
		<?php if (ROX_DEBUG) : ?>
			<div class="exception">
				<?php echo $exception->getMessage(); ?><br />
				File: <?php echo $exception->getFile(); ?><br />
				Line: <?php echo $exception->getLine(); ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="footer">
		<p>Powered by <a href="http://roxphp.com">RoxPHP</a></p>
	</div>
</body>
</html>