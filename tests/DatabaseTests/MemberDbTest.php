<?php
require_once 'PHPUnit/Framework.php';

class MemberDbTest extends PHPUnit_Framework_TestCase
{
	protected $seat;
	protected $user;

	protected function setUp()
	{
		$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql\n");

		$committee = new Committee();
		$committee->setName('Seat Test Committee');
		$committee->save();

		$appointer = new Appointer();
		$appointer->setName('Seat Test Appointer');
		$appointer->save();

		$seat = new Seat();
		$seat->setTitle('Test Seat');
		$seat->setCommittee($committee);
		$seat->setAppointer($appointer);
		$seat->save();
		$this->seat = $seat;

		$user = new User();
		$user->setFirstname('Test');
		$user->setLastname('User');
		$user->save();
		$this->user = $user;
	}

	public function testSaveLoadDelete()
	{
		$member = new Member();
		$member->setSeat($this->seat);
		$member->setUser($this->user);

		$member->save();
		$id = $member->getId();
		$this->assertGreaterThan(0,$member->getId());

		$member = new Member($id);
		$this->assertEquals($member->getId(),$id);

		$now = time();
		$date = date('Y-m-d');
		$member->setTerm_end($now);
		$this->assertEquals($member->getTerm_end('Y-m-d'),$date);
		$member->save();

		$member = new Member($id);
		$this->assertEquals($member->getTerm_end('Y-m-d'),$date);

		$member->delete();
		try { $member = new Member($id); }
		catch (Exception $e) { return; }
		$this->fail('Test member was not deleted');
	}

	public function testDelete()
	{
		$list = new MemberList();
		$list->find();
		if (count($list))
		{
			foreach($list as $member)
			{
				try { $member->delete(); }
				catch (Exception $e) { $this->fail($e->getMessage()); }
			}
		}
		else
		{
			$this->markTestIncomplete('No Members in the system to test');
		}
	}

	public function testSeatMemberLimit()
	{
		# The seat member limit should be one, by default
		$member = new Member();
		$member->setSeat($this->seat);
		$member->setUser($this->user);
		$member->save();

		$member = new Member();
		$member->setSeat($this->seat);
		$member->setUser($this->user);

		try { $member->save(); }
		catch (Exception $e) { return; }
		$this->fail('We were able to exceed the max number of members allowed for a seat');
	}

	public function testGetCommittee()
	{
		$member = new Member();
		$member->setSeat($this->seat);
		$this->assertEquals($member->getCommittee()->getName(),'Seat Test Committee');
	}

	public function testGetVotePercentages()
	{
		$this->markTestIncomplete('Need test data to confirm this functions calculates known values');
	}
}
