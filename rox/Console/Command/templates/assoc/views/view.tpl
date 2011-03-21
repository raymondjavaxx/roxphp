!<h1><?php echo htmlspecialchars($model->getFriendlyModelName()) ?></h1>

<?php
//TODO: fix some of the formatting

foreach ($model->getAttributes() as $name => $options) {
    $type = $options['type'];
    echo "<div class = \"attribute\" id=\"{$name}_attribute\">\n";
    echo "\t<div class=\"title\">" . Rox_Inflector::humanize($name) . "</div>\n";
    echo "\t\t<div class=\"value\">";

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
            if (strstr($name, 'image_file'))
                echo "\t<?php if (\${$model->getModelVarName()}->{$name}) echo \$this->image->resize(\${$model->getModelVarName()}->{$name}, 200, 200) ?>\n";
            elseif (strstr($name, '_file'))
                echo "\t<?php if(\${$model->getModelVarName()}->{$name}) echo \$html->link(str_replace('/files/{$model->getPluralModelVarName()}/', '', \${$model->getModelVarName()}->{$name}), \${$model->getModelVarName()}->{$name}) ?>\n";
            else
                echo "\t<?php echo htmlspecialchars(\${$model->getModelVarName()}->{$name}) ?>\n";
            break;
    }

    echo "\t</div>\n</div>\n\n";
}
if ($includeAssociations) {
    foreach ($includedAssociations as $assocType => $association) {
        foreach ($association as $assocModel => $options) {
            $modelObj = $associatedModels[$assocModel];
            echo "<div class ='association {$assocType}' id='{$assocModel}'>\n";
            echo "\t<h3>{$modelObj->getFriendlyModelName()}</h3>\n";

            if ($assocType == 'hasAndBelongsToMany') {
                echo "<?php \${$model->getModelVarName()}_{$modelObj->getModelVarName()} = '';
                     foreach(\${$model->getModelVarName()}->{$assocModel} as \$item){
                            \${$model->getModelVarName()}_{$assocModel} .= \${$modelObj->getModelVarName()}[\$item->{$modelObj->getModelVarName()}_id] .', ';
                    }
                            echo substr(\${$model->getModelVarName()}_{$assocModel}, 0, strlen(\${$model->getModelVarName()}_{$modelObj->getModelVarName()}) -2);
                    ?>
            </p>";
                continue;
            } else if ($assocType == 'hasMany') {
                echo "\t<?php foreach(\${$model->getModelVarName()}->{$assocModel} as \${$modelObj->getModelVarName()}):?>\n";
                $parentModel = $model;
                $model = $modelObj;
            } else {
                echo "<?php \${$assocModel} = \${$model->getModelVarName()}->{$assocModel}();?>\n";
                $parentModel = $model;
                $model = $modelObj;
            }

            //continue;


            foreach ($modelObj->getAttributes() as $name => $options) {
                if (in_array($name, array('id', 'created_at', 'updated_at')) || strstr($name, 'id'))
                    continue;

                $type = $options['type'];

                echo "<div class = \"attribute\" id = \"{$assocModel}-{$name}_attribute\">\n";
                echo "\t<div class =\"title\">" . Rox_Inflector::humanize($name) . "</div>\n";
                echo "\t<div class=\"value\">";
                switch ($type) {
                    case 'boolean':
                        echo "\t\t<?php echo \${$model->getModelVarName()}->{$name} ? 'Yes' : 'No' ?>\n";
                        break;

                    case 'integer':
                    case 'datetime':
                    case 'date':
                    case 'float':
                        echo "\t\t<?php echo \${$model->getModelVarName()}->{$name} ?>\n";
                        break;

                    default:
                        if (strstr($name, 'image') && strstr($name, '_file'))
                            echo "\t\t<?php if(\${$modelObj->getModelVarName()}->{$name}) echo \$this->image->resize(\${$modelObj->getModelVarName()}->{$name}, 200, 200) ?>\n";
                        elseif (strstr($name, '_file'))
                            echo "\t\t<?php if(\${$modelObj->getModelVarName()}->{$name}) echo \$html->link(str_replace('/files/{$modelObj->getPluralModelVarName()}/', '', \${$model->getModelVarName()}->{$name}), \${$model->getModelVarName()}->{$name}) ?>\n";
                        else
                            echo "\t\t<?php echo htmlspecialchars(\${$modelObj->getModelVarName()}->{$name}) ?>\n";
                        break;
                }
                echo "\t</div>\n</div>\n";
            }

            if ($assocType == 'hasMany') {
                $model = $parentModel;
                echo "\t<?php endforeach;?>\n";
            }

            echo "</div>\n\n";
        }
    }
}
?>