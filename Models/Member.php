<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Member extends ActiveRecord
{
	protected $tablename = 'members';

	protected $committee;
	protected $seat;
	protected $term;
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
				$sql = 'select * from members where id=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if ($result) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('members/unknown');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
		}
	}

	public function validate()
	{
        if (!$this->getCommittee_id()) { throw new \Exception('missingCommittee'); }
        if (!$this->getPerson_id())    { throw new \Exception('missingPerson'); }

        if ($this->getCommittee()->getType() === 'seated'
            && !$this->getSeat_id()) {
            throw new \Exception('missingSeat');
        }

        $seat = $this->getSeat();
        if ($seat && $seat->getType() === 'termed'
            && !$this->getTerm_id()) {
            throw new \Exception('missingTerm');
        }

		// Make sure the end date falls after the start date
		$start = (int)$this->getStartDate('U');
		$end   = (int)$this->getEndDate  ('U');
		if ($end && $end < $start) { throw new \Exception('invalidEndDate'); }

		// Make sure this person is not serving overlapping terms for the same committee
		$zend_db = Database::getConnection();
		$sql = "select id from members
				where committee_id=?
				  and person_id=?
				  and (?<endDate and ?>startDate)";
		if ($this->getId()) { $sql.= ' and m.id!='.$this->getId(); }
		$result = $zend_db->createStatement($sql)->execute([
			$this->getCommittee_id(),
			$this->getPerson_id(),
			$this->getStartDate(),$this->getEndDate()
		]);
		if (count($result) > 0) {
			throw new \Exception('members/overlappingTerms');
		}

		// Make sure this service does not overlap with another member for the same term
		if ($this->getTerm_id()) {
            $sql = "select id from members where term_id=? and (?<endDate and ?>startDate)";
            $resut = $zend_db->createStatement($sql)->execute([
                $this->getTerm_id(), $this->getStartDate(), $this->getEndDate()
            ]);
            if (count($result) > 0) {
                throw new \Exception('members/overlappingTerms');
            }
		}
	}

	public function save() { parent::save(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()           { return parent::get('id'          ); }
	public function getCommittee_id() { return parent::get('committee_id'); }
	public function getSeat_id()      { return parent::get('seat_id'     ); }
	public function getTerm_id()      { return parent::get('term_id'     ); }
	public function getPerson_id()    { return parent::get('person_id'   ); }
	public function getCommittee()    { return parent::getForeignKeyObject(__namespace__.'\Committee', 'committee_id'); }
	public function getSeat()         { return parent::getForeignKeyObject(__namespace__.'\Seat',      'seat_id'     ); }
	public function getTerm()         { return parent::getForeignKeyObject(__namespace__.'\Term',      'term_id'     ); }
	public function getPerson()       { return parent::getForeignKeyObject(__namespace__.'\Person',    'person_id'   ); }
	public function getStartDate($f=null) { return parent::getDateData('startDate', $f); }
	public function getEndDate  ($f=null) { return parent::getDateData('endDate',   $f); }

	public function setCommittee_id($i) { parent::setForeignKeyField (__namespace__.'\Committee', 'committee_id', $i); }
	public function setSeat_id     ($i) { parent::setForeignKeyField (__namespace__.'\Seat',      'seat_id',      $i); }
	public function setTerm_id     ($i) { parent::setForeignKeyField (__namespace__.'\Term',      'term_id',      $i); }
	public function setPerson_id   ($i) { parent::setForeignKeyField (__namespace__.'\Person',    'person_id',    $i); }
	public function setCommittee   ($o) { parent::setForeignKeyObject(__namespace__.'\Committee', 'committee_id', $o); }
	public function setSeat        ($o) { parent::setForeignKeyObject(__namespace__.'\Seat',      'seat_id',      $o); }
	public function setTerm        ($o) { parent::setForeignKeyObject(__namespace__.'\Term',      'term_id',      $o); }
	public function setPerson      ($o) { parent::setForeignKeyObject(__namespace__.'\Person',    'person_id',    $o); }
	public function setStartDate($d) { parent::setDateData('startDate', $d); }
	public function setEndDate  ($d) { parent::setDateData('endDate',   $d); }

	public function handleUpdate($post)
	{
        $fields = ['committee_id', 'seat_id', 'term_id', 'person_id', 'startDate', 'endDate'];
        foreach ($fields as $f) {
            $set = 'set'.ucfirst($f);
            $this->$set($post[$f]);
        }
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
}