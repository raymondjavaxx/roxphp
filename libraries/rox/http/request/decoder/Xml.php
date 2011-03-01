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

namespace rox\http\request\decoder;

use \rox\Exception;

/**
 * XML Decoder
 *
 * @package Rox
 */
class Xml {

	public function decode($data) {
		$dom = new \DOMDocument();
		$dom->preserveWhiteSpace = false;
		if (!$dom->loadXML($data)) {
			throw new Exception("Data is not valid XML");
		}

		$result = array();

		foreach ($dom->childNodes as $node) {
			$key = self::_keyNameFromNode($node);
			$result[$key] = self::_decodeNode($node);
		}

		return $result;
	}

	protected static function _decodeNode($node) {
		$result = array();

		foreach ($node->childNodes as $child) {
			$key = self::_keyNameFromNode($child);

			switch ($child->getAttribute('type')) {
				case 'float':
					$result[$key] = (float)$child->nodeValue;
					break;

				case 'integer':
					$result[$key] = (integer)$child->nodeValue;
					break;

				default:
					$result[$key] = (string)$child->nodeValue;
					break;
			}
		}

		return $result;
	}

	protected static function _keyNameFromNode($node) {
		return strtolower(str_replace('-', '_', $node->nodeName));
	}
}
