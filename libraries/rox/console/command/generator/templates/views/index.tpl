<h1><?php echo htmlspecialchars(\rox\Inflector::pluralize($friendlyModelName)) ?></h1>

<?php

echo "<table>\n";
echo "\t<tr>\n";

foreach ($attributes as $name => $type) {
	echo "\t\t<th>" . \rox\Inflector::humanize($name) . "</th>\n";
}

echo "\t\t<th>Actions</th>\n";

echo "\t</tr>\n";
echo "\t<?php foreach (\${$pluralModelVarName} as \$i => \${$modelVarName}): ?>\n";
echo "\t\t<tr>\n";

foreach ($attributes as $name => $type) {
	switch ($type) {
		case 'boolean':
			echo "\t\t\t<td><?php echo \${$modelVarName}->{$name} ? 'Yes' : 'No' ?></td>\n";
			break;

		case 'integer':
		case 'datetime':
		case 'date':
		case 'float':
			echo "\t\t\t<td><?php echo \${$modelVarName}->{$name} ?></td>\n";
			break;

		default:
			echo "\t\t\t<td><?php echo htmlspecialchars(\${$modelVarName}->{$name}) ?></td>\n";
			break;
	}	
}

echo "\t\t\t<td>\n";
echo "\t\t\t\t<?php echo \$html->deleteLink('Delete', \${$modelVarName}) ?> |\n";
echo "\t\t\t\t<?php echo \$html->editLink('Edit', \${$modelVarName}) ?> |\n";
echo "\t\t\t\t<?php echo \$html->viewLink('View', \${$modelVarName}) ?>\n";
echo "\t\t\t</td>\n";

echo "\t\t</tr>\n";
echo "\t<?php endforeach; ?>\n";
echo "</table>\n\n";

echo "<p><?php echo \$html->link('New {$friendlyModelName}', '/{$controller}/new') ?></p>\n\n";

echo "<?php echo \$pagination->links(\${$pluralModelVarName}) ?>\n";
?>