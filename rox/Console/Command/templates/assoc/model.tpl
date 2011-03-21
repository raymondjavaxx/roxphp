!<?php
echo "<?php


/**
 * {$model->getFriendlyModelName()} model
 *
 * @package {$package_name}
 * @copyright (C) {$year}
 */
class {$model->getClassName()} extends ApplicationModel {";
echo $model->getBehaviors(TRUE);
echo "
    protected \$_displayAttribute = '" . $model->getDisplayAttribute() . "';\n";

echo $model->getAssociations(array('all'), TRUE);

echo "
    public static function model(\$class = __CLASS__) {
            return parent::model(\$class);
    }

    protected function _validate() {
            // validation code
    }
} ";

