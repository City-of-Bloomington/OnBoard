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

	protected $allocation;

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
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
        if (!$this->getType()) { $this->setType('termed'); }

		if (!$this->getName())          { throw new \Exception('missingName'); }
		if (!$this->getAllocation_id()) { throw new \Exception('seats/missingAllocation'); }

		// Make sure the end date falls after the start date
		$start = (int)$this->getTerm_start('U');
		$end   = (int)$this->getTerm_end  ('U');
		if ($end && $end < $start) { throw new \Exception('invalidEndDate'); }
	}

	public function save() { parent::save(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()            { return parent::get('id');   }
	public function getType()          { return parent::get('type'); }
	public function getName()          { return parent::get('name'); }
	public function getAllocation_id() { return parent::get('allocation_id'); }
	public function getAllocation()    { return parent::getForeignKeyObject(__namespace__.'\Allocation', 'allocation_id'); }
	public function getStartDate($f=null) { return parent::getDateData('startDate', $f); }
	public function getEndDate  ($f=null) { return parent::getDateData('endDate',   $f); }

	public function setType($s) { parent::set('type', $s === 'termed' ? 'termed': 'open'); }
	public function setName($s)          { parent::set('name', $s); }
	public function setAllocation_id($i) { parent::setForeignKeyField (__namespace__.'\Allocation', 'allocation_id', $i); }
	public function setAllocation   ($o) { parent::setForeignKeyObject(__namespace__.'\Allocation', 'allocation_id', $o); }
	public function setStartDate($d) { parent::setDateData('startDate', $d); }
	public function setEndDate  ($d) { parent::setDateData('endDate',   $d); }

	public function handleUpdate($post)
	{
		$fields = ['name', 'allocation_id', 'startDate', 'endDate'];
		foreach ($fields as $f) {
			$set = 'set'.ucfirst($f);
			$this->$set($post[$f]);
		}

		if (Person::isAllowed('seats', 'changeType') && isset($post['type'])) {
            $this->setType($post['type']);
		}
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function __toString() { return parent::get('name'); }

	/**
	 * @param array $fields Extra fields to search on
	 * @return Zend\Db\ResultSet
	 */
	public function getTerms(array $fields=null)
	{
		$search = ['seat_id' => $this->getId()];
		if ($fields) {
			$search = array_merge($search, $fields);
		}

		$table = new TermTable();
		return $table->find($search);
	}

	/**
	 * @return Zend\Db\ResultSet
	 */
	public function getCurrentTerms()
	{
		return $this->getTerms(['current'=>time()]);
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
}
