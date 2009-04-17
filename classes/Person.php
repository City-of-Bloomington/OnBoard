<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Person extends ActiveRecord
{
	private $id;
	private $firstname;
	private $lastname;
	private $email;
	private $address;
	private $city;
	private $zipcode;
	private $about;
	private $gender;
	private $race_id;
	private $birthdate;

	private $race;
	private $user_id;
	private $user;
	private $phoneNumbers = array();
	private $privateFields = array();

	/**
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (ctype_digit($id)) {
				$sql = 'select * from people where id=?';
			}
			elseif (false !== strpos($id,'@')) {
				$sql = 'select * from people where email=?';
			}
			else {
				$sql = 'select p.* from people p left join users on p.id=person_id where username=?';
			}

			$pdo = Database::getConnection();
			$query = $pdo->prepare($sql);
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) {
				throw new Exception('people/unknownPerson');
			}
			foreach ($result[0] as $field=>$value) {
				if ($value) {
					switch ($field) {
						case 'birthdate':
							if ($value && $value!='0000-00-00') {
								$this->birthdate = strtotime($value);
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
		if (!$this->firstname || !$this->lastname) {
			throw new Exception('missingRequiredFields');
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
		$fields['firstname'] = $this->firstname;
		$fields['lastname'] = $this->lastname;
		$fields['email'] = $this->email ? $this->email : null;
		$fields['address'] = $this->address ? $this->address : null;
		$fields['city'] = $this->city ? $this->city : null;
		$fields['zipcode'] = $this->zipcode ? $this->zipcode : null;
		$fields['about'] = $this->about ? $this->about : null;
		$fields['gender'] = $this->gender ? $this->gender : null;
		$fields['race_id'] = $this->race_id ? $this->race_id : null;
		$fields['birthdate'] = $this->birthdate ? date('Y-m-d',$this->birthdate) : null;

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

		$this->savePrivateFields();
	}

	private function update($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "update people set $preparedFields where id={$this->id}";
		$query = $PDO->prepare($sql);
		$query->execute($values);
	}

	private function insert($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "insert people set $preparedFields";
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
		return $this->isGettable('id') ? $this->id : '';
	}

	/**
	 * @return string
	 */
	public function getFirstname()
	{
		return $this->isGettable('firstname') ? $this->firstname : '';
	}

	/**
	 * @return string
	 */
	public function getLastname()
	{
		return $this->isGettable('lastname') ? $this->lastname : '';
	}

	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->isGettable('email') ? $this->email : '';
	}

	/**
	 * @return string
	 */
	public function getAddress()
	{
		return $this->isGettable('address') ? $this->address : '';
	}

	/**
	 * @return string
	 */
	public function getCity()
	{
		return $this->isGettable('city') ? $this->city : '';
	}

	/**
	 * @return string
	 */
	public function getZipcode()
	{
		return $this->isGettable('zipcode') ? $this->zipcode : '';
	}

	/**
	 * @return text
	 */
	public function getAbout()
	{
		return $this->isGettable('about') ? $this->about : '';
	}

	/**
	 * @return string
	 */
	public function getGender()
	{
		return $this->isGettable('gender') ? $this->gender : '';
	}

	/**
	 * @return int
	 */
	public function getRace_id()
	{
		return $this->isGettable('race_id') ? $this->race_id : '';
	}

	/**
	 * @return Race
	 */
	public function getRace()
	{
		if ($this->isGettable('race_id')) {
			if ($this->race_id) {
				if (!$this->race) {
					$this->race = new Race($this->race_id);
				}
				return $this->race;
			}
		}
		return null;
	}

	/**
	 * Returns the date/time in the desired format
	 * Format can be specified using either the strftime() or the date() syntax
	 *
	 * @param string $format
	 */
	public function getBirthdate($format=null)
	{
		if ($this->isGettable('birthdate')) {
			if ($format && $this->birthdate) {
				if (strpos($format,'%')!==false) {
					return strftime($format,$this->birthdate);
				}
				return date($format,$this->birthdate);
			}
			return $this->birthdate;
		}
		return null;
	}


	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------
	/**
	 * @param string $string
	 */
	public function setFirstname($string)
	{
		$this->firstname = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setLastname($string)
	{
		$this->lastname = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setEmail($string)
	{
		$this->email = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setAddress($string)
	{
		$this->address = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setCity($string)
	{
		$this->city = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setZipcode($string)
	{
		$this->zipcode = trim($string);
	}

	/**
	 * @param text $text
	 */
	public function setAbout($text)
	{
		$this->about = $text;
	}

	/**
	 * @param string $string
	 */
	public function setGender($string)
	{
		$this->gender = trim($string);
	}

	/**
	 * @param int $int
	 */
	public function setRace_id($int)
	{
		$this->race = new Race($int);
		$this->race_id = $int;
	}

	/**
	 * @param Race $race
	 */
	public function setRace($race)
	{
		$this->race_id = $race->getId();
		$this->race = $race;
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
	public function setBirthdate($date)
	{
		if (is_array($date)) {
			$this->birthdate = $this->dateArrayToTimestamp($date);
		}
		elseif (ctype_digit($date)) {
			$this->birthdate = $date;
		}
		else {
			$this->birthdate = strtotime($date);
		}
	}
	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getFullname()
	{
		return "{$this->getFirstname()} {$this->getLastname()}";
	}
	/**
	 * @return string
	 */
	public function getURL()
	{
		return BASE_URL.'/people/viewPerson.php?person_id='.$this->id;
	}

	/**
	 * @return int
	 */
	public function getUser_id()
	{
		if (!$this->user_id) {
			$pdo = Database::getConnection();
			$query = $pdo->prepare('select id from users where person_id=?');
			$query->execute(array($this->id));
			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (count($result)) {
				$this->user_id = $result[0]['id'];
			}
		}
		return $this->user_id;
	}

	/**
	 * @return User
	 */
	public function getUser() {
		if (!$this->user) {
			if ($this->getUser_id()) {
				$this->user = new User($this->getUser_id());
			}
		}
		return $this->user;
	}

	/**
	 * @return string
	 */
	public function getUsername() {
		if ($this->getUser()) {
			return $this->getUser()->getUsername();
		}
	}

	/**
	 * @return array
	 */
	public function getPrivateFields()
	{
		if (!$this->privateFields) {
			$pdo = Database::getConnection();
			$query = $pdo->prepare('select fieldname from people_private_fields where person_id=?');
			$query->execute(array($this->id));
			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			foreach ($result as $row) {
				$this->privateFields[] = $row['fieldname'];
			}
		}
		return $this->privateFields;
	}

	/**
	 * Takes an array of the user's fields that you want to make private
	 * @param array $fields
	 */
	public function setPrivateFields($fields=null)
	{
		if ($fields) {
			$this->privateFields = $fields;
		}
		else {
			$this->privateFields = array();
		}
	}

	private function savePrivateFields()
	{
		$pdo = Database::getConnection();

		$query = $pdo->prepare('delete from people_private_fields where person_id=?');
		$query->execute(array($this->id));

		$query = $pdo->prepare('insert people_private_fields set person_id=?,fieldname=?');
		foreach ($this->privateFields as $field) {
			$query->execute(array($this->id,$field));
		}
	}

	/**
	 * Determines whether a single field is private or not
	 * @param string $field
	 * @return boolean
	 */
	public function isPrivate($field)
	{
		return in_array($field,$this->getPrivateFields());
	}

	/**
	 * @param string $field
	 */
	private function isGettable($field)
	{
		return (userHasRole(array('Administrator','Clerk')) || !$this->isPrivate($field));
	}

	/**
	 * Returns an array of PhoneNumbers
	 * @return array
	 */
	public function getPhoneNumbers()
	{
		if (!count($this->phoneNumbers)) {
			$list = new PhoneNumberList(array('person_id'=>$this->id));
			foreach ($list as $phoneNumber) {
				$this->phoneNumbers[] = $phoneNumber;
			}
		}
		return $this->phoneNumbers;
	}

	/**
	 * Takes a POST array of phoneNumbers and assigns them to this user
	 * $phoneNumbers[i][ordering]
	 * $phoneNumbers[i][number]
	 * $phoneNumbers[i][type]
	 * $phoneNumbers[i][private] (Optional)
	 * @param array $phoneNumbers
	 */
	public function setPhoneNumbers(array $phoneNumbers)
	{
		$this->phoneNumbers = array();
		foreach ($phoneNumbers as $posted) {
			if (trim($posted['number'])) {
				$phoneNumber = new PhoneNumber();
				$phoneNumber->setNumber($posted['number']);
				$phoneNumber->setUser($this);

				if ($posted['ordering']) {
					$phoneNumber->setOrdering($posted['ordering']);
				}
				if ($posted['type']) {
					$phoneNumber->setType($posted['type']);
				}
				if (isset($posted['private'])) {
					$phoneNumber->setPrivate($posted['private']);
				}

				$this->phoneNumbers[] = $phoneNumber;
			}
		}
	}

	private function savePhoneNumbers()
	{
		if ($this->id) {
			$pdo = Database::getConnection();
			$query = $pdo->prepare('delete from phoneNumbers where person_id=?');
			$query->execute(array($this->id));

			foreach ($this->getPhoneNumbers() as $phoneNumber) {
				$phoneNumber->setUser($this);
				$phoneNumber->save();
			}
		}
	}

	/**
	 * @return CommitteeList
	 */
	public function getCommittees()
	{
		return new CommitteeList(array('person_id'=>$this->id));
	}

	/**
	 * @param array $fields Extra fields to search on
	 * @return TermList
	 */
	public function getTerms($fields=null)
	{
		$search = array('person_id'=>$this->id);
		if (is_array($fields)) {
			$search = array_merge($search,$fields);
		}
		return new TermList($search);
	}

	/**
	 * @param Committee
	 * @return boolean
	 */
	public function isCurrentlyServing(Committee $committee)
	{
		return count($this->getTerms(array('committee_id'=>$committee->getId()))) ? true : false;
	}

	/**
	 * @return PeopleList
	 */
	public function getPeers()
	{
		$peers = array();

		$committees = array();
		foreach ($this->getCommittees() as $committee) {
			$committees[] = $committee->getId();
		}
		if (count($committees)) {
			$list = new PersonList(array('committee_id'=>$committees));
			foreach ($list as $person) {
				if ($person->getId() != $this->id) {
					$peers[] = $person;
				}
			}
		}
		return $peers;
	}

	/**
	 * @param array $fields Any extra search fields to use to create the list
	 * @return VotingRecordList
	 */
	public function getVotingRecords($fields=null)
	{
		$search = array('person_id'=>$this->id);
		if (is_array($fields)) {
			$search = array_merge($search,$fields);
		}
		return new VotingRecordList($search);
	}

	/**
	 * @return boolean
	 */
	public function hasVotingRecord()
	{
		return count($this->getVotingRecords()) ? true : false;
	}

	/**
	 * Calculates a person's voting record percentages for each of their
	 * possible positions (yes, no, absent, abstain).
	 * If you pass in a TopicType, it will calculate the percentages only
	 * for votes on topics of the type you pass in.
	 *
	 * @param TopicType|int $topicType The Type of Topics to look at
	 * @return array
	 */
	public function getVotePercentages($topicType=null)
	{
		$search = array('person_id'=>$this->id);
		if ($topicType) {
			if (ctype_digit($topicType)) {
				$topicType = new TopicType($topicType);
			}
			$search['topicType'] = $topicType;
		}
		$votingRecords = new VotingRecordList($search);
		$total = count($votingRecords) ? count($votingRecords) : 1;

		$output = array('yes'=>0,'no'=>0,'abstain'=>0,'absent'=>0);
		foreach (array_keys($output) as $position) {
			$search = array('person_id'=>$this->id,'position'=>$position);
			if ($topicType) {
				$search['topicType'] = $topicType;
			}
			$count = count(new VotingRecordList($search));
			$output[$position] = round(($count * 100 / $total),2);
		}
		return $output;
	}

	/**
	 * Returns the percentage that someone else has voted the same way as this person
	 *
	 * If you pass in a topic list, the comparison will be only for votes on the given topics
	 * Without a topicList, the comparison will be for any votes both people participated in
	 *
	 * @param Person $otherPerson
	 * @param TopicList $topicList (optional)
	 * @return float
	 */
	public function getAccordancePercentage($otherPerson,TopicList $topicList=null)
	{
		return VotingRecordList::findAccordancePercentage($this,$otherPerson,$topicList);
	}

	/**
	 * Returns all the topics this person has voted on
	 * @return TopicList
	 */
	public function getTopics()
	{
		return new TopicList(array('person_id'=>$this->id));
	}

	/**
	 * Returns the offices held for the given committee
	 * If a timestamp is given, it will return only offices held on the date of the timestamp
	 *
	 * @return OfficerList
	 */
	public function getOffices(Committee $committee=null,$timestamp=null)
	{
		$search = array('person_id'=>$this->id);
		if ($committee) {
			$search['committee_id'] = $committee->getId();
		}
		if ($timestamp) {
			$search['current'] = $timestamp;
		}
		return new OfficerList($search);
	}

	/**
	 * Returns all the appointment information for a person.
	 * Optionall provide a committee to limit the appointment information
	 * @param Committee $committee
	 * @return AppointerList
	 */
	public function getAppointers(Committee $committee=null)
	{
		$search = array('person_id'=>$this->id);
		if ($committee) {
			$search['committee_id'] = $committee->getId();
		}
		return new AppointerList($search);
	}
}
