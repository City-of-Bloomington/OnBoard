<?php
require_once 'PHPUnit/Framework.php';

class VotingRecordListDbTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		$dir = dirname(__FILE__);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME." < $dir/../testData.sql");

	}

    /**
     * @todo Implement testFind().
     */
    public function testFind() {
    	$PDO = Database::getConnection();
    	$query = $PDO->query('select vr.id as id from votingRecords vr left join votes v on  vr.vote_id=v.id order by v.date desc');
    	$result = $query->fetchAll();

    	$list = new VotingRecordList();
    	$list->find();
    	foreach($list as $i=>$votingRecord)
    	{
    		$this->assertEquals($votingRecord->getId(),$result[$i]['id']);
    	}
    }

	public function testFindMemberVotesByType()
	{
		$PDO = Database::getConnection();
		$sql = "select vr.id from votingRecords vr
				left join votes v on vr.vote_id=v.id
				left join topics tp on v.topic_id=tp.id
				where tp.topicType_id=?
				and vr.member_id=?
				and vr.memberVote=?
				order by v.date desc";
		$query = $PDO->prepare($sql);

		$members = new MemberList();
		$members->find();

		$topicTypes = new TopicTypeList();
		$topicTypes->find();

		$memberVotes = array('yes','no','absent','abstain');
		foreach($members as $member)
		{
			foreach($topicTypes as $topicType)
			{
				foreach($memberVotes as $memberVote)
				{
					$query->execute(array($topicType->getId(),$member->getId(),$memberVote));
					$result = $query->fetchAll(PDO::FETCH_ASSOC);

					$search = array('member_id'=>$member->getId(),
									'topicType'=>$topicType,
									'memberVote'=>$memberVote);
					$list = new VotingRecordList($search);
					foreach($list as $i=>$votingRecord)
					{
						$this->assertEquals($votingRecord->getId(),$result[$i]['id']);
					}
				}
			}
		}

	}
}
