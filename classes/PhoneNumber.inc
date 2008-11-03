<?php
/**
 * @copyright Copyright (C) 2007-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class PhoneNumber extends ActiveRecord
{
	private $id;
	private $user_id;
	private $ordering;
	private $type;
	private $number;
	private $private;

	private $user;

	/**
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 */
	public function __construct($id=null)
	{
		if ($id)
		{
			$PDO = Database::getConnection();
			$query = $PDO->prepare('select * from phoneNumbers where id=?');
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) { throw new Exception('phoneNumbers/unknownPhoneNumber'); }
			foreach($result[0] as $field=>$value) { if ($value) $this->$field = $value; }
		}
		else
		{
			# This is where the code goes to generate a new, empty instance.
			# Set any default values for properties that need it here
			$this->private = 0;
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		# Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->user_id) { throw new Exception('phoneNumbers/missingUser'); }
		if (!$this->number) { throw new Exception('phoneNumbers/missingNumber'); }
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
		$fields['user_id'] = $this->user_id;
		if ($this->ordering) { $fields['ordering'] = $this->ordering; }
		if ($this->type) { $fields['type'] = $this->type; }
		$fields['number'] = $this->number;
		$fields['private'] = $this->private;

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

	#----------------------------------------------------------------
	# Generic Getters
	#----------------------------------------------------------------
	public function getId() { return $this->id; }
	public function getUser_id() { return $this->user_id; }
	public function getOrdering() { return $this->ordering; }
	public function getType() { return $this->type; }
	public function getNumber()
	{
		if (!$this->isPrivate() || userHasRole(array('Administrator','Clerk')))
		{
			return $this->number;
		}
	}
	public function getPrivate() { return $this->private; }
	public function getUser()
	{
		if ($this->user_id)
		{
			if (!$this->user) { $this->user = new User($this->user_id); }
			return $this->user;
		}
		else return null;
	}

	#----------------------------------------------------------------
	# Generic Setters
	#----------------------------------------------------------------
	public function setUser_id($int) { $this->user = new User($int); $this->user_id = $int; }
	public function setOrdering($int) { $this->ordering = ereg_replace("[^0-9]","",$int); }
	public function setType($string) { $this->type = trim($string); }
	public function setNumber($string) { $this->number = trim($string); }
	public function setPrivate($boolean) { $this->private = $boolean ? 1 : 0; }
	public function setUser($user) { $this->user_id = $user->getId(); $this->user = $user; }


	#----------------------------------------------------------------
	# Custom Functions
	# We recommend adding all your custom code down here at the bottom
	#----------------------------------------------------------------
	public function __toString() { return $this->getNumber(); }

	public function isPrivate() { return $this->private ? true : false; }
}
