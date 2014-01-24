<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Office extends ActiveRecord
{
	protected $tablename = 'offices';

	protected $committee;
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
				$sql = 'select * from offices where id=?';

				$result = $zend_db->createStatement($sql)->execute([$id]);
				if ($result) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('offices/unknownOffice');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setStartDate(date('Y-m-d'));
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		if (!$this->getCommittee_id() || !$this->getPerson_id()) {
			throw new Exception('missingRequiredFields');
		}

		if (!$this->getTitle()) {
			throw new Exception('offices/missingTitle');
		}

		// Make sure the end date falls after the start date
		$start = $this->getStartDate('U');
		$end   = $this->getEndDate  ('U');
		if ($end && $end < $start) {
			throw new Exception('terms/invalidEndDate');
		}
	}

	public function save() { parent::save(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()           { return parent::get('id');   }
	public function getTitle()        { return parent::get('title'); }
	public function getCommittee_id() { return parent::get('committee_id'); }
	public function getPerson_id()    { return parent::get('person_id'); }
	public function getCommittee()    { return parent::getForeignKeyObject(__namespace__.'\Committee', 'committee_id'); }
	public function getPerson()       { return parent::getForeignKeyObject(__namespace__.'\Person',    'person_id'); }
	public function getStartDate($f)  { return parent::getDateData('startDate', $f); }
	public function getEndDate  ($f)  { return parent::getDateData('endDate',   $f); }

	public function setTitle       ($s) { parent::set('title', $s); }
	public function setCommittee_id($i) { parent::setForeignKeyField (__namespace__.'\Committee', 'committee_id', $i); }
	public function setPerson_id   ($i) { parent::setForeignKeyField (__namespace__.'\Person',    'person_id',    $i); }
	public function setCommittee   ($o) { parent::setForeignKeyObject(__namespace__.'\Committee', 'committee_id', $o); }
	public function setPerson      ($o) { parent::setForeignKeyObject(__namespace__.'\Person',    'person_id',    $o); }
	public function setStartDate   ($d) { parent::setDateData('startDate', $d); }
	public function setEndDate     ($d) { parent::setDateData('endDate',   $d); }

	public function handleUpdate($post)
	{
		// Committee and Person should already be set before we draw the form
		$fields = ['title', 'startDate', 'endDate'];
		foreach ($fields as $f) {
			$set = 'set'.ucfirst($f);
			$this->$set($post[$f]);
		}
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
}
