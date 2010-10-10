<?php

require_once dirname(dirname(dirname(__FILE__))) . '/helper.php';

class Rox_Mailer_MessageTest extends PHPUnit_Framework_TestCase {

	public function testSerialize() {
		$message = new Rox_Mailer_Message(array(
			'from' => 'ramon@example.org',
			'to' => 'john@example.org',
			'subject' => 'Testing'
		));

		$message->addQuotedPrintablePart('text/plain; charset="utf-8"', 'Cinéma');

		$pixel = <<<EOD
iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAMAAAAoyzS7AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJ
bWFnZVJlYWR5ccllPAAAAAZQTFRFAP8AAAAAbwN+QwAAAAxJREFUeNpiYAAIMAAAAgABT21Z4QAA
AABJRU5ErkJggg==
EOD;

		$message->addAttachment('pixel.png', base64_decode($pixel));

		$expected = <<<EOD
MIME-Version: 1.0
From: ramon@example.org
To: john@example.org
Subject: Testing
X-Mailer: RoxPHP Mailer
Content-Type: multipart/mixed; boundary="boundary123"

--boundary123
Content-Type: text/plain; charset="utf-8"
Content-Transfer-Encoding: quoted-printable

Cin=E9ma
--boundary123
Content-Type: application/octet-stream
Content-Disposition: attachment; filename="pixel.png"
Content-Transfer-Encoding: base64

iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAMAAAAoyzS7AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJ
bWFnZVJlYWR5ccllPAAAAAZQTFRFAP8AAAAAbwN+QwAAAAxJREFUeNpiYAAIMAAAAgABT21Z4QAA
AABJRU5ErkJggg==
--boundary123
EOD;

		$serialized = $message->serialize(array('boundary' => 'boundary123'));
		$this->assertEquals($serialized, $expected);
	}
}
