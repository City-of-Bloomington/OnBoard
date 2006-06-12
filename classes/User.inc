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