<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Seat extends ActiveRecord
{
	private $id;
	private $name;
	private $committee_id;
	private $appointer_id;
	private $maxCurrentTerms;

	private $committee;
	private $appointer;

	/**
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			$PDO = Database::getConnection();
			$query = $PDO->prepare('select * from seats where id=?');
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) {
				throw new Exception('seats/unknownSeat');
			}
			foreach ($result[0] as $field=>$value) {
				if ($value) {
					$this->$field = $value;
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->appointer_id = 1;
			$this->maxCurrentTerms = 1;
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->name) {
			throw new Exception('missingName');
		}
		if (!$this->committee_id) {
			throw new Exception('seats/missingCommittee');
		}
	}

	/**
	 * Saves this record back to the database
	 *
	 * This generates generic SQL that should work right away.
	 * You can replace this $fields code with your own custom SQL
	 * for each property of this class,
	 */
	public function save()
	{
		$this->validate();

		$fields = array();
		$fields['name'] = $this->name;
		$fields['committee_id'] = $this->committee_id;
		$fields['appointer_id'] = $this->appointer_id;
		$fields['maxCurrentTerms'] = $this->maxCurrentTerms;

		// Split the fields up into a preparedFields array and a values array.
		// PDO->execute cannot take an associative array for values, so we have
		// to strip out the keys from $fields
		$preparedFields = array();
		foreach ($fields as $key=>$value) {
			$preparedFields[] = "$key=?";
			$values[] = $value;
		}
		$preparedFields = implode(",",$preparedFields);


		if ($this->id) {
			$this->update($values,$preparedFields);
		}
		else {
			$this->insert($values,$preparedFields);
		}
	}

	private function update($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "update seats set $preparedFields where id={$this->id}";
		$query = $PDO->prepare($sql);
		$query->execute($values);
	}

	private function insert($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "insert seats set $preparedFields";
		$query = $PDO->prepare($sql);
		$query->execute($values);
		$this->id = $PDO->lastInsertID();
	}

	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getCommittee_id()
	{
		return $this->committee_id;
	}

	/**
	 * @return int
	 */
	public function getAppointer_id()
	{
		return $this->appointer_id;
	}

	/**
	 * @return int
	 */
	public function getMaxCurrentTerms()
	{
		return $this->maxCurrentTerms;
	}

	/**
	 * @return Committee
	 */
	public function getCommittee()
	{
		if ($this->committee_id) {
			if (!$this->committee) {
				$this->committee = new Committee($this->committee_id);
			}
			return $this->committee;
		}
		return null;
	}

	/**
	 * @return Appointer
	 */
	public function getAppointer()
	{
		if ($this->appointer_id) {
			if (!$this->appointer) {
				$this->appointer = new Appointer($this->appointer_id);
			}
			return $this->appointer;
		}
		return null;
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------

	/**
	 * @param string $string
	 */
	public function setName($string)
	{
		$this->name = trim($string);
	}

	/**
	 * @param int $int
	 */
	public function setCommittee_id($int)
	{
		$this->committee = new Committee($int);
		$this->committee_id = $int;
	}

	/**
	 * @param int $int
	 */
	public function setAppointer_id($int)
	{
		$this->appointer = new Appointer($int);
		$this->appointer_id = $int;
	}

	/**
	 * @param int $int
	 */
	public function setMaxCurrentTerms($int)
	{
		$this->maxCurrentTerms = preg_replace("/[^0-9]/","",$int);
	}

	/**
	 * @param Committee $committee
	 */
	public function setCommittee($committee)
	{
		$this->committee_id = $committee->getId();
		$this->committee = $committee;
	}

	/**
	 * @param Appointer $appointer
	 */
	public function setAppointer($appointer)
	{
		$this->appointer_id = $appointer->getId();
		$this->appointer = $appointer;
	}


	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getURL()
	{
		return BASE_URL.'/seats/viewSeat.php?seat_id='.$this->id;
	}
	/**
	 * @return TermList
	 */
	public function getTerms()
	{
		return new TermList(array('seat_id'=>$this->id));
	}

	/**
	 * Returns the list of terms active at the given time
	 * @param int $timestamp A unix timestamp
	 * @return TermList
	 */
	public function getCurrentTerms(int $timestamp=null)
	{
		if (!$timestamp) {
			$timestamp = time();
		}
		return new TermList(array('seat_id'=>$this->id,'current'=>$timestamp));
	}
	/**
	 * @return RequirementsList
	 */
	public function getRequirements()
	{
		return new RequirementList(array('seat_id'=>$this->id));
	}
	/**
	 * @return boolean
	 */
	public function hasRequirements()
	{
		return count($this->getRequirements()) ? true : false;
	}
	/**
	 * @param Requirement $requirement
	 * @return boolean
	 */
	public function hasRequirement($requirement)
	{
		$pdo = Database::getConnection();
		$query = $pdo->prepare('select requirement_id from seat_requirements where seat_id=? and requirement_id=?');
		$query->execute(array($this->id,$requirement->getId()));
		$result = $query->fetchAll();
		return count($result) ? true : false;
	}
	/**
	 * @param Requirement $requirement
	 */
	public function addRequirement($requirement)
	{
		if (!$this->hasRequirement($requirement)) {
			$pdo = Database::getConnection();

			$query = $pdo->prepare('insert seat_requirements set seat_id=?,requirement_id=?');
			$query->execute(array($this->id,$requirement->getId()));
		}
	}
	/**
	 * @param Requirement $requirement
	 */
	public function removeRequirement($requirement)
	{
		if ($this->id) {
			$pdo = Database::getConnection();

			$query = $pdo->prepare('delete from seat_requirements where seat_id=? and requirement_id=?');
			$query->execute(array($this->id,$requirement->getId()));
		}
	}
}
