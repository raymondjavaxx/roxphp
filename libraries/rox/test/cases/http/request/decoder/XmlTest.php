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
 * @package \rox\test
 * @license The MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace rox\test\cases\http\request\decoder;

use \rox\http\request\decoder\Xml;

/**
 * Test case for \rox\http\request\decoder\Xml
 *
 * @package \rox\test
 */
class XmlTest extends \PHPUnit_Framework_TestCase {

	public function testDecode() {
		$xml = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<country>
	<tax type="float">0.2</tax>
	<code>FR</code>
</country>
EOD;

		$decoder = new Xml;
		$result = $decoder->decode($xml);

		$expected = array('country' => array('tax'  => 0.2, 'code' => 'FR'));

		$this->assertEquals($expected, $result);
	}

	public function testDecodeWithComplexFieldNames() {
		$xml = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<comment>
	<body>I like comments.</body>
	<author>Your name</author>
	<blog-id type="integer">241253187</blog-id>
	<article-id type="integer">134645308</article-id>
	<email>email@example.com</email>
</comment>
EOD;

		$decoder = new Xml;
		$result = $decoder->decode($xml);

		$expected = array(
			'comment' => array(
				'body' => 'I like comments.',
				'author' => 'Your name',
				'blog_id' => 241253187,
				'article_id' => 134645308,
				'email' => 'email@example.com'
			)
		);

		$this->assertEquals($expected, $result);
	}
}
