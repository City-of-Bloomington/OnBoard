<?php
require_once 'PHPUnit/Framework.php';

class SeatDbTest extends PHPUnit_Framework_TestCase
{
	protected $committee;
	protected $appointer;

	protected function setUp()
	{
		$dir = dirname(__FILE__);

		$PDO = Database::getConnection();
		$PDO->exec('drop database '.DB_NAME);
		$PDO->exec('create database '.DB_NAME);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql\n");

		$PDO = Database::getConnection(true);

		$committee = new Committee();
		$committee->setName('Seat Test Committee');
		$committee->save();
		$this->committee = $committee;

		$appointer = new Appointer();
		$appointer->setName('Seat Test Appointer');
		$appointer->save();
		$this->appointer = $appointer;
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

	public function testValidate()
	{
		$seat = new Seat();
		$seat->setTitle('Test Seat');
		$seat->setCommittee($this->committee);
		try { $seat->validate(); }
		catch (Exception $e)
		{
			$this->fail('Validation failed even when all required fields were set');
		}

		$seat->setTitle('');
		try
		{
			$seat->validate();
			$this->fail('Missing Title failed to throw validation error.');
		}
		catch (Exception $e) { }

		$seat->setTitle('Test Seat');
		$seat->setCommittee_id(null);
		try
		{
			$seat->validate();
			$this->fail('Missing committee failed to throw validation error.');
		}
		catch (Exception $e) { }
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
}
