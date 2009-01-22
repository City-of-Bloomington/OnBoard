<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
class VotingRecordList extends PDOResultIterator
{

	public function __construct($fields=null)
	{
		$this->select = 'select vr.id as id from votingRecords vr';
		if (is_array($fields)) $this->find($fields);
	}


	public function find($fields=null,$sort='v.date desc',$limit=null,$groupBy=null)
	{
		$this->sort = $sort;
		$this->limit = $limit;
		$this->groupBy = $groupBy;
		$this->joins = 'left join votes v on vr.vote_id=v.id';

		$options = array();
		$parameters = array();
		if (isset($fields['id']))
		{
			$options[] = 'vr.id=:id';
			$parameters[':id'] = $fields['id'];
		}
		if (isset($fields['member_id']))
		{
			$options[] = 'vr.member_id=:member_id';
			$parameters[':member_id'] = $fields['member_id'];
		}
		if (isset($fields['vote_id']))
		{
			$options[] = 'vr.vote_id=:vote_id';
			$parameters[':vote_id'] = $fields['vote_id'];
		}
		if (isset($fields['memberVote']))
		{
			$options[] = 'vr.memberVote=:memberVote';
			$parameters[':memberVote'] = $fields['memberVote'];
		}

		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to $options by adding the join SQL
		// to $this->joins here
		if (isset($fields['user_id']))
		{
			$this->joins.= ' left join members m on vr.member_id=m.id';
			$options[] = 'm.user_id=:user_id';
			$parameters[':user_id'] = $fields['user_id'];
		}

		if (isset($fields['topicType']))
		{
			$this->joins.= ' left join topics t on v.topic_id=t.id';
			$type = $fields['topicType'];
			$options[] = 't.topicType_id=:topicType_id';
			$parameters['topicType_id'] = $type->getId();
		}

		$this->populateList($options,$parameters);
	}


	protected function loadResult($key) { return new VotingRecord($this->list[$key]); }

	/**
	 * Returns the percentage that two members have voted the same way
	 * on all the votes they have done
	 * @param Member $memberOne
	 * @param Member $otherMember
	 * @return float
	 */
	public static function findAccordancePercentage($memberOne,$otherMember)
	{
		$PDO = Database::getConnection();

		$total=0;
		$match=0;
		$percent=0;
		//
		// Total votes
		$qq = "select count(*) as total from votingRecords v1 where v1.member_id=?";
		$query = $PDO->prepare($qq);
		$query->execute(array($memberOne->getId()));
		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		if(count($result)) {
			$total = $result[0]['total'];
		}
		//
		// accorded votes
		$qq2 = "select count(*) as count from votingRecords v1,votingRecords v2
				where v1.vote_id=v2.vote_id
				and v1.memberVote=v2.memberVote
				and v2.member_id=?
				and v1.member_id=?";
		$query = $PDO->prepare($qq2);
		$query->execute(array($memberOne->getId(),$otherMember->getId()));
		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		if(count($result) > 0) {
			$match = $result[0]['count'];
		}
		if($total > 0) {
			$percent = round($match * 100.0/$total,2);
		}
		return $percent;
	}
}
