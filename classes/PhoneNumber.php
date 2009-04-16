<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class PhoneNumber extends ActiveRecord
{
	private $id;
	private $person_id;
	private $ordering;
	private $type;
	private $number;
	private $private;

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
			$query = $PDO->prepare('select * from phoneNumbers where id=?');
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) {
				throw new Exception('phoneNumbers/unknownPhoneNumber');
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
			$this->private = 0;
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->person_id) {
			throw new Exception('phoneNumbers/missingPerson');
		}

		if (!$this->number) {
			throw new Exception('phoneNumbers/missingNumber');
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
		$fields['person_id'] = $this->person_id;
		$fields['ordering'] = $this->ordering ? $this->ordering : null;
		$fields['type'] = $this->type ? $this->type : null;
		$fields['number'] = $this->number;
		$fields['private'] = $this->private ? $this->private : null;

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

		$sql = "update phoneNumbers set $preparedFields where id={$this->id}";
		$query = $PDO->prepare($sql);
		$query->execute($values);
	}

	private function insert($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "insert phoneNumbers set $preparedFields";
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
	public function getPerson_id()
	{
		return $this->person_id;
	}

	/**
	 * @return int
	 */
	public function getOrdering()
	{
		return $this->ordering;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getNumber()
	{
		if (!$this->isPrivate() || userHasRole(array('Administrator','Clerk'))) {
			return $this->number;
		}
	}

	/**
	 * @return int
	 */
	public function getPrivate()
	{
		return $this->private;
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
	public function setPerson_id($int)
	{
		$this->person = new Person($int);
		$this->person_id = $int;
	}

	/**
	 * @param int $int
	 */
	public function setOrdering($int)
	{
		$this->ordering = preg_replace("/[^0-9]/","",$int);
	}

	/**
	 * @param string $string
	 */
	public function setType($string)
	{
		$this->type = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setNumber($string)
	{
		$this->number = trim($string);
	}

	/**
	 * @param boolean $bool
	 */
	public function setPrivate($bool)
	{
		$this->private = $bool ? 1 : 0;
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
	 * @return string
	 */
	public function __toString()
	{
		return $this->getNumber();
	}

	/**
	 * @return boolean
	 */
	public function isPrivate()
	{
		return $this->getPrivate() ? true : false;
	}
}
