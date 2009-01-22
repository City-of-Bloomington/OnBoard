<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
class User extends SystemUser
{
	private $id;
	private $username;
	private $password;
	private $authenticationMethod;

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
	private $timestamp;

	private $race;
	private $roles = array();
	private $newPassword; # the User's new password, unencrypted
	private $phoneNumbers = array();
	private $privateFields = array();

	public function __construct($id = null)
	{
		if ($id)
		{
			$PDO = Database::getConnection();

			# Load an existing user
			if (ctype_digit($id)) { $sql = 'select * from users where id=?'; }
			else { $sql = 'select * from users where username=?'; }

			$query = $PDO->prepare($sql);
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) { throw new Exception('users/unknownUser'); }
			foreach ($result[0] as $field=>$value)
			{
				if ($value)
				{
					# Birthdates must be stored internally as timestamps
					#
					# MySQL cannot handle timestamps prior to 1970
					# PHP supports negative timestamps, which go back to 1901
					# For early dates, we need to select the dates as strings
					# and convert them to timestamps in PHP
					if ($field=='birthdate' && $value!='0000-00-00')
					{
						$this->birthdate = strtotime($value);
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

	public function validate()
	{
		if (!$this->firstname || !$this->lastname)
		{
			throw new Exception('missingName');
		}
	}

	public function save()
	{
		$this->validate();

		$fields = array();
		$fields['username'] = $this->username ? $this->username : null;
		# Passwords should not be updated by default.  Use the savePassword() function
		if ($this->authenticationMethod) { $fields['authenticationMethod'] = $this->authenticationMethod; }
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
		# Timestamp should be left alone, let the database set it automatically

		# Split the fields up into a preparedFields array and a values array.
		# PDO->execute cannot take an associative array for values, so we have
		# to strip out the keys from $fields
		$preparedFields = array();
		foreach ($fields as $key=>$value)
		{
			$preparedFields[] = "$key=?";
			$values[] = $value;
		}
		$preparedFields = implode(",",$preparedFields);

		# Do the database calls
		if ($this->id) { $this->update($values,$preparedFields); }
		else { $this->insert($values,$preparedFields); }

		# Save the password only if it's changed
		if ($this->passwordHasChanged()) { $this->savePassword(); }

		$this->updateRoles();
		$this->savePhoneNumbers();
		$this->savePrivateFields();
	}

	private function update($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "update users set $preparedFields where id={$this->id}";
		$query = $PDO->prepare($sql);
		$query->execute($values);
	}

	private function insert($values,$preparedFields)
	{
		$PDO = Database::getConnection();

		$sql = "insert users set $preparedFields";
		$query = $PDO->prepare($sql);
		$query->execute($values);
		$this->id = $PDO->lastInsertID();
	}

	public function delete()
	{
		$PDO = Database::getConnection();
		$PDO->beginTransaction();

		try
		{
			$query = $PDO->prepare('delete from user_roles where user_id=?');
			$query->execute(array($this->id));

			$query = $PDO->prepare('delete from users where id=?');
			$query->execute(array($this->id));

			$PDO->commit();
		}
		catch(Exception $e)
		{
			$PDO->rollBack();
			throw $e;
		}
	}

	private function savePhoneNumbers()
	{
		if ($this->id)
		{
			$PDO = Database::getConnection();
			$query = $PDO->prepare('delete from phoneNumbers where user_id=?');
			$query->execute(array($this->id));

			foreach ($this->getPhoneNumbers() as $phoneNumber)
			{
				$phoneNumber->setUser($this);
				$phoneNumber->save();
			}
		}
	}

	public function savePrivateFields()
	{
		$PDO = Database::getConnection();

		$query = $PDO->prepare('delete from user_private_fields where user_id=?');
		$query->execute(array($this->id));

		$query = $PDO->prepare('insert user_private_fields set user_id=?,fieldname=?');
		foreach ($this->privateFields as $field)
		{
			$query->execute(array($this->id,$field));
		}
	}

	#----------------------------------------------------------------
	# Generic Getters
	#----------------------------------------------------------------
	public function getId() { return $this->isGettable('id') ? $this->id : null; }
	public function getUsername() { return $this->isGettable('username') ? $this->username : ''; }
	public function getAuthenticationMethod() { return $this->isGettable('authenticationMethod') ? $this->authenticationMethod : null; }
	public function getFirstname() { return $this->isGettable('firstname') ? $this->firstname : 'Anonymous'; }
	public function getLastname() { return $this->isGettable('lastname') ? $this->lastname : 'Anonymous'; }
	public function getEmail() { return $this->isGettable('email') ? $this->email : null; }
	public function getAddress() { return $this->isGettable('address') ? $this->address : null; }
	public function getCity() { return $this->isGettable('city') ? $this->city : null; }
	public function getZipcode() { return $this->isGettable('zipcode') ? $this->zipcode : null; }
	public function getAbout() { return $this->isGettable('about') ? $this->about : null; }
	public function getGender() { return $this->isGettable('gender') ? $this->gender : null; }
	public function getRace_id() { return $this->isGettable('race_id') ? $this->race_id : null; }
	public function getRace()
	{
		if ($this->isGettable('race_id'))
		{
			if ($this->race_id)
			{
				if (!$this->race) { $this->race = new Race($this->race_id); }
				return $this->race;
			}
		}
		return null;
	}
	public function getBirthdate($format=null)
	{
		if ($this->isGettable('birthdate'))
		{
			if ($format && $this->birthdate)
			{
				if (strpos($format,'%')!==false) { return strftime($format,$this->birthdate); }
				else { return date($format,$this->birthdate); }
			}
			else return $this->birthdate;
		}
		return null;
	}

	#----------------------------------------------------------------
	# Generic Setters
	#----------------------------------------------------------------
	public function setUsername($string) { $this->username = trim($string); }
	/**
	 * Takes a user-given password and converts it to an MD5 Hash
	 * @param String $string
	 */
	public function setPassword($string)
	{
		# Save the user given password, so we can update it externally, if needed
		$this->newPassword = trim($string);
		$this->password = md5(trim($string));
	}
	/**
	 * Takes a pre-existing MD5 hash
	 * @param MD5 $hash
	 */
	public function setPasswordHash($hash) { $this->password = trim($hash); }
	public function setAuthenticationMethod($string) { $this->authenticationMethod = $string; }
	public function setFirstname($string) { $this->firstname = trim($string); }
	public function setLastname($string) { $this->lastname = trim($string); }
	public function setEmail($string) { $this->email = trim($string); }
	public function setAddress($string) { $this->address = trim($string); }
	public function setCity($string) { $this->city = trim($string); }
	public function setZipcode($string) { $this->zipcode = trim($string); }
	public function setAbout($text) { $this->about = $text; }
	public function setGender($string) { $this->gender = trim($string); }
	public function setRace($race) { $this->race_id = $race->getId(); $this->race = $race; }
	public function setRace_id($int)
	{
		if ($int)
		{
			$this->race = new Race($int);
			$this->race_id = $int;
		}
		else
		{
			$this->race = null;
			$this->race_id = null;
		}
	}
	public function setBirthdate($date)
	{
		if (is_array($date)) { $this->birthdate = $this->dateArrayToTimestamp($date); }
		elseif(ctype_digit($date)) { $this->birthdate = $date; }
		else { $this->birthdate = strtotime($date); }
	}

	#----------------------------------------------------------------
	# Custom Functions
	# We recommend adding all your custom code down here at the bottom
	#----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getFullname()
	{
		return "{$this->firstname} {$this->lastname}";
	}

	/**
	 * Returns an array of Role names with the role id as the array index
	 * @return array
	 */
	public function getRoles()
	{
		if (!count($this->roles))
		{
			if ($this->id)
			{
				$PDO = Database::getConnection();
				$sql = 'select role_id,name from user_roles left join roles on role_id=id where user_id=?';
				$query = $PDO->prepare($sql);
				$query->execute(array($this->id));
				$result = $query->fetchAll();
				if (count($result))
				{
					foreach ($result as $row) { $this->roles[$row['role_id']] = $row['name']; }
				}
			}
		}
		return $this->roles;
	}
	/**
	 * Takes an array of role names.  Loads the Roles from the database
	 * @param array $roleNames An array of names
	 */
	public function setRoles($roleNames)
	{
		$this->roles = array();
		if ($roleNames)
		{
			foreach ($roleNames as $name)
			{
				$role = new Role($name);
				$this->roles[$role->getId()] = $role->getName();
			}
		}
		# If this user is already in the database, and they set
		# the roles to be empty, we must wipe them out immediately
		# Otherwise, they will be reset when we go to save
		else
		{
			if ($this->id)
			{
				$PDO = Database::getConnection();
				$query = $PDO->prepare('delete from user_roles where user_id=?');
				$query->execute(array($this->id));
			}
		}
	}
	/**
	 * Takes a string or an array of strings and checks if the user has that role
	 * @param Array|String $roles
	 * @return boolean
	 */
	public function hasRole($roles)
	{
		if (is_array($roles))
		{
			foreach ($roles as $roleName)
			{
				if (in_array($roleName,$this->getRoles())) { return true; }
			}
			return false;
		}
		else { return in_array($roles,$this->getRoles()); }
	}
	private function updateRoles()
	{
		$PDO = Database::getConnection();

		$roles = $this->getRoles();

		$query = $PDO->prepare('delete from user_roles where user_id=?');
		$query->execute(array($this->id));

		$query = $PDO->prepare('insert user_roles values(?,?)');
		foreach ($roles as $role_id=>$roleName)
		{
			$query->execute(array($this->id,$role_id));
		}
	}

	/**
	 * Since passwords can be stored externally, we only want to bother trying
	 * to save them when they've actually changed
	 * @return boolean
	 */
	public function passwordHasChanged() { return $this->newPassword ? true : false; }

	/**
	 * Callback function from the SystemUser class
	 * The SystemUser will determine where the password should be stored.
	 * If the password is stored locally, it will call this function
	 */
	protected function saveLocalPassword()
	{
		$PDO = Database::getConnection();

		# Passwords in the class should already be MD5 hashed
		$query = $PDO->prepare('update users set password=? where id=?');
		$query->execute(array($this->password,$this->id));
	}

	/**
	 * Callback function from the SystemUser class
	 * The SystemUser class will determine where the authentication
	 * should occur.  If the user should be authenticated locally,
	 * this function will be called.
	 * @param string $password
	 * @return boolean
	 */
	protected function authenticateDatabase($password)
	{
		$PDO = Database::getConnection();

		$query = $PDO->prepare('select id from users where username=? and password=md5(?)');
		$query->execute(array($this->username,$password));
		$result = $query->fetchAll();
		return count($result) ? true : false;
	}

	/**
	 * Returns an array of PhoneNumbers
	 * @return array
	 */
	public function getPhoneNumbers()
	{
		if (!count($this->phoneNumbers))
		{
			$list = new PhoneNumberList(array('user_id'=>$this->id));
			foreach ($list as $phoneNumber)
			{
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
		foreach ($phoneNumbers as $posted)
		{
			if (trim($posted['number']))
			{
				$phoneNumber = new PhoneNumber();
				$phoneNumber->setNumber($posted['number']);
				$phoneNumber->setUser($this);
				if ($posted['ordering']) { $phoneNumber->setOrdering($posted['ordering']); }
				if ($posted['type']) { $phoneNumber->setType($posted['type']); }
				if (isset($posted['private'])) { $phoneNumber->setPrivate($posted['private']); }

				$this->phoneNumbers[] = $phoneNumber;
			}
		}
	}

	/**
	 * Takes an array of the user's fields that you want to make private
	 * @param array $fields
	 */
	public function setPrivateFields($fields=null)
	{
		if ($fields) { $this->privateFields = $fields; }
		else { $this->privateFields = array(); }
	}

	/**
	 * @return array
	 */
	public function getPrivateFields()
	{
		if (!$this->privateFields)
		{
			$PDO = Database::getConnection();
			$query = $PDO->prepare('select fieldname from user_private_fields where user_id=?');
			$query->execute(array($this->id));
			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			foreach ($result as $row)
			{
				$this->privateFields[] = $row['fieldname'];
			}
		}
		return $this->privateFields;
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
	 * @return MemberList
	 */
	public function getMembers()
	{
		return new MemberList(array('user_id'=>$this->id));
	}

	/**
	 * @return VotingRecordList
	 */
	public function getVotingRecords()
	{
		return new VotingRecordList(array('user_id'=>$this->id));
	}
}
