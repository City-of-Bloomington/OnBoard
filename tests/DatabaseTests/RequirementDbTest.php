<?php
require_once 'PHPUnit/Framework.php';

class RequirementDbTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$dir = dirname(__FILE__);

		$PDO = Database::getConnection();
		$PDO->exec('drop database '.DB_NAME);
		$PDO->exec('create database '.DB_NAME);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql\n");

		$PDO = Database::getConnection(true);
	}

	public function testSaveLoadDelete()
	{
		$requirement = new Requirement();
		$requirement->setText('Test Requirement');
		$requirement->save();
		$id = $requirement->getId();
		$this->assertGreaterThan(1,$id);

		$requirement = new Requirement($id);
		$this->assertEquals($requirement->getId(),$id);

		$requirement->setText('Updated Test Requirement');
		$requirement->save();
		$requirement = new Requirement($id);
		$this->assertEquals($requirement->getText(),'Updated Test Requirement');

		$requirement->delete();

		try
		{
			$requirement = new Requirement($id);
			$this->fail('Failed to delete requirement');
		}
		catch (Exception $e) { }
	}

	public function testDelete()
	{
		$list = new RequirementList();
		$list->find();
		foreach($list as $requirement)
		{
			$requirement->delete();
		}
	}
}
