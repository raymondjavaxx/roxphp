<?php

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/helper.php';

class Rox_Http_Decoder_XmlTest extends PHPUnit_Framework_TestCase {

	public function testDecode() {
		$xml = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<country>
	<tax type="float">0.2</tax>
	<code>FR</code>
</country>
EOD;

		$decoder = new Rox_Http_Decoder_Xml;
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

		$decoder = new Rox_Http_Decoder_Xml;
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
