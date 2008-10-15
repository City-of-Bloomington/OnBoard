<?php
require_once 'PHPUnit/Framework.php';

class RoleListDbTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql\n");
	}

	/**
	 * Roles must be ordered alphabetically
	 * The list class should return Roles as strings, not as objects
	 */
	public function testFind()
	{
		$PDO = Database::getConnection();
		$query = $PDO->query('select name from roles order by name');
		$result = $query->fetchAll();

		$list = new RoleList();
		$list->find();
		foreach($list as $i=>$role)
		{
			$this->assertEquals($role,$result[$i]['name']);
		}
	}
}
