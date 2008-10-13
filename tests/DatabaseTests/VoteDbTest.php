<?php
require_once 'PHPUnit/Framework.php';

class VoteDbTest extends PHPUnit_Framework_TestCase
{
	protected $topic;
	protected $voteType;

	protected function setUp()
	{
		$dir = dirname(__FILE__);

		$PDO = Database::getConnection();
		$PDO->exec('drop database '.DB_NAME);
		$PDO->exec('create database '.DB_NAME);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql");

		$PDO = Database::getConnection(true);

		$query = $PDO->query('select id from topics limit 1');
		$result = $query->fetchAll();
		$this->topic = new Topic($result[0]['id']);

		$query = $PDO->query('select id from voteTypes limit 1');
		$result = $query->fetchAll();
		$this->voteType = new VoteType($result[0]['id']);
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
}
