<?php
require_once 'PHPUnit/Framework.php';

class VoteTypeDbTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql");
	}

    public function testSaveLoad()
    {
		$votetype = new VoteType();
		$votetype->setName('Test VoteType');
    	try
		{
			$votetype->save();
			$id = $votetype->getId();
			$this->assertGreaterThan(0,$id);
		}
		catch (Exception $e) { $this->fail($e->getMessage()); }

		$votetype = new VoteType($id);
		$this->assertEquals($votetype->getName(),'Test VoteType');

		$votetype->setName('Test');
		$votetype->save();

		$votetype = new VoteType($id);
		$this->assertEquals($votetype->getName(),'Test');
    }
}
