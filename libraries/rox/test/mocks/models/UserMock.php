<?php

namespace rox\test\mocks\models;

class UserMock extends \rox\ActiveRecord {

	protected static $_dataSourceName = 'rox-test';

	protected static $_table = 'users';

	protected static $_protectedAttributes = array('role');

	public function invokeMethod($method) {
		$params = array_slice(func_get_args(), 1);
		return call_user_func_array(array($this, $method), $params);
	}
}
