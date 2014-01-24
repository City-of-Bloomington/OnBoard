<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Application\Models\TermTable;
use Application\Models\RequirementTable;
use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Seat extends ActiveRecord
{
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
			$this->setMaxCurrentTerms(1);
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		if (!$this->getName())         { throw new \Exception('missingName'); }
		if (!$this->getCommittee_id()) { throw new \Exception('seats/missingCommittee'); }
	}

	public function save() { parent::save(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()              { return parent::get('id');   }
	public function getName()            { return parent::get('name'); }
	public function getMaxCurrentTerms() { return parent::get('maxCurrentTerms'); }
	public function getCommittee_id()    { return parent::get('committee_id'); }
	public function getAppointer_id()    { return parent::get('appointer_id'); }
	public function getCommittee()       { return parent::getForeignKeyObject(__namespace__.'\Committee', 'committee_id'); }
	public function getAppointer()       { return parent::getForeignKeyObject(__namespace__.'\Appointer', 'appointer_id'); }

	public function setName($s)            { parent::set('name', $s); }
	public function setMaxCurrentTerms($i) { parent::set('maxCurrentTerms', (int)$i); }
	public function setCommittee_id($i)    { parent::setForeignKeyField (__namespace__.'\Committee', 'committee_id', $i); }
	public function setAppointer_id($i)    { parent::setForeignKeyField (__namespace__.'\Appointer', 'appointer_id', $i); }
	public function setCommittee($o)       { parent::setForeignKeyObject(__namespace__.'\Committee', 'committee_id', $o); }
	public function setAppointer($o)       { parent::setForeignKeyObject(__namespace__.'\Appointer', 'appointer_id', $o); }

	public function handleUpdate($post)
	{
		$this->setName($post['name']);
		$this->setAppointer_id($post['appointer_id']);
		$this->setMaxCurrentTerms($post['maxCurrentTerms']);
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function __toString() { return parent::get('name'); }

	/**
	* @return string
	*/
	public function getUrl() { return "{$this->getCommittee()->getUrl()};tab=seats;seat_id={$this->getId()}"; }
	public function getUri() { return "{$this->getCommittee()->getUri()};tab=seats;seat_id={$this->getId()}"; }

	/**
	 * @return Zend\Db\ResultSet
	 */
	public function getTerms()
	{
		$table = new TermTable();
		return $table->find(['seat_id'=>$this->getId()]);
	}

	/**
	 * @return Zend\Db\ResultSet
	 */
	public function getCurrentTerms()
	{
		$table = new TermTable();
		return $table->find(['seat_id'=>$this->getId(), 'current'=>time()]);
	}

	/**
	 * @return Zend\Db\ResultSet
	 */
	public function getRequirements()
	{
		$table = new RequirementTable();
		return $table->find(['seat_id'=>$this->getId()]);
	}

	/**
	 * @return boolean
	 */
	public function hasRequirements()
	{
		return count($this->getRequirements()) ? true : false;
	}

	/**
	 * Check if this seat has a particular requirement
	 *
	 * @param Requirement $requirement
	 * @return boolean
	 */
	public function hasRequirement(Requirement $requirement)
	{
		$zend_db = Database::getConnection();
		$query = $zend_db->createStatement('select requirement_id from seat_requirements where seat_id=? and requirement_id=?');
		$result = $query->execute([$this->getId(),$requirement->getId()]);
		return count($result) ? true : false;
	}

	/**
	 * @param Requirement $requirement
	 */
	public function addRequirement(Requirement $requirement)
	{
		if (!$this->hasRequirement($requirement)) {
			$zend_db = Database::getConnection();

			$query = $zend_db->createStatement('insert seat_requirements set seat_id=?,requirement_id=?');
			$query->execute([$this->getId(),$requirement->getId()]);
		}
	}

	/**
	 * @param Requirement $requirement
	 */
	public function removeRequirement(Requirement $requirement)
	{
		if ($this->getId()) {
			$zend_db = Database::getConnection();

			$query = $zend_db->createStatement('delete from seat_requirements where seat_id=? and requirement_id=?');
			$query->execute([$this->getId(),$requirement->getId()]);
		}
	}
}
