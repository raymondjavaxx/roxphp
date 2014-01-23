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
use \rox\http\Request;

/**
 * Request normalizer
 *
 * @package Rox
 */
class Normalizer {

	protected static $_config = array(
		'decoders' => array(
			'application/xml' => '\rox\http\request\decoder\Xml',
			'application/json' => '\rox\http\request\decoder\Json'
		)
	);

	public static function config($config) {
		self::$_config = array_merge(self::$_config, $config);
	}

	public static function normalize(Request $request) {
		list($contentType) = explode(';', $request->server->get('CONTENT_TYPE', ''));
		if (empty($contentType)) {
			return false;
		}

		if (isset(static::$_config['decoders'][$contentType])) {
			$raw = $request->rawBody();
			if (!empty($raw)) {
				$class = self::$_config['decoders'][$contentType];
				$decoder = new $class;
				$request->data = new ParamCollection($decoder->decode($raw));
			}
		}
	}
}
