<?php
require_once 'PHPUnit/Framework.php';

class TopicTypeDbTest extends PHPUnit_Framework_TestCase
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
    	$topicType = new TopicType();
    	$topicType->setName('Test TopicType');
    	try
    	{
    		$topicType->save();
    		$id = $topicType->getId();
    		$this->assertGreaterThan(0,$id);
    	}
    	catch (Exception $e) { $this->fail($e->getMessage()); }

    	$topicType = new TopicType($id);
    	$this->assertEquals($topicType->getName(),'Test TopicType');

    	$topicType->setName('Test');
    	$topicType->save();

    	$topicType = new TopicType($id);
    	$this->assertEquals($topicType->getName(),'Test');
    }
}
