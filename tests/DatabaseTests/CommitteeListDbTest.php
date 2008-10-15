<?php
require_once 'PHPUnit/Framework.php';

class CommitteeListDbTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql\n");
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
