<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Term extends ActiveRecord
{
	protected $tablename = 'terms';

	protected $committee;
	protected $seat;
	protected $person;

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
				$sql = 'select * from terms where id=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if ($result) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('terms/unknownTerm');
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
        $committee = $this->getCommittee();
        if (!$committee) {
            throw new \Exception('terms/missingCommittee');
        }
        if ($committee->getType() === 'seated' && !$this->getSeat_id()) {
            throw new \Exception('terms/missingSeat');
        }
        if (!$this->getPerson_id()) {
            throw new \Exception('terms/missingPerson');
        }

		if (!$this->getTerm_start()) {
			$this->setTerm_start(date(DATE_FORMAT));
		}

		// Make sure the end date falls after the start date
		$start = (int)$this->getTerm_start('U');
		$end   = (int)$this->getTerm_end  ('U');
		if ($end && $end < $start) { throw new \Exception('terms/invalidEndDate'); }

		// Make sure this term does not exceed the maxCurrentTerms for the seat
		if ($committee->getType() === 'seated') {
            $seat = $this->getSeat();
            $max  = $seat->getMaxCurrentTerms();
            if (($start <= time() && (!$end || $end >= time())) && $max) {
                // The term we're adding is current, make sure there's room
                $count = count($seat->getCurrentTerms());
                if (!$this->getId()) {
                    $count++;
                }
                if ($count > $max) {
                    throw new \Exception('seats/maxCurrentTermsFilled');
                }
            }
        }

		// Make sure this person is not serving overlapping terms for the same committee
		$zend_db = Database::getConnection();
		$sql = "select t.id from terms t
				where t.committee_id=?
				  and t.person_id=?
				  and (?<t.term_end and ?>t.term_start)";
		if ($this->getId()) { $sql.= ' and t.id!='.$this->getId(); }

		$result = $zend_db->createStatement($sql)->execute([
			$this->getCommittee()->getId(),
			$this->getPerson_id(),
			$this->getTerm_start(),$this->getTerm_end()
		]);
		if (count($result) > 1) {
			throw new \Exception('terms/overlappingTerms');
		}
	}

	public function save()
	{
		parent::save();
		$this->deleteInvalidVotingRecords();
	}

	public function delete()
	{
		if ($this->getId()) {
			$zend_db = Database::getConnection();
			$zend_db->query('delete from votingRecords where term_id=?', [$this->getId()]);

			parent::delete();
		}
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()           { return parent::get('id'          ); }
	public function getCommittee_id() { return parent::get('committee_id'); }
	public function getSeat_id()      { return parent::get('seat_id'     ); }
	public function getPerson_id()    { return parent::get('person_id'   ); }
	public function getCommittee()    { return parent::getForeignKeyObject(__namespace__.'\Committee', 'committee_id'); }
	public function getSeat()         { return parent::getForeignKeyObject(__namespace__.'\Seat',      'seat_id'     ); }
	public function getPerson()       { return parent::getForeignKeyObject(__namespace__.'\Person',    'person_id'   ); }
	public function getTerm_start($f=null) { return parent::getDateData('term_start', $f); }
	public function getTerm_end  ($f=null) { return parent::getDateData('term_end',   $f); }
	public function getCarryOver() { return parent::get('carryover'); }

	public function setCommittee_id($i) { parent::setForeignKeyField (__namespace__.'\Committee', 'committee_id', $i); }
	public function setSeat_id     ($i) { parent::setForeignKeyField (__namespace__.'\Seat',      'seat_id',      $i); }
	public function setPerson_id   ($i) { parent::setForeignKeyField (__namespace__.'\Person',    'person_id',    $i); }
	public function setCommittee   ($o) { parent::setForeignKeyObject(__namespace__.'\Committee', 'committee_id', $o); }
	public function setSeat        ($o) { parent::setForeignKeyObject(__namespace__.'\Seat',      'seat_id',      $o); }
	public function setPerson      ($o) { parent::setForeignKeyObject(__namespace__.'\Person',    'person_id',    $o); }
	public function setTerm_start($d) { parent::setDateData('term_start', $d); }
	public function setTerm_end  ($d) { parent::setDateData('term_end',   $d); }
	public function setCarryOver($b) { parent::set('carryover', $b ? 1 : null); }

	public function handleUpdate($post)
	{
        $fields = ['committee_id', 'seat_id', 'person_id', 'term_start', 'term_end'];
        foreach ($fields as $f) {
            $set = 'set'.ucfirst($f);
            $this->$set($post[$f]);
        }

        $this->setCarryOver(!empty($post['carryover']));
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function __toString() { return parent::get('text'); }

	public function getAppointer()
	{
        $seat = $this->getSeat();
        if ($seat) {
            return $seat->getAppointer();
        }
	}

	/**
	 * @return VotingRecordList
	 */
	public function getVotingRecords()
	{
		$table = new VotingRecordTable();
		return $table->find(['term_id'=>$this->getId()]);
	}

	/**
	 * @return boolean
	 */
	public function isCarryingOver()
	{
        return $this->getCarryOver() ? true : false;
	}

	/**
	 * @return boolean
	 */
	public function isSafeToDelete()
	{
		return (count($this->getVotingRecords()) == 0) ? true : false;
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
		$zend_db = Database::getConnection();
		$parameters = [$this->getId()];

		$dateCheck = "?>v.date";
		$parameters[] = $this->getTerm_start('Y-m-d');

		if ($this->getTerm_end()) {
			$dateCheck.= " or ?<v.date";
			$parameters[] = $this->getTerm_end('Y-m-d');
		}

		$sql = "select vr.id from votingRecords vr
				left join terms t on vr.term_id=t.id
				left join votes v on vr.vote_id=v.id
				where vr.term_id=?
				and ($dateCheck)";

		$result = $zend_db->createStatement($sql)->execute($parameters);

		$invalidVotingRecords = array();
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
	 * Deletes all the invalid voting records for this term
	 */
	public function deleteInvalidVotingRecords()
	{
		foreach ($this->getInvalidVotingRecords() as $votingRecord) {
			$votingRecord->delete();
		}
	}
}
