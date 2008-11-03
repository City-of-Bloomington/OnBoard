<?php
require_once 'PHPUnit/Framework.php';

class RoleDbTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql\n");
	}

	public function testSaveLoad()
	{
		$role = new Role();
		$role->setName('Test Role');
		$role->save();
		$id = $role->getId();

		$role = new Role($id);
		$this->assertEquals($role->getName(),'Test Role');

		$role->setName('Updated Test Role');
		$role->save();

		$role = new Role($id);
		$this->assertEquals($role->getName(),'Updated Test Role');
	}
}
