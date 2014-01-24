<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class VotingRecord extends ActiveRecord
{
	protected $tablename = 'votingRecords';

	protected $term;
	protected $vote;

	public static $positions = 	['yes','no','abstain','absent'];


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
				$sql = 'select * from votingRecords where id=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if ($result) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('votingRecords/unknownVotingRecord');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		if (!$this->getTerm_id() || !$this->getVote_id()) {
			throw new \Exception('missingRequiredFields');
		}

		if (!$this->getPosition()) {
			$this->setPosition('absent');
		}
	}

	public function save()   { parent::save();   }
	public function delete() { parent::delete(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()       { return parent::get('id');   }
	public function getPosition() { return parent::get('position'); }
	public function getTerm_id()  { return parent::get('term_id'); }
	public function getVote_id()  { return parent::get('vote_id'); }
	public function getTerm()     { return parent::getForeignKeyObject(__namespace__.'\Term', 'term_id'); }
	public function getVote()     { return parent::getForeignKeyObject(__namespace__.'\Vote', 'vote_id'); }

	public function setPosition($s) { parent::set('position', $s); }
	public function setTerm_id ($i) { parent::setForeignKeyField (__namespace__.'\Term', 'term_id', $i); }
	public function setVote_id ($i) { parent::setForeignKeyField (__namespace__.'\Vote', 'vote_id', $i); }
	public function setTerm    ($o) { parent::setForeignKeyObject(__namespace__.'\Term', 'term_id', $o); }
	public function setVote    ($o) { parent::setForeignKeyObject(__namespace__.'\Vote', 'vote_id', $o); }



	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function getDate($f=null) { return $this->getVote()->getDate($f);   }
	public function getTopic()       { return $this->getVote()->getTopic();    }
	public function getVoteType()    { return $this->getVote()->getVoteType(); }

	public function getPerson() { return $this->getTerm()->getPerson(); }

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
	 * @param array $topic_ids (optional) An array of topic_id's
	 * @param VoteType $voteType (optional)
	 * @return float
	 */
	public static function findAccordancePercentage(Person $personOne,
													Person $otherPerson,
													array $topic_ids=null,
													VoteType $voteType=null)
	{
		$a = (int)  $personOne->getId();
		$b = (int)$otherPerson->getId();

		$select = "select a.id,
						  a.position as personOneVote,
						  b.position as otherPersonVote
					 from votingRecords a";
		$joins =  "left join terms        at on a.term_id=at.id
				  inner join votingRecords b on a.vote_id=b.vote_id
				   left join terms        bt on b.term_id=bt.id";
		$where = "where at.person_id=$a and bt.person_id=$b";

		if ($topic_ids) {
			// Clean the id's and make sure they're just numbers
			$t = [];
			foreach ($topic_ids as $id) { $t[] = (int)$id; }
			$t = implode(',', $t);
			$where.= " and a.vote_id in (select votes.id from votes where topic_id in ($t))";
		}
		if ($voteType) {
			$v = (int)$voteType->getId();

			$joins.= ' left join votes v on a.vote_id=v.id';
			$where.= " and v.voteType_id=$v";
		}

		$zend_db = Database::getConnection();
		$query = $zend_db->createStatement("$select $joins $where");
		$result = $query->execute();

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
