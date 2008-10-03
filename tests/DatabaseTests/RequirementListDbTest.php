<?php
require_once 'PHPUnit/Framework.php';

class RequirementListTest extends PHPUnit_Framework_TestCase
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

	/**
	 * Requirements should always be ordered alphabetically
	 */
	public function testFindOrderedAlphabetically()
	{
		$requirement = new Requirement();
		$requirement->setText('Test Requirement');
		$requirement->save();

		$requirement = new Requirement();
		$requirement->setText('Another Requirement');
		$requirement->save();

		$PDO = Database::getConnection();
		$query = $PDO->query('select text from requirements order by text');
		$result = $query->fetchAll();

		$list = new RequirementList();
		$list->find();
		foreach($list as $i=>$requirement)
		{
			$this->assertEquals($requirement->getText(),$result[$i]['text']);
		}
	}
}
