<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
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
	private $timestamp;

	private $roles = array();
	private $newPassword; # the User's new password, unencrypted

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
			foreach($result[0] as $field=>$value) { if ($value) $this->$field = $value; }
		}
		else
		{
			# This is where the code goes to generate a new, empty instance.
			# Set any default values for properties that need it here
		}
	}

	public function save()
	{
		# Check for required fields before saving
		if (!$this->firstname || !$this->lastname) { throw new Exception("missingRequiredFields"); }

		$fields = array();
		$fields['username'] = $this->username ? $this->username : null;
		# Passwords should not be updated by default.  Use the savePassword() function
		$fields['authenticationMethod'] = $this->authenticationMethod ? $this->authenticationMethod : null;
		$fields['firstname'] = $this->firstname ? $this->firstname : null;
		$fields['lastname'] = $this->lastname ? $this->lastname : null;
		$fields['email'] = $this->email ? $this->email : null;
		$fields['address'] = $this->address ? $this->address : null;
		$fields['city'] = $this->city ? $this->city : null;
		$fields['zipcode'] = $this->zipcode ? $this->zipcode : null;
		$fields['about'] = $this->about ? $this->about : null;
		# Timestamp should be left alone, let the database set it automatically

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

		# Do the database calls
		if ($this->id) { $this->update($values,$preparedFields); }
		else { $this->insert($values,$preparedFields); }

		# Save the password only if it's changed
		if ($this->passwordHasChanged()) { $this->savePassword(); }

		$this->updateRoles();
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

	#----------------------------------------------------------------
	# Generic Getters
	#----------------------------------------------------------------
	public function getId() { return $this->id; }
	public function getUsername() { return $this->username; }
	public function getAuthenticationMethod() { return $this->authenticationMethod; }
	public function getFirstname() { return $this->firstname; }
	public function getLastname() { return $this->lastname; }
	public function getEmail() { return $this->email; }
	public function getAddress() { return $this->address; }
	public function getCity() { return $this->city; }
	public function getZipcode() { return $this->zipcode; }
	public function getAbout() { return $this->about; }

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
					foreach($result as $row) { $this->roles[$row['role_id']] = $row['name']; }
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
		foreach($roleNames as $name)
		{
			$role = new Role($name);
			$this->roles[$role->getId()] = $role->getName();
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
			foreach($roles as $roleName)
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
		foreach($roles as $role_id=>$roleName)
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
}
