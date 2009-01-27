<?php
/**
 * A collection class for VotingRecord objects
 *
 * This class creates a select statement, only selecting the ID from each row
 * PDOResultIterator handles iterating and paginating those results.
 * As the results are iterated over, PDOResultIterator will pass each desired
 * ID back to this class's loadResult() which will be responsible for hydrating
 * each VotingRecord object
 *
 * Beyond the basic $fields handled, you will need to write your own handling
 * of whatever extra $fields you need
 *
 * The PDOResultIterator uses prepared queries; it is recommended to use bound
 * parameters for each of the options you handle
 *
 * @copyright 2008-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class VotingRecordList extends PDOResultIterator
{
	/**
	 * Creates a basic select statement for the collection.
	 * Populates the collection if you pass in $fields
	 *
	 * @param array $fields
	 */
	public function __construct($fields=null)
	{
		$this->select = 'select vr.id as id from votingRecords vr';
		if (is_array($fields)) {
			$this->find($fields);
		}
	}

	/**
	 * Populates the collection from the database based on the $fields you handle
	 *
	 * @param array $fields
	 * @param string $sort
	 * @param int $limit
	 * @param string $groupBy
	 */
	public function find($fields=null,$sort='v.date desc',$limit=null,$groupBy=null)
	{
		$this->sort = $sort;
		$this->limit = $limit;
		$this->groupBy = $groupBy;
		$this->joins = 'left join votes v on vr.vote_id=v.id';

		$options = array();
		$parameters = array();
		if (isset($fields['id'])) {
			$options[] = 'vr.id=:id';
			$parameters[':id'] = $fields['id'];
		}
		if (isset($fields['member_id'])) {
			$options[] = 'vr.member_id=:member_id';
			$parameters[':member_id'] = $fields['member_id'];
		}
		if (isset($fields['vote_id'])) {
			$options[] = 'vr.vote_id=:vote_id';
			$parameters[':vote_id'] = $fields['vote_id'];
		}
		if (isset($fields['memberVote'])) {
			$options[] = 'vr.memberVote=:memberVote';
			$parameters[':memberVote'] = $fields['memberVote'];
		}

		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to $options by adding the join SQL
		// to $this->joins here
		if (isset($fields['user_id'])) {
			$this->joins.= ' left join members m on vr.member_id=m.id';
			$options[] = 'm.user_id=:user_id';
			$parameters[':user_id'] = $fields['user_id'];
		}

		if (isset($fields['topicType'])) {
			$this->joins.= ' left join topics t on v.topic_id=t.id';
			$type = $fields['topicType'];
			$options[] = 't.topicType_id=:topicType_id';
			$parameters['topicType_id'] = $type->getId();
		}

		$this->populateList($options,$parameters);
	}


	/**
	 * Loads a single VotingRecord object for the key returned from PDOResultIterator
	 * @param int $key
	 */
	protected function loadResult($key)
	{
		return new VotingRecord($this->list[$key]);
	}

	/**
	 * Returns the percentage that two members have voted the same way
	 *
	 * If you pass in a topic list, the comparison will be only for votes on the given topics
	 * Without a topicList, the comparison will be for any votes the two members share
	 *
	 * @param Member $memberOne
	 * @param Member $otherMember
	 * @param TopicList $topicList (optional)
	 * @return float
	 */
	public static function findAccordancePercentage($memberOne,$otherMember,TopicList $topicList=null)
	{
		$pdo = Database::getConnection();

		$sql = "select a.id,a.memberVote as memberOneVote,b.memberVote as otherMemberVote
				from votingRecords a
				inner join votingRecords b on a.vote_id=b.vote_id
				where a.member_id=:a
				and b.member_id=:b ";
		$parameters = array(':a'=>$memberOne->getId(),':b'=>$otherMember->getId());
		if ($topicList) {
			$sql.= " and a.vote_id in
					(select votes.id from votes where topic_id in
					({$topicList->getSQL()}))";
			$parameters = array_merge($parameters,$topicList->getParameters());
		}

		$query = $pdo->prepare($sql);
		$query->execute($parameters);
		$result = $query->fetchAll(PDO::FETCH_ASSOC);

		$total = count($result);
		$matchedVotes = 0;
		if ($total) {
			foreach ($result as $row) {
				if ($row['memberOneVote'] == $row['otherMemberVote']) {
					$matchedVotes++;
				}
			}
			$percent = round($matchedVotes * 100.0/$total,2);
		}
		else {
			$percent = false;
		}
		return $percent;
	}
}
