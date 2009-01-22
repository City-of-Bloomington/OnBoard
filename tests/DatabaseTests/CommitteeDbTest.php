<?php
require_once 'PHPUnit/Framework.php';

class CommitteeDbTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql\n");
	}

	public function testSaveAndLoad()
	{
    	$committee = new Committee();
    	$committee->setName('test committee');

		$committee->save();
		$id = $committee->getId();
		$this->assertGreaterThan(0,$id);

		$committee = new Committee($id);
		$this->assertEquals($committee->getName(),'test committee');

		$committee->setName('New test committee');
		$committee->save();

		$committee = new Committee($id);
		$this->assertEquals($committee->getName(),'New test committee');
	}

    public function testGetSeats()
    {
    	$list = new CommitteeList();
    	$list->find();
    	foreach ($list as $committee)
    	{
    		$this->assertTrue($committee->getSeats() instanceof SeatList);
    		foreach ($committee->getSeats() as $seat)
    		{
				$this->assertEquals($seat->getCommittee_id(),$committee->getId());
    		}
    	}
    }

    public function testGetTopics()
    {
    	$list = new CommitteeList();
    	$list->find();
    	foreach ($list as $committee)
    	{
			$this->assertTrue($committee->getTopics() instanceof TopicList);
			foreach ($committee->getTopics() as $topic)
			{
				$this->assertEquals($topic->getCommittee_id(),$committee->getId());
			}
		}
    }

    public function testGetMaxCurrentMembers()
    {
    	$committee = new Committee();
    	$committee->setName('test committee');
		$committee->save();

		$seat = new Seat();
		$seat->setTitle('Test Seat');
		$seat->setCommittee($committee);
		$seat->save();

		$this->assertEquals(1,$committee->getMaxCurrentMembers());

		$seat = new Seat();
		$seat->setTitle('Test Seat');
		$seat->setCommittee($committee);
		$seat->save();
		$id = $seat->getId();

		$this->assertEquals(2,$committee->getMaxCurrentMembers());

		$seat->setMaxCurrentMembers(2);
		$seat->save();

		$this->assertEquals(3,$committee->getMaxCurrentMembers());
    }
}
