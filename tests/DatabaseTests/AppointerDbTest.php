<?php
require_once 'PHPUnit/Framework.php';

class AppointerDbTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql\n");
	}

    public function testSaveLoad()
    {
    	$appointer = new Appointer();
    	$appointer->setName('Test Appointer');
    	try
    	{
    		$appointer->save();
    		$id = $appointer->getId();
    		$this->assertGreaterThan(0,$id);
    	}
    	catch (Exception $e) { $this->fail($e->getMessage()); }

    	$appointer = new Appointer($id);
    	$this->assertEquals($appointer->getName(),'Test Appointer');

    	$appointer->setName('Test');
    	$appointer->save();

    	$appointer = new Appointer($id);
    	$this->assertEquals($appointer->getName(),'Test');
    }
}
