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

	protected $seat;

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
        if (!$this->getSeat_id()) {
            throw new \Exception('terms/missingSeat');
        }

		if (!$this->getStartDate()) {
			 $this->setStartDate(date(DATE_FORMAT));
		}

		if (!$this->getEndDate()) { throw new \Exception('missingDate'); }

		// Make sure the end date falls after the start date
		$start = (int)$this->getStartDate('U');
		$end   = (int)$this->getEndDate  ('U');
		if ($end && $end < $start) { throw new \Exception('invalidEndDate'); }

		// Make sure this term is not overlapping terms for the seat
		$zend_db = Database::getConnection();
		$sql = "select id from terms
                where seat_id=?
                and (?<endDate and ?>startDate)";
		if ($this->getId()) { $sql.= ' and id!='.$this->getId(); }

		$result = $zend_db->createStatement($sql)->execute([
            $this->getSeat_id(),
			$this->getStartDate(), $this->getEndDate()
		]);
		if (count($result) > 0) {
			throw new \Exception('overlappingTerms');
		}
	}

	public function save() { parent::save(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()           { return parent::get('id'          ); }
	public function getSeat_id()      { return parent::get('seat_id'     ); }
	public function getSeat()         { return parent::getForeignKeyObject(__namespace__.'\Seat', 'seat_id'); }
	public function getStartDate($f=null) { return parent::getDateData('startDate', $f); }
	public function getEndDate  ($f=null) { return parent::getDateData('endDate',   $f); }

	public function setSeat_id     ($i) { parent::setForeignKeyField (__namespace__.'\Seat', 'seat_id', $i); }
	public function setSeat        ($o) { parent::setForeignKeyObject(__namespace__.'\Seat', 'seat_id', $o); }
	public function setStartDate($d) { parent::setDateData('startDate', $d); }
	public function setEndDate  ($d) { parent::setDateData('endDate',   $d); }

	public function handleUpdate($post)
	{
        $fields = ['seat_id', 'startDate', 'endDate'];
        foreach ($fields as $f) {
            $set = 'set'.ucfirst($f);
            $this->$set($post[$f]);
        }
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
}