<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Committee extends ActiveRecord
{
	private $id;
	private $name;
	private $statutoryName;
	private $statuteReference;
	private $dateFormed;
	private $website;
	private $description;

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
			$query = $PDO->prepare('select * from committees where id=?');
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) {
				throw new Exception('committees/unknownCommittee');
			}
			foreach ($result[0] as $field=>$value) {
				if ($value) {
					switch ($field) {
						case 'dateFormed':
							if ($value && $value!='0000-00-00') {
								$this->dateFormed = strtotime($value);
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
		$fields['statutoryName'] = $this->statutoryName ? $this->statutoryName : null;
		$fields['statuteReference'] = $this->statuteReference ? $this->statuteReference : null;
		$fields['dateFormed'] = $this->dateFormed ? date('Y-m-d',$this->dateFormed) : null;
		$fields['website'] = $this->website ? $this->website : null;
		$fields['description'] = $this->description ? $this->description : null;

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

		$sql = "update committees set $preparedFields where id={$this->id}";
		$query = $PDO->prepare($sql);
		$query->execute($values);
	}

	private function insert($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "insert committees set $preparedFields";
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
	 * @return string
	 */
	public function getStatutoryName()
	{
		return $this->statutoryName;
	}

	/**
	 * @return string
	 */
	public function getStatuteReference()
	{
		return $this->statuteReference;
	}

	/**
	 * Returns the date/time in the desired format
	 * Format can be specified using either the strftime() or the date() syntax
	 *
	 * @param string $format
	 */
	public function getDateFormed($format=null)
	{
		if ($format && $this->dateFormed) {
			if (strpos($format,'%')!==false) {
				return strftime($format,$this->dateFormed);
			}
			else {
				return date($format,$this->dateFormed);
			}
		}
		return $this->dateFormed;
	}

	/**
	 * @return string
	 */
	public function getWebsite()
	{
		return $this->website;
	}

	/**
	 * @return text
	 */
	public function getDescription()
	{
		return $this->description;
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
	 * @param string $string
	 */
	public function setStatutoryName($string)
	{
		$this->statutoryName = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setStatuteReference($string)
	{
		$this->statuteReference = trim($string);
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
	public function setDateFormed($date)
	{
		if (is_array($date)) {
			$this->dateFormed = $this->dateArrayToTimestamp($date);
		}
		elseif (ctype_digit($date)) {
			$this->dateFormed = $date;
		}
		else {
			$this->dateFormed = strtotime($date);
		}
	}

	/**
	 * @param string $string
	 */
	public function setWebsite($string)
	{
		$this->website = trim($string);
	}

	/**
	 * @param text $text
	 */
	public function setDescription($text)
	{
		$this->description = $text;
	}


	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * @return SeatList
	 */
	public function getSeats()
	{
		return new SeatList(array('committee_id'=>$this->id));
	}

	/**
	 * Each seat can have multiple concurrent terms
	 * @return int
	 */
	public function getMaxCurrentTerms()
	{
		$positions = 0;
		foreach ($this->getSeats() as $seat) {
			$positions += $seat->getMaxCurrentTerms();
		}
		return $positions;
	}

	/**
	 * @param array $fields Extra fields to search on
	 * @param string $sort Optional sorting
	 * @return TopicList
	 */
	public function getTopics(array $fields=null,$sort=null)
	{
		$search = array('committee_id'=>$this->id);
		if ($fields) {
			$search = array_merge($search,$fields);
		}

		if ($sort) {
			$topics = new TopicList();
			$topics->find($search,$sort);
		}
		else {
			$topics = new TopicList($search);
		}

		return $topics;
	}

	/**
	 * @return boolean
	 */
	public function hasTopics()
	{
		return count($this->getTopics()) ? true : false;
	}

	/**
	 * @return URL
	 */
	public function getURL()
	{
		return new URL(BASE_URL.'/committees/viewCommittee.php?committee_id='.$this->id);
	}

	/**
	 * Returns terms that were current for the given timestamp.
	 * If no timestamp is given, the current time is used.
	 *
	 * @param timestamp $timestamp The timestamp for when the terms would have been current
	 * @return TermList
	 */
	public function getCurrentTerms($timestamp=null)
	{
		if (!$timestamp) {
			$timestamp = time();
		}
		return new TermList(array('committee_id'=>$this->id,'current'=>$timestamp));
	}

	/**
	 * Returns all the terms for this committee
	 * @return TermList
	 */
	public function getTerms()
	{
		return new TermList(array('committee_id'=>$this->id));
	}

	/**
	 * Returns all the people who have served on this committee
	 * @return PeopleList
	 */
	public function getPeople()
	{
		return new PersonList(array('committee_id'=>$this->id));
	}
}
