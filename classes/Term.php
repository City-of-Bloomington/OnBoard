<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Term extends ActiveRecord
{
	private $id;
	private $seat_id;
	private $person_id;
	private $term_start;
	private $term_end;

	private $seat;
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
			$query = $PDO->prepare('select * from terms where id=?');
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) {
				throw new Exception('terms/unknownTerm');
			}
			foreach ($result[0] as $field=>$value) {
				if ($value) {
					switch ($field) {
						case 'term_start':
						case 'term_end':
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
			$this->term_start = time();
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->seat_id || !$this->person_id) {
			throw new Exception('missingRequiredFields');
		}

		if (!$this->term_start) {
			$this->term_start = time();
		}

		// Make sure the end date falls after the start date
		if ($this->term_end && $this->term_end < $this->term_start) {
			throw new Exception('terms/invalidEndDate');
		}

		// Make sure this term does not exceed the maxCurrentTerms for the seat
		if ( $this->term_start <= time() && (!$this->term_end || $this->term_end >= time()) ) {
			// The term we're adding is current, make sure there's room
			$count = count($this->getSeat()->getCurrentTerms());
			if (!$this->id) {
				$count++;
			}
			if ($count > $this->getSeat()->getMaxCurrentTerms()) {
				throw new Exception('seats/maxCurrentTermsFilled');
			}
		}

		// Make sure this person is not serving overlapping terms for the same committee
		$pdo = Database::getConnection();
		$sql = "select id from terms
				where id!=?
				and ((term_start<=? and ?>=term_end)
					or (?<=term_end and ?>=term_end))";
		$query = $pdo->prepare($sql);
		$query->execute(array($this->id,
							  $this->term_start,$this->term_end,
							  $this->term_start,$this->term_end));
		$result = $query->fetchAll(PDO::FETCH_ASSOC);
		if (count($result)) {
			throw new Exception('terms/overlappingTerms');
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
		$fields['seat_id'] = $this->seat_id;
		$fields['person_id'] = $this->person_id;
		$fields['term_start'] = date('Y-m-d',$this->term_start);
		$fields['term_end'] = $this->term_end ? date('Y-m-d',$this->term_end) : null;

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

		$sql = "update terms set $preparedFields where id={$this->id}";
		$query = $PDO->prepare($sql);
		$query->execute($values);
	}

	private function insert($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "insert terms set $preparedFields";
		$query = $PDO->prepare($sql);
		$query->execute($values);
		$this->id = $PDO->lastInsertID();
	}

	/**
	 * Wipes all records of this term from the database
	 */
	public function delete()
	{
		if ($this->id) {
			$pdo = Database::getConnection();

			$query = $pdo->prepare('delete from votingRecords where term_id=?');
			$query->execute(array($this->id));

			$query = $pdo->prepare('delete from terms where id=?');
			$query->execute(array($this->id));
		}
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
	public function getSeat_id()
	{
		return $this->seat_id;
	}

	/**
	 * @return int
	 */
	public function getPerson_id()
	{
		return $this->person_id;
	}

	/**
	 * Returns the date/time in the desired format
	 * Format can be specified using either the strftime() or the date() syntax
	 *
	 * @param string $format
	 */
	public function getTerm_start($format=null)
	{
		if ($format && $this->term_start) {
			if (strpos($format,'%')!==false) {
				return strftime($format,$this->term_start);
			}
			else {
				return date($format,$this->term_start);
			}
		}
		else {
			return $this->term_start;
		}
	}

	/**
	 * Returns the date/time in the desired format
	 * Format can be specified using either the strftime() or the date() syntax
	 *
	 * @param string $format
	 */
	public function getTerm_end($format=null)
	{
		if ($format && $this->term_end) {
			if (strpos($format,'%')!==false) {
				return strftime($format,$this->term_end);
			}
			else {
				return date($format,$this->term_end);
			}
		}
		else {
			return $this->term_end;
		}
	}

	/**
	 * @return Seat
	 */
	public function getSeat()
	{
		if ($this->seat_id) {
			if (!$this->seat) {
				$this->seat = new Seat($this->seat_id);
			}
			return $this->seat;
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
	public function setSeat_id($int)
	{
		$this->seat = new Seat($int);
		$this->seat_id = $int;
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
	public function setTerm_start($date)
	{
		if (is_array($date)) {
			$this->term_start = $this->dateArrayToTimestamp($date);
		}
		elseif (ctype_digit($date)) {
			$this->term_start = $date;
		}
		else {
			$this->term_start = strtotime($date);
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
	public function setTerm_end($date)
	{
		if (is_array($date)) {
			$this->term_end = $this->dateArrayToTimestamp($date);
		}
		elseif (ctype_digit($date)) {
			$this->term_end = $date;
		}
		else {
			$this->term_end = strtotime($date);
		}
	}

	/**
	 * @param Seat $seat
	 */
	public function setSeat($seat)
	{
		$this->seat_id = $seat->getId();
		$this->seat = $seat;
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
	/**
	 * @return Committee
	 */
	public function getCommittee()
	{
		return $this->getSeat()->getCommittee();
	}

	/**
	 * @return VotingRecordList
	 */
	public function getVotingRecords()
	{
		return new VotingRecordList(array('term_id'=>$this->id));
	}

	/**
	 * @return boolean
	 */
	public function isSafeToDelete()
	{
		return (count($this->getVotingRecords()) == 0) ? true : false;
	}
}
