<?php
require_once 'PHPUnit/Framework.php';

class VoteDbTest extends PHPUnit_Framework_TestCase
{
	protected $committee;
	protected $topic;
	protected $voteType;

	protected function setUp()
	{
		$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql");

		$topicType = new TopicType();
		$topicType->setName('Vote Test TopicType');
		$topicType->save();

		$committee = new Committee();
		$committee->setName('Vote Test Committee');
		$committee->save();
		$this->committee = $committee;

		$topic = new Topic();
		$topic->setCommittee($this->committee);
		$topic->setTopicType($topicType);
		$topic->setNumber(111);
		$topic->setDescription('Test Topic Description');
		$topic->setSynopsis('Test Topic Synopsis');
		$topic->save();
		$this->topic = $topic;

		$voteType = new VoteType();
		$voteType->setName('Test Vote Type');
		$voteType->save();
		$this->voteType = $voteType;
	}

    public function testSaveLoad()
    {
		$vote = new Vote();
		$vote->setVoteType($this->voteType);
		$vote->setTopic($this->topic);
		$vote->setDate(strtotime('yesterday'));
    	try
		{
			$vote->save();
			$id = $vote->getId();
			$this->assertGreaterThan(0,$id);
		}
		catch (Exception $e) { $this->fail($e->getMessage()); }

		$vote = new Vote($id);
		$this->assertEquals($vote->getDate('Y-m-d'),date('Y-m-d',strtotime('yesterday')));

		$vote->setDate(time());
		$vote->save();

		$vote = new Vote($id);
		$this->assertEquals($vote->getDate('Y-m-d'),date('Y-m-d'));
    }

    public function testGetCommitte()
    {
		$vote = new Vote();
		$vote->setTopic($this->topic);
		$this->assertEquals($vote->getCommittee()->getId(),$this->committee->getId());
    }

    public function testVotingRecords()
    {
		$appointer = new Appointer();
		$appointer->setName('Seat Test Appointer');
		$appointer->save();

		$seat = new Seat();
		$seat->setTitle('Test Seat');
		$seat->setCommittee($this->committee);
		$seat->setAppointer($appointer);
		$seat->save();

		$user = new User();
		$user->setFirstname('Test');
		$user->setLastname('User');
		$user->save();

		$member = new Member();
		$member->setSeat($seat);
		$member->setUser($user);
		$member->save();

		$vote = new Vote();
		$vote->setVoteType($this->voteType);
		$vote->setTopic($this->topic);
		$vote->save();

		$records = array($member->getId()=>'yes');
		$vote->setVotingRecords($records);

		$this->assertTrue($vote->hasVotingRecords());
		foreach ($vote->getVotingRecords() as $record)
		{
			$this->assertEquals($vote->getId(),$record->getVote_id());
		}

		$record = $vote->getVotingRecord($member);
		$this->assertEquals($record->getMemberVote(),'yes');
    }
}
