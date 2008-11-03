<?php
require_once 'PHPUnit/Framework.php';

class VoteTypeListDbTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql");
	}

	/**
	 * Makes sure find returns all voteTypes ordered correctly by default
	 */
	public function testFindOrderedByName()
	{
		$PDO = Database::getConnection();
		$query = $PDO->query('select id from voteTypes order by id');
		$result = $query->fetchAll();

		$list = new VoteTypeList();
		$list->find();
		$this->assertEquals($list->getSort(),'id');

		foreach($list as $i=>$votetype)
		{
			$this->assertEquals($votetype->getId(),$result[$i]['id']);
		}
    }
}
