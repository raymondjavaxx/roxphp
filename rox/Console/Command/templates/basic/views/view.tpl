!<h1><?php echo htmlspecialchars($model->getFriendlyModelName()) ?></h1>

<?php
foreach ($model->getAttributes() as $name => $type) {
	echo "<p>\n";
	echo "\t" . Rox_Inflector::humanize($name) . "<br/>\n";

	switch ($type) {
		case 'boolean':
			echo "\t<?php echo \${$model->getModelVarName()}->{$name} ? 'Yes' : 'No' ?>\n";
			break;

		case 'integer':
		case 'datetime':
		case 'date':
		case 'float':
			echo "\t<?php echo \${$model->getModelVarName()}->{$name} ?>\n";
			break;

		default:
			echo "\t<?php echo htmlspecialchars(\${$model->getModelVarName()}->{$name}) ?>\n";
			break;
	}

	echo "</p>\n\n";
}
?>