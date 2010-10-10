<?php

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/helper.php';

class Rox_Mailer_Adapter_SmtpTest extends PHPUnit_Framework_TestCase {

	public function testSend() {
		$message = new Rox_Mailer_Message(array(
			'from' => 'ramon@example.org',
			'to' => 'john@example.org',
			'subject' => 'Testing'
		));

		$message->addQuotedPrintablePart('text/plain; charset="utf-8"', 'Cinéma');

		$adapter = new Rox_Mailer_Adapter_Smtp;
		$adapter->send($message);
	}
}
