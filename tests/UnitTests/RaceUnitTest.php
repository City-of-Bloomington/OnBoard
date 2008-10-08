<?php
require_once 'PHPUnit/Framework.php';

class RaceUnitTest extends PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		$race = new Race();
		try
		{
			$race->validate();
			$this->fail('Missing name failed to throw exception');
		}
		catch (Exception $e) { }

		$race->setName('Test Race');
		$race->validate();
	}

	public function testToString()
	{
		$race = new Race();
		$race->setName('Test Race');
		$this->assertEquals((string) $race,'Test Race');
	}
}
