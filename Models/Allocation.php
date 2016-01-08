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

class Allocation extends ActiveRecord
{
	protected $tablename = 'allocations';

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
				$sql = 'select * from allocations where id=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if ($result) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('allocations/unknown');
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
		if (!$this->getName())         { throw new \Exception('missingName'); }
		if (!$this->getCommittee_id()) { throw new \Exception('allocations/missingCommittee'); }
	}

	public function save() { parent::save(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()               { return parent::get('id');   }
	public function getName()             { return parent::get('name'); }
	public function getCommittee_id()     { return parent::get('committee_id'); }
	public function getAppointer_id()     { return parent::get('appointer_id'); }
	public function getCommittee()        { return parent::getForeignKeyObject(__namespace__.'\Committee', 'committee_id'); }
	public function getAppointer()        { return parent::getForeignKeyObject(__namespace__.'\Appointer', 'appointer_id'); }
	public function getStartDate($f=null) { return parent::getDateData('startDate', $f); }
	public function getEndDate  ($f=null) { return parent::getDateData('endDate',   $f); }
	public function getRequirements()     { return parent::get('requirements'); }

	public function setName($s)            { parent::set('name', $s); }
	public function setCommittee_id($i)    { parent::setForeignKeyField (__namespace__.'\Committee', 'committee_id', $i); }
	public function setAppointer_id($i)    { parent::setForeignKeyField (__namespace__.'\Appointer', 'appointer_id', $i); }
	public function setCommittee($o)       { parent::setForeignKeyObject(__namespace__.'\Committee', 'committee_id', $o); }
	public function setAppointer($o)       { parent::setForeignKeyObject(__namespace__.'\Appointer', 'appointer_id', $o); }
	public function setStartDate($d)       { parent::setDateData('startDate', $d); }
	public function setEndDate  ($d)       { parent::setDateData('endDate',   $d); }
	public function setRequirements($s)    { parent::set('requirements', $s); }

	public function handleUpdate($post)
	{
		$fields = ['name', 'appointer_id', 'startDate', 'endDate', 'requirements'];
		foreach ($fields as $f) {
			$set = 'set'.ucfirst($f);
			$this->$set($post[$f]);
		}
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function __toString() { return parent::get('name'); }

	public function getSeats()
	{
        $table = new SeatTable();
        return $table->find(['allocation_id'=>$this->getId()]);
	}
}
