<?php echo "<?php\n" ?>
/**
 * <?php echo $class; ?> migration
 *
 * @package app.migrations
 * @copyright (C) <?php echo date('Y') . "\n" ?>
 */
class <?php echo $class; ?> extends \rox\active_record\Migration {

	public function up() {
		$t = $this->createTable('<?php echo $table; ?>');
<?php foreach ($columns as $column): ?>
		$t-><?php echo $column['type']; ?>('<?php echo $column['name']; ?>');
<?php endforeach ?>
		$t->timestamps();
		$t->finish();
<?php if (!empty($indexes)): ?>

<?php foreach($indexes as $column): ?>
		$this->addIndex('<?php echo $table; ?>', '<?php echo $column; ?>');
<?php endforeach; ?>
<?php endif; ?>
	}
}
