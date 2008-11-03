<?php
require_once 'PHPUnit/Framework.php';

class MemberListDbTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql\n");
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
