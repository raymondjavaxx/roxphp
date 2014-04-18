<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title><?php echo htmlspecialchars($rox_page_title); ?></title>
	<?php echo $html->favicon(); ?>
	<?php echo $html->css('rox'); ?>
</head>
<body>
	<div id="wrapper">
		<div id="header">
			<p>
				<a href="<?php echo \rox\Router::url('/'); ?>">
				<?php echo $html->image('rox_logo.png'); ?></a>
			</p>
		</div>
		<div id="container">
			<?php foreach ($this->getFlashMessages() as $type => $message) : ?>
				<div class="flash-message <?php echo $type ?>-flash-message">
					<?php echo htmlspecialchars($message); ?>
				</div>
			<?php endforeach; ?>
			<?php echo $rox_layout_content; ?>
		</div>
		<div id="footer">
			<p>Powered by <a class="delete" href="http://roxphp.com">RoxPHP</a></p>
		</div>
	</div>
	<?php echo $html->javascript('common') ?>
</body>
</html>
