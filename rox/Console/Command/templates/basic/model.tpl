<?php
/**
 * {$model->getFriendlyModelName()} model
 *
 * @package {package_name}
 * @copyright (C) {year}
 */
class {$model->getClassName()} extends Rox_ActiveRecord {

	public static function model($class = __CLASS__) {
		return parent::model($class);
	}

	protected function _validate() {
		// validation code
	}
}