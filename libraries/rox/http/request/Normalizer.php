<?php
/**
 * RoxPHP
 *
 * Copyright (C) 2008 - 2011 Ramon Torres
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) 2008 - 2011 Ramon Torres
 * @package Rox
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace rox\http\request;

use \rox\Exception;

/**
 * Request normalizer
 *
 * @package Rox
 */
class Normalizer {

	protected static $_config = array(
		'body_decoders' => array(
			'application/xml' => '\rox\http\request\decoder\Xml',
			'application/json' => '\rox\http\request\decoder\Json'
		)
	);

	public static function config($config) {
		self::$_config = array_merge(self::$_config, $config);
	}

	public static function normalize($request) {
		// override request method
		$method = $request->getPost('_method');
		if ($method !== null && in_array($method, array('PUT', 'DELETE'))) {
			$_SERVER['X_ROX_OVERRIDEN_METHOD'] = $_SERVER['REQUEST_METHOD'];
			$_SERVER['REQUEST_METHOD'] = $method;
		}

		$contentType = $request->getServer('CONTENT_TYPE');
		if (!$contentType) {
			return false;
		}

		// remove parameters
		list($contentType) = explode(';', $contentType);

		$request->data = array_merge($_GET, $_POST);

		// list of content types that PHP knows how to parse
		$knownTypes = array('application/x-www-form-urlencoded', 'multipart/form-data');
		if (!in_array($contentType, $knownTypes)) {
			if (array_key_exists($contentType, self::$_config['body_decoders'])) {
				$body = file_get_contents('php://input');
				if (!empty($body)) {
					$class = self::$_config['body_decoders'][$contentType];
					$decoder = new $class;
					$request->data = array_merge($request->data, $decoder->decode($body));
				}
			} else {
				throw new Exception("Dispatcher doesn't know how to parse {$contentType}");
			}
		}
		
		unset($request->data['route']);
	}
}
