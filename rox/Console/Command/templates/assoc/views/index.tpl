!<h1><?php echo htmlspecialchars(Rox_Inflector::pluralize($model->getFriendlyModelName())) ?></h1>


<?php echo
"<?php
    echo \$html->link('Add New " . ucwords($model->getModelVarName()) . "', '/{$model->getPluralModelVarName()}/add/');
?>"; ?>


<?php
echo "<table>\n";
echo "\t<tr>\n";

foreach ($model->getAttributes() as $name => $type) {
    echo "\t\t<th>" . Rox_Inflector::humanize($name) . "</th>\n";
}

echo "\t\t<th>Actions</th>\n";

echo "\t</tr>\n";
echo "\t<?php foreach (\${$model->getPluralModelVarName()} as \$i => \${$model->getModelVarName()}): ?>\n";
echo "\t\t<tr>\n";

foreach ($model->getAttributes() as $name => $type) {
    switch ($type) {
        case 'boolean':
            echo "\t\t\t<td><?php echo \${$model->getModelVarName()}->{$name} ? 'Yes' : 'No' ?></td>\n";
            break;

        case 'integer':
        case 'datetime':
        case 'date':
        case 'float':
            echo "\t\t\t<td><?php echo \${$model->getModelVarName()}->{$name} ?></td>\n";
            break;

        default:
            echo "\t\t\t<td><?php echo htmlspecialchars(\${$model->getModelVarName()}->{$name}) ?></td>\n";
            break;
    }
}

echo "\t\t\t<td>\n";
echo "\t\t\t\t<?php echo \$html->deleteLink('Delete', \${$model->getModelVarName()}) ?> |\n";
echo "\t\t\t\t<?php echo \$html->editLink('Edit', \${$model->getModelVarName()}) ?> |\n";
echo "\t\t\t\t<?php echo \$html->viewLink('View', \${$model->getModelVarName()}) ?>\n";
echo "\t\t\t</td>\n";

echo "\t\t</tr>\n";
echo "\t<?php endforeach; ?>\n";
echo "</table>\n\n";

echo "<p><?php echo \$pagination->links(\${$model->getPluralModelVarName()}) ?></p>\n";
?>
