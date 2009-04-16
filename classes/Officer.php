<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Officer extends ActiveRecord
{
	private $id;
	private $committee_id;
	private $person_id;
	private $title;
	private $startDate;
	private $endDate;

	private $committee;
	private $person;

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
			$query = $PDO->prepare('select * from officers where id=?');
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) {
				throw new Exception('officers/unknownOfficer');
			}
			foreach ($result[0] as $field=>$value) {
				if ($value) {
					switch ($field) {
						case 'startDate':
						case 'endDate':
							if ($value && $value!='0000-00-00') {
								$this->$field = strtotime($value);
							}
							break;
						default:
							$this->$field = $value;
					}
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->startDate = time();
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->committee_id || !$this->person_id) {
			throw new Exception('missingRequiredFields');
		}

		if (!$this->title) {
			throw new Exception('officers/missingTitle');
		}

		// Make sure the end date falls after the start date
		if ($this->endDate && $this->endDate < $this->startDate) {
			throw new Exception('terms/invalidEndDate');
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
		$fields['committee_id'] = $this->committee_id;
		$fields['person_id'] = $this->person_id;
		$fields['title'] = $this->title;
		$fields['startDate'] = date('Y-m-d',$this->startDate);
		$fields['endDate'] = $this->endDate ? date('Y-m-d',$this->endDate) : null;

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

		$sql = "update officers set $preparedFields where id={$this->id}";
		$query = $PDO->prepare($sql);
		$query->execute($values);
	}

	private function insert($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "insert officers set $preparedFields";
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
	 * @return int
	 */
	public function getCommittee_id()
	{
		return $this->committee_id;
	}

	/**
	 * @return int
	 */
	public function getPerson_id()
	{
		return $this->person_id;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Returns the date/time in the desired format
	 * Format can be specified using either the strftime() or the date() syntax
	 *
	 * @param string $format
	 */
	public function getStartDate($format=null)
	{
		if ($format && $this->startDate) {
			if (strpos($format,'%')!==false) {
				return strftime($format,$this->startDate);
			}
			else {
				return date($format,$this->startDate);
			}
		}
		else {
			return $this->startDate;
		}
	}

	/**
	 * Returns the date/time in the desired format
	 * Format can be specified using either the strftime() or the date() syntax
	 *
	 * @param string $format
	 */
	public function getEndDate($format=null)
	{
		if ($format && $this->endDate) {
			if (strpos($format,'%')!==false) {
				return strftime($format,$this->endDate);
			}
			else {
				return date($format,$this->endDate);
			}
		}
		else {
			return $this->endDate;
		}
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
	 * @return Person
	 */
	public function getPerson()
	{
		if ($this->person_id) {
			if (!$this->person) {
				$this->person = new Person($this->person_id);
			}
			return $this->person;
		}
		return null;
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------

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
	public function setPerson_id($int)
	{
		$this->person = new Person($int);
		$this->person_id = $int;
	}

	/**
	 * @param string $string
	 */
	public function setTitle($string)
	{
		$this->title = trim($string);
	}

	/**
	 * Sets the date
	 *
	 * Dates and times should be stored as timestamps internally.
	 * This accepts dates and times in multiple formats and sets the internal timestamp
	 * Accepted formats are:
	 * 		array - in the form of PHP getdate()
	 *		timestamp
	 *		string - anything strtotime understands
	 * @param date $date
	 */
	public function setStartDate($date)
	{
		if (is_array($date)) {
			$this->startDate = $this->dateArrayToTimestamp($date);
		}
		elseif (ctype_digit($date)) {
			$this->startDate = $date;
		}
		else {
			$this->startDate = strtotime($date);
		}
	}

	/**
	 * Sets the date
	 *
	 * Dates and times should be stored as timestamps internally.
	 * This accepts dates and times in multiple formats and sets the internal timestamp
	 * Accepted formats are:
	 * 		array - in the form of PHP getdate()
	 *		timestamp
	 *		string - anything strtotime understands
	 * @param date $date
	 */
	public function setEndDate($date)
	{
		if (is_array($date)) {
			$this->endDate = $this->dateArrayToTimestamp($date);
		}
		elseif (ctype_digit($date)) {
			$this->endDate = $date;
		}
		else {
			$this->endDate = strtotime($date);
		}
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
	 * @param Person $person
	 */
	public function setPerson($person)
	{
		$this->person_id = $person->getId();
		$this->person = $person;
	}


	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
}
