<?php
echo "<?php echo \$form->forModel(\${$modelVarName}); ?>\n";

echo "\t<?php\n";
foreach ($attributes as $attribute => $type) {
	if (in_array($attribute, array('id', 'created_at', 'updated_at'))) {
		continue;
	}

	switch ($type) {
		case 'boolean':
			echo "\t\techo \$form->input('{$attribute}', array('type' => 'checkbox'));\n";
			break;

		default:
			echo "\t\techo \$form->input('{$attribute}');\n";
			break;
	}
}
echo "\t?>\n";

echo "\t<?php echo \$form->submit(); ?>\n";

echo "<?php echo \$form->end(); ?>\n";
