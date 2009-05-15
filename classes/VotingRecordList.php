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
 * @copyright 2009 City of Bloomington, Indiana
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
		$this->select = 'select distinct vr.id as id from votingRecords vr';
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
	public function find($fields=null,$sort='votes.date desc,people.lastname',$limit=null,$groupBy=null)
	{
		$this->sort = $sort;
		$this->limit = $limit;
		$this->groupBy = $groupBy;
		$this->joins = "left join votes on vr.vote_id=votes.id
						left join terms on vr.term_id=terms.id
						left join people on terms.person_id=people.id";

		$options = array();
		$parameters = array();

		if (isset($fields['id'])) {
			$options[] = 'vr.id=:id';
			$parameters[':id'] = $fields['id'];
		}

		if (isset($fields['term_id'])) {
			$options[] = 'vr.term_id=:term_id';
			$parameters[':term_id'] = $fields['term_id'];
		}

		if (isset($fields['vote_id'])) {
			$options[] = 'vr.vote_id=:vote_id';
			$parameters[':vote_id'] = $fields['vote_id'];
		}

		if (isset($fields['position'])) {
			$options[] = 'vr.position=:position';
			$parameters[':position'] = $fields['position'];
		}


		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to $options by adding the join SQL
		// to $this->joins here
		if (isset($fields['person_id'])) {
			$options[] = 'terms.person_id=:person_id';
			$parameters[':person_id'] = $fields['person_id'];
		}

		if (isset($fields['topicType'])) {
			$this->joins.= ' left join topics on votes.topic_id=topics.id';
			$type = $fields['topicType'];
			$options[] = 'topics.topicType_id=:topicType_id';
			$parameters[':topicType_id'] = $type->getId();
		}

		if (isset($fields['voteType'])) {
			$options[] = 'votes.voteType_id=:voteType_id';
			$parameters[':voteType_id'] = $fields['voteType']->getId();
		}

		if (isset($fields['invalid_for_vote'])) {
			$options[] = "vr.vote_id=:invalid_vote_id
							and (terms.term_start>:invalid_start_date
								or (terms.term_end is not null
									and terms.term_end<:invalid_end_date))";

			$date = $fields['invalid_for_vote']->getDate('Y-m-d');
			$parameters[':invalid_vote_id'] = $fields['invalid_for_vote']->getId();
			$parameters[':invalid_start_date'] = $date;
			$parameters[':invalid_end_date'] = $date;
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
	 * Returns the percentage that two people have voted the same way
	 *
	 * If you pass in a topic list, the comparison will be only for votes on the given topics
	 * Without a topicList, the comparison will be for any votes both people participated in
	 *
	 * Passing in a voteType will limit the calculation to only votingRecords of that
	 * type within the topicList
	 *
	 * @param Person $personOne
	 * @param Person $otherPerson
	 * @param TopicList $topicList (optional)
	 * @param VoteType $voteType (optional)
	 * @return float
	 */
	public static function findAccordancePercentage($personOne,
													$otherPerson,
													TopicList $topicList=null,
													VoteType $voteType=null)
	{
		$select = "select a.id,a.position as personOneVote,b.position as otherPersonVote
					from votingRecords a";
		$joins = "left join terms at on a.term_id=at.id
				inner join votingRecords b on a.vote_id=b.vote_id
				left join terms bt on b.term_id=bt.id";
		$where = "where at.person_id=:a and bt.person_id=:b";

		$parameters = array(':a'=>$personOne->getId(),':b'=>$otherPerson->getId());
		if ($topicList) {
			$where.= " and a.vote_id in
					(select votes.id from votes where topic_id in
					({$topicList->getSQL()}))";
			$parameters = array_merge($parameters,$topicList->getParameters());
		}
		if ($voteType) {
			$joins.= ' left join votes v on a.vote_id=v.id';
			$where.= ' and v.voteType_id=:voteType_id';
			$parameters[':voteType_id'] = $voteType->getId();
		}

		$pdo = Database::getConnection();
		$query = $pdo->prepare("$select $joins $where");
		$query->execute($parameters);
		$result = $query->fetchAll(PDO::FETCH_ASSOC);

		$total = count($result);
		$matchedVotes = 0;
		if ($total) {
			foreach ($result as $row) {
				if ($row['personOneVote'] == $row['otherPersonVote']) {
					$matchedVotes++;
				}
			}
			return round($matchedVotes * 100.0/$total,2);
		}
	}
}
