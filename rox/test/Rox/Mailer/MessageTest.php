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

		$htmlParagraphs = <<<EOD
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc dapi bus aliquet porttitor.</p>
<p>t vehicula varius velit, eu sagittis nibh auctor vitae.</p>
EOD;

		$message->addQuotedPrintablePart('text/html; charset="utf-8"', $htmlParagraphs);

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
Subject: =?UTF-8?Q?Testing?=
X-Mailer: RoxPHP Mailer
Content-Type: multipart/alternative; boundary="boundary123"

This is a multi-part message in MIME format

--boundary123
Content-Type: text/plain; charset="utf-8"
Content-Transfer-Encoding: quoted-printable

Cin=C3=A9ma
--boundary123
Content-Type: text/html; charset="utf-8"
Content-Transfer-Encoding: quoted-printable

<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc dapi=
bus aliquet porttitor.</p>
<p>t vehicula varius velit, eu sagittis nibh auctor vitae.</p>
--boundary123
Content-Type: application/octet-stream
Content-Disposition: attachment; filename="pixel.png"
Content-Transfer-Encoding: base64

iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAMAAAAoyzS7AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJ
bWFnZVJlYWR5ccllPAAAAAZQTFRFAP8AAAAAbwN+QwAAAAxJREFUeNpiYAAIMAAAAgABT21Z4QAA
AABJRU5ErkJggg==
--boundary123--
EOD;

		$serialized = $message->serialize(array('boundary' => 'boundary123'));
		$this->assertEquals($serialized, $expected);
	}
}
