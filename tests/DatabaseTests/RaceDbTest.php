<?php
require_once 'PHPUnit/Framework.php';

class RaceDbTest extends PHPUnit_Framework_TestCase
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

    public function testSaveLoad()
    {
    	$race = new Race();
    	$race->setName('Test Race');
    	try
    	{
    		$race->save();
    		$id = $race->getId();
    		$this->assertGreaterThan(0,$id);
    	}
    	catch (Exception $e) { $this->fail($e->getMessage()); }

    	$race = new Race($id);
    	$this->assertEquals($race->getName(),'Test Race');

    	$race->setName('Test');
    	$race->save();

    	$race = new Race($id);
    	$this->assertEquals($race->getName(),'Test');
    }
}
