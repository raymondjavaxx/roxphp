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

namespace rox\test\cases\mailer\adapter;

use \rox\mailer\Message;
use \rox\mailer\adapter\Smtp;

/**
 * Test case for \rox\mailer\adapter\Smtp
 *
 * @package \rox\test
 */
class SmtpTest extends \PHPUnit_Framework_TestCase {

	protected function setUp() {
		if ($f = @fsockopen('127.0.0.1', 25, $errno, $errstr, 1)) {
			fclose($f);
		} else {
			$this->markTestSkipped('No SMTP server running on localhost.');
		}
	}

	public function testSend() {
		$message = new Message(array(
			'from' => 'ramon@example.org',
			'to' => 'john@example.org',
			'subject' => 'Testing'
		));

		$message->addQuotedPrintablePart('text/plain; charset="utf-8"', 'Cinéma');

		$adapter = new Smtp;
		$adapter->send($message);
	}
}
