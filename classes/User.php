<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class User extends SystemUser
{
	private $id;
	private $person_id;
	private $username;
	private $password;
	private $authenticationMethod;

	private $person;
	private $roles = array();
	private $newPassword; // the User's new password, unencrypted

	/**
	 * Should provide the list of methods supported
	 *
	 * There should always be at least one method, called "local"
	 * Additional methods must match classes that implement External Identities
	 * See: ExternalIdentity.php
	 *
	 * @return array
	 */
	public static function getAuthenticationMethods()
	{
		return array('local','Employee');
	}

	/**
	 * @param int|string $id
	 */
	public function __construct($id = null)
	{
		if ($id) {
			$PDO = Database::getConnection();

			// Load an existing user
			if (ctype_digit($id)) {
				$sql = 'select * from users where id=?';
			}
			else {
				$sql = 'select * from users where username=?';
			}

			$query = $PDO->prepare($sql);
			$query->execute(array($id));

			$result = $query->fetchAll(PDO::FETCH_ASSOC);
			if (!count($result)) {
				throw new Exception('users/unknownUser');
			}
			foreach ($result[0] as $field=>$value) {
				if ($value) $this->$field = $value;
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
		if (!$this->person_id) {
			throw new Exception('users/missingPerson_id');
		}
		if (!$this->username) {
			throw new Exception('users/missingUsername');
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
		$fields['username'] = $this->username;
		// Passwords should not be updated by default.  Use the savePassword() function
		$fields['authenticationMethod'] = $this->authenticationMethod
										? $this->authenticationMethod
										: null;

		// Split the fields up into a preparedFields array and a values array.
		// PDO->execute cannot take an associative array for values, so we have
		// to strip out the keys from $fields
		$preparedFields = array();
		foreach ($fields as $key=>$value) {
			$preparedFields[] = "$key=?";
			$values[] = $value;
		}
		$preparedFields = implode(",",$preparedFields);

		// Do the database calls
		if ($this->id) {
			$this->update($values,$preparedFields);
		}
		else {
			$this->insert($values,$preparedFields);
		}

		// Save the password only if it's changed
		if ($this->passwordHasChanged()) {
			$this->savePassword();
		}

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

	/**
	 * Removes this object from the database
	 */
	public function delete()
	{
		$PDO = Database::getConnection();
		$PDO->beginTransaction();

		try {
			$query = $PDO->prepare('delete from user_roles where user_id=?');
			$query->execute(array($this->id));

			$query = $PDO->prepare('delete from users where id=?');
			$query->execute(array($this->id));

			$PDO->commit();
		}
		catch(Exception $e) {
			$PDO->rollBack();
			throw $e;
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
	public function getPerson_id()
	{
		return $this->person_id;
	}
	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}
	/**
	 * @return string
	 */
	public function getAuthenticationMethod()
	{
		return $this->authenticationMethod;
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
	/**
	 * @return string
	 */
	public function getFirstname()
	{
		return $this->getPerson()->getFirstname();
	}
	/**
	 * @return string
	 */
	public function getLastname()
	{
		return $this->getPerson()->getLastname();
	}
	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->getPerson()->getEmail();
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
	 * @param string $string
	 */
	public function setUsername($string)
	{
		$this->username = trim($string);
	}
	/**
	 * Takes a user-given password and converts it to an MD5 Hash
	 * @param String $string
	 */
	public function setPassword($string)
	{
		// Save the user given password, so we can update it externally, if needed
		$this->newPassword = trim($string);
		$this->password = md5(trim($string));
	}
	/**
	 * Takes a pre-existing MD5 hash
	 * @param MD5 $hash
	 */
	public function setPasswordHash($hash)
	{
		$this->password = trim($hash);
	}
	/**
	 * @param string $authenticationMethod
	 */
	public function setAuthenticationMethod($string)
	{
		$this->authenticationMethod = $string;
		if ($this->authenticationMethod != 'local') {
			$this->password = null;
			$this->saveLocalPassword();
		}
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
	 * Returns an array of Role names with the role id as the array index
	 * @return array
	 */
	public function getRoles()
	{
		if (!count($this->roles)) {
			if ($this->id) {
				$PDO = Database::getConnection();
				$sql = 'select role_id,name from user_roles left join roles on role_id=id where user_id=?';
				$query = $PDO->prepare($sql);
				$query->execute(array($this->id));
				$result = $query->fetchAll();

				foreach ($result as $row) {
					$this->roles[$row['role_id']] = $row['name'];
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
		foreach ($roleNames as $name) {
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
		if (is_array($roles)) {
			foreach ($roles as $roleName) {
				if (in_array($roleName,$this->getRoles())) {
					return true;
				}
			}
			return false;
		}
		else {
			return in_array($roles,$this->getRoles());
		}
	}

	private function updateRoles()
	{
		$PDO = Database::getConnection();

		$roles = $this->getRoles();

		$query = $PDO->prepare('delete from user_roles where user_id=?');
		$query->execute(array($this->id));

		$query = $PDO->prepare('insert user_roles values(?,?)');
		foreach ($roles as $role_id=>$roleName) {
			$query->execute(array($this->id,$role_id));
		}
	}

	/**
	 * Since passwords can be stored externally, we only want to bother trying
	 * to save them when they've actually changed
	 * @return boolean
	 */
	public function passwordHasChanged()
	{
		return $this->newPassword ? true : false;
	}

	/**
	 * Callback function from the SystemUser class
	 * The SystemUser will determine where the password should be stored.
	 * If the password is stored locally, it will call this function
	 */
	protected function saveLocalPassword()
	{
		$PDO = Database::getConnection();

		// Passwords in the class should already be MD5 hashed
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
