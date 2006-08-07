<?php
	require_once(APPLICATION_HOME."/classes/User.inc");
	require_once(GLOBAL_INCLUDES."/classes/PDOResultIterator.inc");

	class UserList extends PDOResultIterator
	{
		private $joins = "";
		private $where = "";

		public function find($fields=null,$sort = "username")
		{
			global $PDO;

			if ($fields)
			{
				$options = array();

				if (isset($fields['id'])) 
				{ 
					$options[] = "users.id=$fields[id]"; 
				}
				
				if (isset($fields['username'])) 
				{ 
					$options[] = "username='$fields[username]'"; 
				}
				
				if (isset($fields['authenticationMethod'])) 
				{ 
					$options[] = "authenticationMethod='$fields[authenticationMethod]'"; 
				}
				
				if (isset($fields['firstname'])) 
				{ 
					$options[] = "firstname='$fields[firstname]'"; 
				}
				
				if (isset($fields['lastname'])) 
				{ 
					$options[] = "lastname='$fields[lastname]'"; 
				}

				# To search on Role, you have to join the userRoles table
				if (isset($fields['role']))
				{
					$this->joins = "left join user_roles on users.id=user_id left join roles on role_id=roles.id";
					$options[] = "role='$fields[role]'";
				}

				$this->where = "where ".implode(" and ",$options);
				$sql.= "select distinct users.id from users {$this->joins} {$this->where} order by $sort";
			}
			else
			{ 
				$sql = "select id from users order by $sort"; 
			}

			$result = $PDO->query($sql);
			if ($result)
			{
				foreach($result as $row) 
				{ 
					$this->list[] = $row['id']; 
				}
			}
			else
			{ 
				$e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); 
			}
		}

		public function search($fields=null,$sort = "username")
		{
			global $PDO;

			if ($fields)
			{
				$options = array();

				if (isset($fields['id'])) 
				{ 
					$options[] = "users.id=$fields[id]"; 
				}
				
				if (isset($fields['username'])) 
				{
					$options[] = "username like '$fields[username]%'"; 
				}
			
				if (isset($fields['authenticationMethod']))
				{ 
					$options[] = "authenticationMethod='$fields[authenticationMethod]'"; 
				}
				
				if (isset($fields['firstname'])) 
				{ 
					$options[] = "firstname like '$fields[firstname]%'"; 
				}
				
				if (isset($fields['lastname'])) 
				{ 
					$options[] = "lastname like '$fields[lastname]%'"; 
				}

				# To search on Role, you have to join the userRoles table
				if (isset($fields['role']))
				{
					$this->joins = "left join user_roles on users.id=user_id left join roles on role_id=roles.id";
					$options[] = "role='$fields[role]'";
				}

				$this->where = "where ".implode(" and ",$options);
				$sql.= "select distinct users.id from users {$this->joins} {$this->where} order by $sort";
			}
			else 
			{ 
				$sql = "select id from users order by $sort"; 
			}

			$result = $PDO->query($sql);
			if ($result)
			{
				foreach($result as $row) 
				{ 
					$this->list[] = $row['id']; 
				}
			}
			else 
			{ 
				$e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); 
			}
		}
		
		protected function loadResult($key) 
		{ 
			return new User($this->list[$key]); 
		}
	}
?>