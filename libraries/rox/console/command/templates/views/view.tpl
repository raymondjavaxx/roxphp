<h1><?php echo htmlspecialchars($friendlyModelName) ?></h1>

<?php
foreach ($attributes as $name => $type) {
	echo "<p>\n";
	echo "\t" . \rox\Inflector::humanize($name) . "<br/>\n";

	switch ($type) {
		case 'boolean':
			echo "\t<?php echo \${$modelVarName}->{$name} ? 'Yes' : 'No' ?>\n";
			break;

		case 'integer':
		case 'datetime':
		case 'date':
		case 'float':
			echo "\t<?php echo \${$modelVarName}->{$name} ?>\n";
			break;

		default:
			echo "\t<?php echo htmlspecialchars(\${$modelVarName}->{$name}) ?>\n";
			break;
	}

	echo "</p>\n\n";
}
?>