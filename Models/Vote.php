<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Vote extends ActiveRecord
{
	protected $tablename = 'votes';

	protected $voteType;
	protected $topic;

	public static $outcomes = ['pass', 'fail'];

	/**
	 * Populates the object with data
	 *
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * Passing in a scalar will load the data from the database.
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int|string|array $id (ID, email, username)
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$this->exchangeArray($id);
			}
			else {
				$zend_db = Database::getConnection();
				$sql = 'select * from votes where id=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if ($result) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('votes/unknownVote');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setDate(date(DATE_FORMAT));
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		if (!$this->getVoteType_id() || !$this->getTopic_id()) {
			throw new \Exception('missingRequiredFields');
		}

		if (!$this->getDate()) {
			$this->setDate(date(DATE_FORMAT));
		}
	}

	public function save()
	{
		parent::save();
		$this->deleteInvalidVotingRecords();
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()          { return parent::get('id');   }
	public function getOutcome()     { return parent::get('outcome'); }
	public function getVoteType_id() { return parent::get('voteType_id'); }
	public function getTopic_id()    { return parent::get('topic_id'); }
	public function getVoteType()    { return parent::getForeignKeyObject(__namespace__.'\VoteType', 'voteType_id'); }
	public function getTopic()       { return parent::getForeignKeyObject(__namespace__.'\Topic',    'topic_id'); }
	public function getDate($f=null) { return parent::getDateData('date', $f); }

	public function setOutcome    ($s) { parent::set('outcome', $s=='pass' ? 'pass' : 'fail'); }
	public function setVoteType_id($i) { parent::setForeignKeyField (__namespace__.'\VoteType', 'voteType_id', $i); }
	public function setTopic_id   ($i) { parent::setForeignKeyField (__namespace__.'\Topic',    'topic_id',    $i); }
	public function setVoteType   ($o) { parent::setForeignKeyObject(__namespace__.'\VoteType', 'voteType_id', $o); }
	public function setTopic      ($o) { parent::setForeignKeyObject(__namespace__.'\Topic',    'topic_id',    $o); }
	public function setDate       ($d) { parent::setDateData('date', $d); }

	public function handleUpdate($post)
	{
		$this->setDate($post['date']);
		$this->setVoteType_id($post['voteType_id']);
		$this->setOutcome($post['outcome']);
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getUrl() { return BASE_URL.'/votes/view.php?vote_id='.$this->getId(); }
	public function getUri() { return BASE_URI.'/votes/view.php?vote_id='.$this->getId(); }

	/**
	 * Returns just one term's record on this vote
	 *
	 * @param Term $term
	 * @return VotingRecord
	 */
	public function getVotingRecord($term)
	{
		$table = new VotingRecordTable();
		$list = $table->find(['vote_id'=>$this->getId(),'term_id'=>$term->getId()]);
		if (count($list)) {
			return $list[0];
		}
		else {
			$votingRecord = new VotingRecord();
			$votingRecord->setVote($this);
			$votingRecord->setTerm($term);
			return $votingRecord;
		}
	}

	/**
	 * Returns the set of records matching the result you ask for.
	 * If no result is given, it returns the full list
	 *
	 * @return Zend\Db\ResultSet
	 */
	public function getVotingRecords($position=null)
	{
		if ($this->getId()) {
			$fields = ['vote_id'=>$this->getId()];
			if ($position) { $fields['position'] = $position; }

			$table = new VotingRecordTable();
			return $table->find($fields);
		}
	}

	/**
	 * @return boolean
	 */
	public function hasVotingRecords()
	{
		return count($this->getVotingRecords()) ? true : false;
	}

	/**
	 * @param array $records A POST array of records with term_id as the index
	 */
	public function setVotingRecords(array $records)
	{
		if ($this->getId()) {
			$zend_db = Database::getConnection();

			$zend_db->createStatement('delete from votingRecords where vote_id=?')->execute([$this->getId()]);

			$query = $zend_db->createStatement('insert votingRecords set vote_id=?,term_id=?,position=?');
			foreach ($records as $term_id=>$position) {
				$query->execute([$this->getId(),$term_id,$position]);
			}
		}
	}

	/**
	 * @return Committee
	 */
	public function getCommittee()
	{
		return $this->getTopic()->getCommittee();
	}

	/**
	 * Returns an associative array of Term objects using the term_id as the key
	 *
	 * We need to return all the current terms, as well as any other terms that are
	 * hanging around with votingRecords for this vote
	 *
	 * Because the dates for all this stuff can be edited at any time, votes
	 * can be entered for "current" terms, only to have the dates changed later.
	 * The votingRecords for those non-current terms are still in the system
	 * and need to be displayed. (If only to make someone clean up the data)
	 *
	 * @return array
	 */
	public function getTerms()
	{
		$terms = array();
		foreach ($this->getCommittee()->getCurrentTerms($this->getDate()) as $term) {
			$terms[$term->getId()] = $term;
		}

		// Merge the set of terms for the votingRecords we have with the
		// set of terms that are current
		foreach ($this->getVotingRecords() as $votingRecord) {
			if (!array_key_exists($votingRecord->getTerm_id(),$terms)) {
				$terms[$votingRecord->getTerm_id()] = $votingRecord->getTerm();
			}
		}

		return $terms;
	}

	/**
	 * Invalid Voting Records are votingRecords where the vote date does not occur
	 * during the term for the votingRecord.  This happens as people change term dates
	 * or vote dates after votingRecords are entered.
	 *
	 * @return array VotingRecords
	 */
	public function getInvalidVotingRecords()
	{
		$invalidVotingRecords = array();

		$zend_db = Database::getConnection();
		$sql = "select vr.id from votingRecords vr
				left join terms t on vr.term_id=t.id
				left join votes v on vr.vote_id=v.id
				where vr.vote_id=?
				and (t.term_start>?
				or (t.term_end is not null and t.term_end<?))";
		$query = $zend_db->createStatement($sql);
		$result = $query->execute([$this->getId(), $this->getDate('Y-m-d'), $this->getDate('Y-m-d')]);
		foreach ($result as $row) {
			$invalidVotingRecords[] = new VotingRecord($row['id']);
		}
		return $invalidVotingRecords;
	}

	/**
	 * @return boolean
	 */
	public function hasInvalidVotingRecords()
	{
		return count($this->getInvalidVotingRecords()) ? true : false;
	}

	/**
	 * Deletes all the invalid voting records for this vote
	 */
	public function deleteInvalidVotingRecords()
	{
		foreach ($this->getInvalidVotingRecords() as $votingRecord) {
			$votingRecord->delete();
		}
	}
}
