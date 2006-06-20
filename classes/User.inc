<?php
	require_once(GLOBAL_INCLUDES."/classes/SystemUser.inc");

	class User extends SystemUser
	{
		private $id;
		private $username;
		private $password;
		private $authenticationMethod;

		private $firstname;
		private $lastname;

		private $roles;
		
		private $city;
		private $zipcode;
		private $street;
		private $email;
		private $about;
		private $homephone;
		private $workphone;

		public function __construct($id = null)
		{
			global $PDO;

			if ($id)
			{
				# Load an existing user
				if (is_numeric($id)) { $sql = "select * from users where id=$id"; }
				else { $sql = "select * from users where username='$id'"; }

				$result = $PDO->query($sql);
				if ($user = $result->fetch())
				{
					$this->id = $user['id'];
					$this->username = $user['username'];
					# Passwords are one-way encrypted.  They should not be loaded by default
					$this->authenticationMethod = $user['authenticationMethod'];

					$this->firstname = $user['firstname'];
					$this->lastname = $user['lastname'];
					$this->street = $user['street'];
					$this->email = $user['email'];
					$this->city = $user['city'];
					$this->zipcode= $user['zipcode'];
					$this->homephone = $user['homephone'];
					$this->workphone = $user['workphone'];
					$this->about = $user['about'];

					$result->closeCursor();
				}
				else { throw new Exception("unknownUser"); }
			}
		}

		public function save()
		{
			# Check for required fields before saving
			if (!$this->username || !$this->firstname || !$this->lastname) { throw new Exception("missingRequiredFields"); }

			$fields = array();
			$fields[] = $this->username ? "username='{$this->username}'" : "username=null";
			$fields[] = $this->firstname ? "firstname='{$this->firstname}'" : "firstname=null";
			$fields[] = $this->lastname ? "lastname='{$this->lastname}'" : "lastname=null";
			$fields[] = $this->email ? "email='{$this->email}'" : "email=null";
			$fields[] = $this->street ? "street='{$this->street}'" : "street=null";
			$fields[] = $this->zipcode ? "zipcode='{$this->zipcode}'" : "zipcode=null";
			$fields[] = $this->city ? "city='{$this->city}'" : "city=null";
			$fields[] = $this->workphone ? "workphone='{$this->workphone}'" : "workphone=null";
			$fields[] = $this->homephone ? "homephone='{$this->homephone}'" : "homephone=null";
			$fields[] = $this->about ? "about='{$this->about}'" : "about=null";
			$fields[] = $this->authenticationMethod ? "authenticationMethod='{$this->authenticationMethod}'" : "authenticationMethod=local";
			$fields = implode(",",$fields);

			if ($this->id) { $this->update($fields); }
			else { $this->insert($fields); }

			$this->updateRoles();
		}

		private function update($fields)
		{
			global $PDO;

			$sql = "update users set $fields where id={$this->id}";

			if (false === $PDO->exec($sql)) { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
		}

		private function insert($fields)
		{
			global $PDO;

			$sql = "insert users set $fields";


			if (false === $PDO->exec($sql)) { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
			$this->id = $PDO->lastInsertID();

			# Save the initial password if we've added one
			if ($this->getPassword()) { $this->savePassword(); }
		}


		private function updateRoles()
		{
			global $PDO;

			$roles = $this->getRoles();

			$sql = "delete from user_roles where user_id={$this->id}";
			if (false === $PDO->exec($sql)) { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }

			$statement = $PDO->prepare("insert user_roles set user_id={$this->id},role_id=?");
			foreach($roles as $role_id=>$role) 
			{ 
				if (false === $statement->execute(array("$role_id"))) { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
			}
		}

		protected function saveLocalPassword()
		{
			global $PDO;

			$sql = "update users set password=md5('{$this->password}') where id={$this->id}";
			if (false === $PDO->exec($sql)) { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
		}

		protected function authenticateDatabase($password)
		{
			global $PDO;

			$sql = "select id from users where username='{$this->username}' and password=md5('$password')";
			$result = $PDO->query($sql);
			if ($user = $result->fetch()) { return true; }
			else { return false; }
			$result->closeCursor();
		}

		public function getId() { return $this->id; }
		public function getUsername() { return $this->username; }
		public function getPassword() { return $this->password; }
		public function getAuthenticationMethod() { return $this->authenticationMethod; }
		public function getFirstname() { return $this->firstname; }
		public function getLastname() { return $this->lastname; }
		public function getCity() { return $this->city; }
		public function getZipcode() { return $this->zipcode; }
		public function getStreet() { return $this->street; }
		public function getEmail() { return $this->email; }
		public function getAbout() { return $this->about; }
		public function getHomephone() { return $this->homephone; }
		public function getWorkphone() { return $this->workphone; }
		
		public function getRoles()
		{
			global $PDO;

			if (!$this->roles)
			{
				$this->roles = array();
				$sql = "select role_id,role from user_roles left join roles on role_id=id where user_id={$this->id}";
				if ($result = $PDO->query($sql))
				{
					foreach($result as $row) { $this->roles[$row['role_id']] = $row['role']; }
					$result->closeCursor();
				}
			}
			return $this->roles;
		}

		public function setUsername($string) { $this->username = sanitizeString($string); }
		public function setPassword($string) { $this->password = $string; }
		public function setAuthenticationMethod($string) { $this->authenticationMethod = $string; }
		public function setFirstname($string) { $this->firstname = sanitizeString($string); }
		public function setLastname($string) { $this->lastname = sanitizeString($string); }
		public function setStreet($string) { $this->street = sanitizeString($string); }
		public function setCity($string) { $this->city = sanitizeString($string); }
		public function setZipcode($string) { $this->zipcode = sanitizeString($string); }
		public function setEmail($string) { $this->email = sanitizeString($string); }
		public function setHomephone($string) { $this->homephone = sanitizeString($string); }
		public function setWorkphone($string) { $this->workphone = sanitizeString($string); }
		public function setAbout($string) { $this->about = sanitizeString($string); }
		
		
		public function setRoles($array) 
		{ 
			$this->roles = array();
			foreach($array as $id)
			{
				$role = new Role($id);
				$this->roles[$role->getId()] = $role->getRole();
			} 
		}
		
		public function deleteUser() 
		{
			global $PDO;

			$sql = "delete from user_roles where user_id ={$this->id}";
			if (false === $PDO->exec($sql)) { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }

			$sql = "delete from users where id ={$this->id}";
			if (false === $PDO->exec($sql)) { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
		}
	}
?>