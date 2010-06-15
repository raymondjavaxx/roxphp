<?php
header('Content-type: application/rss+xml');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
?>
<rss version="2.0">
	<?php echo $rox_layout_content; ?>
</rss>
