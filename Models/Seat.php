<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Application\Models\TermTable;
use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Seat extends ActiveRecord
{
    public static $types = ['termed', 'open'];

	protected $tablename = 'seats';

	protected $committee;
	protected $appointer;

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
				$sql = 'select * from seats where id=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if ($result) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('seats/unknownSeat');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setAppointer_id   (1);
			$this->setStartDate(date(DATE_FORMAT));
		}
	}

	/**
	 * We are setting the default Appoint at construct time,
	 * however, the TableGateway contructs first, then calls exchangeArray().
	 * This means there will be a mismatch in the protected $appointer property,
	 * which is intended to be lazy-loaded from $data
	 * We need to clear out that property when loading an array of data
	 */
	public function exchangeArray($data)
	{
		$this->appointer = null; parent::exchangeArray($data);
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
        if (!$this->getType()) { $this->setType('termed'); }

		if (!$this->getName())         { throw new \Exception('missingName'); }
		if (!$this->getCommittee_id()) { throw new \Exception('seats/missingCommittee'); }

		// Make sure the end date falls after the start date
		$start = (int)$this->getStartDate('U');
		$end   = (int)$this->getEndDate  ('U');
		if ($end && $end < $start) { throw new \Exception('invalidEndDate'); }
	}

	public function save() { parent::save(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()           { return parent::get('id');   }
	public function getType()         { return parent::get('type'); }
	public function getName()         { return parent::get('name'); }
	public function getRequirements() { return parent::get('requirements'); }
	public function getCommittee_id() { return parent::get('committee_id'); }
	public function getAppointer_id() { return parent::get('appointer_id'); }
	public function getTermLength()   { return parent::get('termLength'); }
	public function getCommittee()    { return parent::getForeignKeyObject(__namespace__.'\Committee', 'committee_id'); }
	public function getAppointer()    { return parent::getForeignKeyObject(__namespace__.'\Appointer', 'appointer_id'); }
	public function getStartDate($f=null) { return parent::getDateData('startDate', $f); }
	public function getEndDate  ($f=null) { return parent::getDateData('endDate',   $f); }

	public function setType        ($s) { parent::set('type', $s === 'termed' ? 'termed': 'open'); }
	public function setName        ($s) { parent::set('name', $s); }
	public function setRequirements($s) { parent::set('requirements', $s); }
	public function setTermLength  ($s) { parent::set('termLength', $s); }
	public function setCommittee_id($i) { parent::setForeignKeyField (__namespace__.'\Committee', 'committee_id', $i); }
	public function setAppointer_id($i) { parent::setForeignKeyField (__namespace__.'\Appointer', 'appointer_id', $i); }
	public function setCommittee($o)    { parent::setForeignKeyObject(__namespace__.'\Committee', 'committee_id', $o); }
	public function setAppointer($o)    { parent::setForeignKeyObject(__namespace__.'\Appointer', 'appointer_id', $o); }
	public function setStartDate($d) { parent::setDateData('startDate', $d); }
	public function setEndDate  ($d) { parent::setDateData('endDate',   $d); }

	public function handleUpdate($post)
	{
		$fields = ['name', 'appointer_id', 'startDate', 'endDate', 'requirements', 'type', 'termLength'];
		foreach ($fields as $f) {
			$set = 'set'.ucfirst($f);
			$this->$set($post[$f]);
		}
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function __toString() { return parent::get('name'); }

	public function getMembers()
	{
		$table = new MemberTable();
		return $table->find(['seat_id'=>$this->getId()]);
	}

	public function getMember($timestamp=null)
	{
        if (!$timestamp) { $timestamp = time(); }

        $table = new MemberTable();
        $list = $table->find(['seat_id'=>$this->getId(), 'current'=>$timestamp]);
        if (count($list)) {
            return $list->current();
        }
	}

	/**
	 * @return Member
	 */
	public function newMember()
	{
        if ($this->getType() === 'open') {
            $member = new Member();
            $member->setSeat($this);
            $member->setCommittee_id($this->getCommittee_id());
            return $member;
        }

        throw new \Exception('seats/invalidMember');
	}

	/**
	 * @return Zend\Db\ResultSet
	 */
	public function getTerms()
	{
		$search = ['seat_id' => $this->getId()];

		$table = new TermTable();
		return $table->find($search);
	}

	/**
	 * Returns the term for a seat at a given point in time
	 *
	 * If no timestamp is provided, returns the current term
	 *
	 * @param int $timestamp
	 */
	public function getTerm($timestamp=null)
	{
        if (!$timestamp) { $timestamp = time(); }

        $table = new TermTable();
        $list = $table->find(['seat_id'=>$this->getId(), 'current'=>$timestamp]);
        if (count($list)) {
            return $list->current();
        }
	}


	/**
	 * Checks whether it is safe to delete a seat.
	 *
     * Seats on 'seated' committees can only be deleted if there are no terms
     * associated with the seat.
     *
     * Open committees do not need seats.
     * When you change a committee's type from seated to open, the seats
     * will still be there.  For 'open' committees we allow users to delete the
     * unneeded seats.  The system will preserve the terms.  We simply remove the
     * seat_id from all the terms before deleting the seat.
     *
	 * @return bool
	 */
	public function canBeDeleted()
	{
        $committee = $this->getCommittee();

        if ($committee->getType() === 'seated') {
            // Seats on seated committees can only be deleted if there are no terms
            $zend_db = Database::getConnection();
            $sql = 'select count(*) as count from terms where seat_id=?';
            $result = $zend_db->query($sql, [$this->getId()]);
            $row = $result->current();
            return $row['count'] === 0;
        }
        else {
            // Terms for open committees do not need seats.
            return true;
        }
	}

	/**
	 * @return boolean
	 */
	public function hasVacancy($timestamp=null)
	{
        if (!$timestamp) { $timestamp = time(); }

        return count($this->getMembers($timestamp)) ? true : false;
	}
}
