<?php
require_once 'PHPUnit/Framework.php';

class AppointerTest extends PHPUnit_Framework_TestCase
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

    public function testValidate()
    {
    	# Name Should be required
    	$appointer = new Appointer();
    	try
    	{
    		$appointer->validate();
    		$this->fail('Missing name did not throw an exception');
    	}
    	catch (Exception $e)
    	{
			# Success
    	}
    }

    public function testSaveLoadDelete()
    {
    	$appointer = new Appointer();
    	$appointer->setName('Test Appointer');
    	try
    	{
    		$appointer->save();
    		$id = $appointer->getId();
    		$this->assertGreaterThan(1,$id);
    	}
    	catch (Exception $e) { $this->fail($e->getMessage()); }

    	$appointer = new Appointer($id);
    	$this->assertEquals($appointer->getName(),'Test Appointer');

    	$appointer->setName('Test');
    	$appointer->save();

    	$appointer = new Appointer($id);
    	$this->assertEquals($appointer->getName(),'Test');

    	$appointer->delete();
    	try
    	{
			$appointer = new Appointer($id);
			$this->fail('Appointer failed to delete');
    	}
    	catch (Exception $e)
    	{
			# Success
    	}
    }

    public function testDelete()
    {
    	$list = new AppointerList();
    	$list->find();

    	foreach($list as $appointer) { $appointer->delete(); }
    }
}
