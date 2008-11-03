<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
class Topic extends ActiveRecord
{
	private $id;
	private $topicType_id;
	private $date;
	private $number;
	private $description;
	private $synopsis;
	private $committee_id;

	private $topicType;
	private $committee;

	/**
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 */
	public function __construct($id=null)
	{
		if ($id)
		{
			$PDO = Database::getConnection();
			$sql = "select id,topicType_id,number,description,synopsis,committee_id,
					unix_timestamp(date) as date from topics where id=?";
			$query = $PDO->prepare($sql);
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) { throw new Exception('topics/unknownTopic'); }
			foreach($result[0] as $field=>$value) { if ($value) $this->$field = $value; }
		}
		else
		{
			# This is where the code goes to generate a new, empty instance.
			# Set any default values for properties that need it here
			$this->date = time();
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		# Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->topicType_id || !$this->date || !$this->number ||
			!$this->description || !$this->synopsis || !$this->committee_id)
		{
			throw new Exception('missingRequiredFields');
		}
	}

	/**
	 * This generates generic SQL that should work right away.
	 * You can replace this $fields code with your own custom SQL
	 * for each property of this class,
	 */
	public function save()
	{
		$this->validate();

		$fields = array();
		$fields['topicType_id'] = $this->topicType_id;
		$fields['date'] = date('Y-m-d',$this->date);
		$fields['number'] = $this->number;
		$fields['description'] = $this->description;
		$fields['synopsis'] = $this->synopsis;
		$fields['committee_id'] = $this->committee_id;

		# Split the fields up into a preparedFields array and a values array.
		# PDO->execute cannot take an associative array for values, so we have
		# to strip out the keys from $fields
		$preparedFields = array();
		foreach($fields as $key=>$value)
		{
			$preparedFields[] = "$key=?";
			$values[] = $value;
		}
		$preparedFields = implode(",",$preparedFields);


		if ($this->id) { $this->update($values,$preparedFields); }
		else { $this->insert($values,$preparedFields); }
	}

	private function update($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "update topics set $preparedFields where id={$this->id}";
		$query = $PDO->prepare($sql);
		$query->execute($values);
	}

	private function insert($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "insert topics set $preparedFields";
		$query = $PDO->prepare($sql);
		$query->execute($values);
		$this->id = $PDO->lastInsertID();
	}

	#----------------------------------------------------------------
	# Generic Getters
	#----------------------------------------------------------------
	public function getId() { return $this->id; }
	public function getTopicType_id() { return $this->topicType_id; }
	public function getNumber() { return $this->number; }
	public function getDescription() { return $this->description; }
	public function getSynopsis() { return $this->synopsis; }
	public function getCommittee_id() { return $this->committee_id; }

	public function getDate($format=null)
	{
		if ($format && $this->date)
		{
			if (strpos($format,'%')!==false) { return strftime($format,$this->date); }
			else { return date($format,$this->date); }
		}
		else return $this->date;
	}

	public function getTopicType()
	{
		if ($this->topicType_id)
		{
			if (!$this->topicType) { $this->topicType = new TopicType($this->topicType_id); }
			return $this->topicType;
		}
		else return null;
	}

	public function getCommittee()
	{
		if ($this->committee_id)
		{
			if (!$this->committee) { $this->committee = new Committee($this->committee_id); }
			return $this->committee;
		}
		else return null;
	}

	#----------------------------------------------------------------
	# Generic Setters
	#----------------------------------------------------------------
	public function setTopicType_id($int) { $this->topicType = new TopicType($int); $this->topicType_id = $int; }
	public function setNumber($string) { $this->number = trim($string); }
	public function setDescription($text) { $this->description = $text; }
	public function setSynopsis($text) { $this->synopsis = $text; }
	public function setCommittee_id($int) { $this->committee = new Committee($int); $this->committee_id = $int; }

	public function setTopicType($topicType) { $this->topicType_id = $topicType->getId(); $this->topicType = $topicType; }
	public function setCommittee($committee) { $this->committee_id = $committee->getId(); $this->committee = $committee; }

	public function setDate($date)
	{
		if (is_array($date)) { $this->date = $this->dateArrayToTimestamp($date); }
		elseif(ctype_digit($date)) { $this->date = $date; }
		else {
			// here is handled the mm/dd/yyyy case
			$this->date = strtotime($date);
		}
	}


	#----------------------------------------------------------------
	# Custom Functions
	# We recommend adding all your custom code down here at the bottom
	#----------------------------------------------------------------
	public function __toString(){
		return $this->getTopicType().' '.$this->number.' '.$this->date;
	}

	public function getURL()
	{
		return BASE_URL.'/topics/viewTopic.php?topic_id='.$this->id;
	}

	public function getVotes()
	{
		if ($this->id)
		{
			return new VoteList(array('topic_id'=>$this->id));
		}
		else return array();
	}
	public function hasVotes() { return count($this->getVotes()) ? true : false; }
}
