<?php
/**
 * {friendly_model_name} model
 *
 * @package {package_name}
 * @copyright (C) {year}
 */
class {class_name} extends Rox_ActiveRecord {

	public static function model($class = __FILE__) {
		return parent::model($class);
	}

	protected function _validate() {
		// validation code
	}
}
