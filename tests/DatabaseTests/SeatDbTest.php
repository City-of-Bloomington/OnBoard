<?php
require_once 'PHPUnit/Framework.php';

class SeatDbTest extends PHPUnit_Framework_TestCase
{
	protected $committee;
	protected $appointer;
	protected $user;

	protected function setUp()
	{
		$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql\n");

		$committee = new Committee();
		$committee->setName('Seat Test Committee');
		$committee->save();
		$this->committee = $committee;

		$appointer = new Appointer();
		$appointer->setName('Seat Test Appointer');
		$appointer->save();
		$this->appointer = $appointer;

		$users = new UserList();
		$users->find();
		$this->user = $users[0];
	}

	public function testSetCommitte()
	{
		$seat = new Seat();
		$seat->setCommittee_id($this->committee->getId());
		$this->assertEquals($seat->getCommittee()->getId(),$this->committee->getId());

		$seat = new Seat();
		$seat->setCommittee($this->committee);
		$this->assertEquals($seat->getCommittee_id(),$this->committee->getId());
	}

	public function testSetAppointer()
	{
		$seat = new Seat();
		$seat->setAppointer($this->appointer);
		$this->assertEquals($seat->getAppointer_id(),$this->appointer->getId());

		$seat = new Seat();
		$seat->setAppointer_id($this->appointer->getId());
		$this->assertEquals($seat->getAppointer()->getId(),$this->appointer->getId());
	}

	public function testValidateTitle()
	{
		$seat = new Seat();
		$seat->setCommittee($this->committee);
		try { $seat->validate(); }
		catch (Exception $e) { return; }
		$this->fail('Missing Title failed to throw validation error.');
	}

	public function testValidateCommittee()
	{
		$seat = new Seat();
		$seat->setTitle('Test Seat');
		try { $seat->validate(); }
		catch (Exception $e) { return; }
		$this->fail('Missing committee failed to throw validation error.');
	}

	public function testSuccessfulValidate()
	{
		$seat = new Seat();
		$seat->setTitle('Test Seat');
		$seat->setCommittee($this->committee);
		try { $seat->validate(); }
		catch (Exception $e)
		{
			$this->fail('Validation failed even when all required fields were set');
		}
	}

	public function testSaveAndLoad()
	{
		$seat = new Seat();
		$seat->setTitle('Test Seat');
		$seat->setCommittee($this->committee);

		$seat->save();
		$id = $seat->getId();
		$this->assertGreaterThan(0,$id);

		$seat = new Seat($id);
		$seat->setTitle('Updated Test Title');
		$seat->save();

		$seat = new Seat($id);
		$this->assertEquals($seat->getTitle(),'Updated Test Title');
	}

	public function testGetMembers()
	{
		$seat = new Seat();
		$seat->setTitle('Test Seat');
		$seat->setCommittee($this->committee);
		$seat->save();

		$member = new Member();
		$member->setSeat($seat);
		$member->setUser($this->user);
		$member->setTerm_start(strtotime('-2 weeks'));
		$member->setTerm_end(strtotime('-1 week'));
		$member->save();

		$this->assertEquals(1,count($seat->getMembers()));
	}

	public function testGetCurrentMembers()
	{
		$seat = new Seat();
		$seat->setTitle('Test Seat');
		$seat->setCommittee($this->committee);
		$seat->save();

		$member = new Member();
		$member->setSeat($seat);
		$member->setUser($this->user);
		$member->setTerm_start(strtotime('-2 weeks'));
		$member->setTerm_end(strtotime('-1 week'));
		$member->save();

		$this->assertEquals(0,count($seat->getCurrentMembers()));

		$member = new Member();
		$member->setSeat($seat);
		$member->setUser($this->user);
		$member->setTerm_start(strtotime('-2 weeks'));
		$member->setTerm_end(strtotime('+1 week'));
		$member->save();

		$this->assertEquals(1,count($seat->getCurrentMembers()));
	}
}
