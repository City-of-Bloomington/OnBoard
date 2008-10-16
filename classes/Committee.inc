<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
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
	 */
	public function __construct($id=null)
	{
		if ($id)
		{
			$PDO = Database::getConnection();
			$query = $PDO->prepare('select * from committees where id=?');
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) { throw new Exception('committees/unknownCommittee'); }
			foreach($result[0] as $field=>$value)
			{
				if ($value)
				{
					if ($field=='dateFormed' && $value!='0000-00-00')
					{
						$this->dateFormed = strtotime($value);
					}
					else { $this->$field = $value; }
				}
			}
		}
		else
		{
			# This is where the code goes to generate a new, empty instance.
			# Set any default values for properties that need it here
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		# Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->name) { throw new Exception('missingName'); }
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
		$fields['name'] = $this->name;
		$fields['statutoryName'] = $this->statutoryName ? $this->statutoryName : null;
		$fields['statuteReference'] = $this->statuteReference ? $this->statuteReference : null;
		$fields['dateFormed'] = $this->dateFormed ? date('Y-m-d',$this->dateFormed) : null;
		$fields['website'] = $this->website ? $this->website : null;
		$fields['description'] = $this->description ? $this->description : null;

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

	#----------------------------------------------------------------
	# Generic Getters
	#----------------------------------------------------------------
	public function getId() { return $this->id; }
	public function getName() { return $this->name; }
	public function getStatutoryName() { return $this->statutoryName; }
	public function getStatuteReference() { return $this->statuteReference; }
	public function getWebsite() { return $this->website; }
	public function getDescription() { return $this->description; }
	public function getDateFormed($format=null)
	{
		if ($format && $this->dateFormed)
		{
			if (strpos($format,'%')!==false) { return strftime($format,$this->dateFormed); }
			else { return date($format,$this->dateFormed); }
		}
		else return $this->dateFormed;
	}

	#----------------------------------------------------------------
	# Generic Setters
	#----------------------------------------------------------------
	public function setName($string) { $this->name = trim($string); }
	public function setStatutoryName($string) { $this->statutoryName = trim($string); }
	public function setStatuteReference($string) { $this->statuteReference = trim($string); }
	public function setWebsite($string) { $this->website = trim($string); }
	public function setDescription($text) { $this->description = $text; }
	public function setDateFormed($date)
	{
		if (is_array($date)) { $this->dateFormed = $this->dateArrayToTimestamp($date); }
		elseif(ctype_digit($date)) { $this->dateFormed = $date; }
		else
		{
			if ($date) { $this->dateFormed = strtotime($date); }
			else { $this->dateFormed = null; }
		}
	}


	#----------------------------------------------------------------
	# Custom Functions
	# We recommend adding all your custom code down here at the bottom
	#----------------------------------------------------------------
	/**
	 * @return SeatList
	 */
	public function getSeats()
	{
		return new SeatList(array('committee_id'=>$this->id));
	}

	/**
	 * @return TopicList
	 */
	public function getTopics()
	{
		return new TopicList(array('committee_id'=>$this->id));
	}

	/**
	 * @return string
	 */
	public function getURL()
	{
		return BASE_URL.'/committees/viewCommittee.php?committee_id='.$this->id;
	}

	/**
	 * @return MemberList
	 */
	public function getCurrentMembers()
	{
		return new MemberList(array('committee_id'=>$this->id,'status'=>'current'));
	}
}
