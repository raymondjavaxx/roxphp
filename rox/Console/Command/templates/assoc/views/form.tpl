<?php

//Checks if we have a field with name *_file in the model, or an included associated model, if so, we make the form accept file uploads

if (preg_match("/.*_file/i", join(",", array_keys($model->getAttributes()))))
    echo "<?php echo \$form->forModel(\${$model->getModelVarName()}, array('enctype'=>'multipart/form-data')); ?>\n";
else if ($includeAssociations) {
    $file = false;
    foreach ($includedAssociations as $assocType => $association) {
        foreach ($association as $assocModel => $options) {
            $modelObj = $associatedModels[$assocModel];
            if (preg_match("/.*_file/i", join(",", array_keys($modelObj->getAttributes())))) {
                $file = true;
                break;
            }
        }
    }
    if ($file)
        echo "<?php echo \$form->forModel(\${$model->getModelVarName()}, array('enctype'=>'multipart/form-data')); ?>\n";
    else
        echo "<?php echo \$form->forModel(\${$model->getModelVarName()}); ?>\n";
}
else
    echo "<?php echo \$form->forModel(\${$model->getModelVarName()}); ?>\n";

echo "\t<?php\n";
foreach ($model->getAttributes() as $attribute => $options) {
    if (in_array($attribute, array('id', 'created_at', 'updated_at'))) {
        continue;
    }
    $type = $options['type'];
    $required = $options['required'];

    if ($attribute == 'password')
        $type = 'password';
    elseif ($attribute == 'date')
        $type = 'date';
    elseif (in_array($attribute, array('telephone', 'tel', 'phone', 'phone_number', 'phone_no', 'phoneNumber', 'phoneNo')))
        $type = 'tel';
    elseif (in_array($attribute, array('email', 'email_address', 'emailaddress', 'emailAddress', 'email1', 'email2')))
        $type = 'email';
    elseif (in_array($attribute, array('zip', 'number', 'cc', 'creditcard', 'card_no', 'cc_no', 'cc_num', 'card_num')))
        $type = 'number';
    elseif (strstr($attribute, 'image_file'))
        $type = 'image';
    elseif (strstr($attribute, '_file'))
        $type = 'file';

    foreach ($model->getAssociations(array('belongsTo')) as $assocType => $association) {
        foreach ($association as $assocModel => $options) {
            if ($attribute == $assocModel . '_id' || $attribute == Rox_Inflector::singularize($assocModel) . '_id') {
                $type = 'select';
                $options = Rox_Inflector::pluralize($assocModel);
                $label = ucwords(Rox_Inflector::humanize(str_replace('_id', '', $attribute)));
            }
        }
    }

    switch ($type) {
        case 'password':
            echo "\t\techo \$form->input('{$attribute}', array('type'=>'password'";
            if ($required)
                echo ", 'attributes'=>array('class'=>'required')"; echo "));\n";
            break;
        case 'boolean':
            echo "\t\techo \$form->input('{$attribute}', array('type' => 'checkbox'";
            if ($required)
                echo ", 'attributes'=>array('class'=>'required')"; echo "));\n";
            break;
        case 'date':
            echo "\t\techo \$form->input('{$attribute}', array('type'=>'date', 'attributes'=>array('class'=>'date";
            if ($required)
                echo " required"; echo "')));\n";
            break;
        case 'select':
            echo "\t\techo \$form->input('{$attribute}', array('type'=>'select', 'label'=>'{$label}', 'options'=>\${$options}";
            if ($required)
                echo ", 'attributes'=>array('class'=>'required')"; echo "));\n";
            break;
        case 'email':
            echo "\t\techo \$form->input('{$attribute}', array('type'=>'email', 'attributes'=>array('class'=>'email";
            if ($required)
                echo " required"; echo "')));\n";
            break;
        case 'telephone':
            echo "\t\techo \$form->input('{$attribute}', array('type'=>'tel'";
            if ($required)
                echo ", 'attributes'=>array('class'=>'required')"; echo "));\n";
            break;
        case 'number':
            echo "\t\techo \$form->input('{$attribute}', array('type'=>'number'";
            if ($required)
                echo ", 'attributes'=>array('class'=>'required')"; echo "));\n";
            break;
        case 'textarea':
            echo "\t\techo \$form->input('{$attribute}', array('type'=>'textarea'";
            if ($required)
                echo ", 'attributes'=>array('class'=>'required')"; echo "));\n";
            break;
        case 'image':
            echo "\t\techo \$form->input('{$attribute}', array('type'=>'file', 'name'=>'{$attribute}'";
            if ($required)
                echo ", 'attributes'=>array('class'=>'required')"; echo "));\n";
            if ($template == 'edit')
                echo "\t\tif(\${$model->getModelVarName()}->{$name}) echo\"<div class ='current'>Current {$attribute}: \".\$this->image->resize(\${$model->getModelVarName()}->{$attribute}, 150, 150) .\"</div>\";\n";
            break;
        case 'file':
            echo "\t\techo \$form->input('{$attribute}', array('type'=>'file', 'name'=>'{$attribute}'";
            if ($required)
                echo ", 'attributes'=>array('class'=>'required')"; echo "));\n";
            if ($template == 'edit')
                echo "\t\techo \"<div class = 'current'>Current {$attribute}: \".\$html->link(str_replace('/files/{$model->getPluralModelVarName()}/', '', \${$model->getModelVarName()}->{$attribute}), \${$model->getModelVarName()}->{$attribute}, array('target'=>'_blank')).\"</div>\";\n";
            break;
        default:
            echo "\t\techo \$form->input('{$attribute}'";
            if ($required)
                echo ", array('attributes'=>array('class'=>'required'))"; echo ");\n";
            break;
    }
}

echo "\t?>\n";

$i = 0;

foreach ($includedAssociations as $assocType => $association) {
    foreach ($association as $assocModel => $options) {
        $modelObj = $associatedModels[$assocModel];

        echo "
    <div class = \"association {$assocType}\" id =\"{$modelObj->getModelVarName()}\">
        <h3>{$modelObj->getClassName()}</h3>
        <?php
            \$f{$i} = \$form->fieldsFor('{$modelObj->getModelVarName()}');\n";
        if ($assocType == 'hasAndBelongsToMany') {
            echo "\t\t\techo \$f{$i}->input('{$modelObj->getModelVarName()}', array('type'=>'select', 'multiple'=>'multiple', 'options'=>\${$modelObj->getPluralModelVarName()}));";
            echo "\n\t\t\$f{$i}->end();\n ?>\n";
            $i++;
            continue;
        } else if ($assocType == 'hasMany' && $template == 'edit') {
            echo "\t\tforeach(\${$model->getModelVarName()}->{$assocModel} as \${$modelObj->getModelVarName()}):\n";
        } else if ($assocType == 'hasOne' && $template == 'edit') {
            echo "\t\t\${$assocModel} = \${$model->getModelVarName()}->{$assocModel}();\n";
        }


        foreach ($modelObj->getAttributes() as $attribute => $options) {
            if (in_array($attribute, array('id', 'created_at', 'updated_at')) || $attribute == $model->getModelVarName() . '_id' || $attribute == $model->getPluralModelVarName() . '_id') {
                if ($attribute == 'id' && $template == 'edit')
                    echo "\t\techo \$f{$i}->input('{$attribute}', array('type'=>'hidden', 'value'=>\${$modelObj->getModelVarName()}->id));\n";
                continue;
            }

            $type = $options['type'];
            $required = $options['required'];

            //TODO: Find a way to not duplicate this.

            if ($attribute == 'password')
                $type = 'password';
            elseif ($attribute == 'date')
                $type = 'date';
            elseif (in_array($attribute, array('telephone', 'tel', 'phone', 'phone_number', 'phone_no', 'phoneNumber', 'phoneNo')))
                $type = 'tel';
            elseif (in_array($attribute, array('email', 'email_address', 'emailaddress', 'emailAddress', 'email1', 'email2')))
                $type = 'email';
            elseif (in_array($attribute, array('zip', 'number', 'cc', 'creditcard', 'card_no', 'cc_no', 'cc_num', 'card_num')))
                $type = 'number';
            elseif (strstr($attribute, 'image_file'))
                $type = 'image';
            elseif (strstr($attribute, '_file'))
                $type = 'file';

            $attributes = array();

            if ($required)
                $attributes['class'] = 'required';
            if ($assocType == 'hasMany') {
                if ($type == 'file' || $type == 'image')
                    $attributes['name'] = $modelObj->getModelVarName() . "[$attribute][$i]";
                else
                    $attributes['name'] = $modelObj->getModelVarName() . "[$i][$attribute]";
            }

            echo "\t\t";



            switch ($type) {
                case 'password':
                    echo "\t\techo \$f{$i}->input('{$attribute}', array('type'=>'password', array('attributes' => array(";
                    foreach ($attributes as $a_key => $a) {
                        echo "'{$a_key}'=>'{$a}'";
                    }
                    echo ")));\n";
                    break;
                case 'boolean':
                    echo "\t\techo \$f{$i}->input('{$attribute}', array('type' => 'checkbox', array('attributes' => array(";
                    foreach ($attributes as $a_key => $a) {
                        echo "'{$a_key}'=>'{$a}'";
                    }
                    echo ")));\n";
                    break;
                case 'date':
                    $attributes['class'] .= ' date';
                    echo "\t\techo \$f{$i}->input('{$attribute}', array('type'=>'date', array('attributes' => array(";
                    foreach ($attributes as $a_key => $a) {
                        echo "'{$a_key}'=>'{$a}'";
                    }
                    echo ")));\n";
                    break;
                case 'select':
                    echo "\t\techo \$f{$i}->input('{$attribute}', array('type'=>'select', 'label'=>'{$label}', 'options'=>\${$options}, 'attributes' => array(";
                    foreach ($attributes as $a_key => $a) {
                        echo "'{$a_key}'=>'{$a}'";
                    }
                    echo ")));\n";
                    break;
                case 'email':
                    echo "\t\techo \$f{$i}->input('{$attribute}', array('type'=>'email', 'attributes' => array(";
                    foreach ($attributes as $a_key => $a) {
                        echo "'{$a_key}'=>'{$a}'";
                    }
                    echo ")));\n";
                    break;
                case 'telephone':
                    echo "\t\techo \$f{$i}->input('{$attribute}', array('type'=>'tel', 'attributes' => array(";
                    foreach ($attributes as $a_key => $a) {
                        echo "'{$a_key}'=>'{$a}'";
                    }
                    echo ")));\n";
                    break;
                case 'number':
                    echo "\t\techo \$f{$i}->input('{$attribute}', array('type'=>'number', 'attributes' => array(";
                    foreach ($attributes as $a_key => $a) {
                        echo "'{$a_key}'=>'{$a}'";
                    }
                    echo ")));\n";
                    break;
                case 'textarea':
                    echo "\t\techo \$f{$i}->input('{$attribute}', array('type'=>'textarea', array('attributes' => array(";
                    foreach ($attributes as $a_key => $a) {
                        echo "'{$a_key}'=>'{$a}', ";
                    }
                    echo "))));\n";
                    break;
                case 'image':
                    echo "\t\techo \$f{$i}->input('{$attribute}', array('type'=>'file', 'name'=>'{$attribute}', 'attributes' => array(";
                    foreach ($attributes as $a_key => $a) {
                        echo "'{$a_key}'=>'{$a}', ";
                    }
                    echo ")));\n";
                    if ($template == 'edit')
                        echo "\t\tif(\${$modelObj->getModelVarName()}->{$attribute}) echo \"<div class ='current'>Current {$attribute}: \".\$this->image->resize(\${$modelObj->getModelVarName()}->{$attribute}, 150, 150) .\"</div>\";\n";
                    break;
                case 'file':
                    echo "\t\techo \$f{$i}->input('{$attribute}', array('type'=>'file', 'name'=>'{$attribute}', 'attributes' => array(";
                    foreach ($attributes as $a_key => $a) {
                        echo "'{$a_key}'=>'{$a}', ";
                    }
                    echo ")));\n";
                    if ($template == 'edit')
                        echo "\t\tif(\${$modelObj->getModelVarName()}->{$attribute}) echo \"<div class = 'current'>Current {$attribute}: \".\$html->link(str_replace('/files/{$modelObj->getPluralModelVarName()}/', '', \${$modelObj->getModelVarName()}->{$attribute}), \${$modelObj->getModelVarName()}->{$attribute}, array('target'=>'_blank')).\"</div>\";\n";
                    break;
                default:
                    echo "\t\techo \$f{$i}->input('{$attribute}', array('attributes' => array(";
                    foreach ($attributes as $a_key => $a) {
                        echo "'{$a_key}'=>'{$a}', ";
                    }
                    echo ")));\n";
                    break;
            }
        }
        if ($assocType == 'hasMany' && $template == 'edit')
            echo "\t\tendforeach;\n";
        echo "            \$f{$i} = \$form->end();\n";
        $i++;
        echo "
        ?>
    </div>
                ";
    }
}



echo "\t<?php echo \$form->submit(); ?>\n";

echo "<?php echo \$form->end(); ?>\n";
