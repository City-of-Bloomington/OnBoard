<?php
require_once 'PHPUnit/Framework.php';

class CommitteeListTest extends PHPUnit_Framework_TestCase
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
	 * Make sure Find returns committees ordered by name
	 */
    public function testCommitteesOrderedByName()
    {
    	$PDO = Database::getConnection();
    	$query = $PDO->query('select name from committees order by name');
    	$result = $query->fetchAll();

    	$list = new CommitteeList();
    	$list->find();
    	$this->assertEquals($list->getSort(),'name');

    	foreach($list as $i=>$committee)
    	{
    		$this->assertEquals($committee->getName(),$result[$i]['name']);
    	}
    }
}
