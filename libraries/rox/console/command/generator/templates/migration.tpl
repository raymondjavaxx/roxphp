<?php echo "<?php\n" ?>
/**
 * <?php echo $class; ?> migration
 *
 * @package app.migrations
 * @copyright (C) <?php echo date('Y') . "\n" ?>
 */
class <?php echo $class; ?> extends \rox\active_record\Migration {

	public function up() {
<?php if ($type == 'create_table'): ?>
		$t = $this->createTable('<?php echo $table; ?>');
<?php foreach ($columns as $column): ?>
		$t-><?php echo $column['type']; ?>('<?php echo $column['name']; ?>');
<?php endforeach ?>
		$t->timestamps();
		$t->finish();
<?php elseif($type == 'add_columns'): ?>
<?php foreach ($columns as $column): ?>
		$this->addColumn('<?php echo $table; ?>', '<?php echo $column['name']; ?>', '<?php echo $column['type']; ?>');
<?php endforeach ?>
<?php else: ?>
		// Migration code
<?php endif; ?>
<?php if ($type != 'other' && !empty($indexes)): ?>

		// Indexes
<?php foreach($indexes as $column): ?>
		$this->addIndex('<?php echo $table; ?>', '<?php echo $column; ?>');
<?php endforeach; ?>
<?php endif; ?>
	}
}
