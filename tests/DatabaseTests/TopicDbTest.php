<?php
require_once 'PHPUnit/Framework.php';

class TopicDbTest extends PHPUnit_Framework_TestCase
{
	protected $committee;
	protected $topicType;

	protected function setUp()
	{
		$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql\n");

		$PDO = Database::getConnection();

		$committee = new Committee();
		$committee->setName('Seat Test Committee');
		$committee->save();
		$this->committee = $committee;

		$topicType = new TopicType();
		$topicType->setName('Topic Test Type');
		$topicType->save();
		$this->topicType = $topicType;
	}

	public function testSaveLoad()
	{
		$topic = new Topic();
		$topic->setTopicType($this->topicType);
		$topic->setCommittee($this->committee);
		$topic->setNumber(111);
		$topic->setDescription('Test Topic Description');
		$topic->setSynopsis('Test Topic Synopsis');
		$topic->save();

		$id = $topic->getId();
		$this->assertGreaterThan(0,$id);

		$topic = new Topic($id);
		$this->assertEquals($topic->getTopicType_id(),$this->topicType->getId());
		$this->assertEquals($topic->getCommittee_id(),$this->committee->getId());
		$this->assertEquals($topic->getNumber(),111);
		$this->assertEquals($topic->getDescription(),'Test Topic Description');
		$this->assertEquals($topic->getSynopsis(),'Test Topic Synopsis');

		$topic->setDescription('Updated Description');
		$topic->save();

		$topic = new Topic($id);
		$this->assertEquals($topic->getDescription(),'Updated Description');
	}
}
