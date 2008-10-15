<?php
require_once 'PHPUnit/Framework.php';

class PhoneNumberUnitTest extends PHPUnit_Framework_TestCase
{
	public function testPrivateRestrictions()
	{
		$phoneNumber = new PhoneNumber();
		$phoneNumber->setNumber('111-111-1111');

		$this->assertEquals($phoneNumber->getNumber(),'111-111-1111');

		$phoneNumber->setPrivate(true);

		$this->assertNull($phoneNumber->getNumber());
	}
}