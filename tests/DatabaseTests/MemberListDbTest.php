<?php
require_once 'PHPUnit/Framework.php';

class MemberListTest extends PHPUnit_Framework_TestCase
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
     * Make sure find is ordered by the term start dates
     */
    public function testFind()
    {
    	$PDO = Database::getConnection();
    	$query = $PDO->query('select id from members order by term_start desc');
    	$result = $query->fetchAll();

    	$list = new MemberList();
    	$list->find();
    	foreach($list as $i=>$member)
    	{
    		$this->assertEquals($member->getId(),$result[$i]['id']);
    	}
    }
}
