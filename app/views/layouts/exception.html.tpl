<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Error</title>
	<style type="text/css">
		img {
			border:0;
		}
	</style>
</head>
<body>
	<div id="wrapper">
		<div id="header">
			<p>
				<a href="<?php echo \rox\Router::url('/'); ?>">
				<img src="<?php echo \rox\Router::url('/img/rox_logo.png'); ?>" alt="RoxPHP" /></a>
			</p>
		</div>
		<div id="container">
			<?php echo $rox_layout_content; ?>
			<?php if (ROX_DEBUG) : ?>
				<div class="exception">
					<h3><?php echo get_class($exception) ?>: <?php echo $exception->getMessage(); ?></h3>

					<p>
						File: <?php echo $exception->getFile(); ?><br />
						Line: <?php echo $exception->getLine(); ?>
					</p>

					<h4>Stack Trace:</h4>
					<pre><?php echo $exception->getTraceAsString(); ?></pre>
				</div>
				<style type="text/css">
					.exception {
						color: #7e2217;
						padding: 8px;
						background-color: #fdfaa4;
						border: 2px solid #933;
					}

					.exception pre {
						color:#fff;
						margin:0;
						background:#933;
						padding:8px;
						overflow-x:auto;
					}

					.exception h3 {
						font-size:18px;
						margin:0;
						padding:0;
					}

					.exception h4 {
						font-size:14px;
						margin:0;
						padding:0;
					}
				</style>
			<?php endif; ?>
		</div>
	</div>
</body>
</html>
