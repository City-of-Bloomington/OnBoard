<?php
require_once 'PHPUnit/Framework.php';

class RoleUnitTest extends PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		$role = new Role();
		try
		{
			$role->validate();
			$this->fail('Missing name failed to throw exception');
		}
		catch (Exception $e) { }

		$role->setName('Test Role');
		$role->validate();
	}

	public function testToString()
	{
		$role = new Role();
		$role->setName('Test Role');
		$this->assertEquals((string) $role,'Test Role');
	}
}
