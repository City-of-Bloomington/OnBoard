<?php

	class ApplicationList extends PDOResultIterator
	{
		private $joins = "";
		private $where = "";

		public function __construct($fields=null) { if (is_array($fields)) $this->find($fields); }


		public function find($fields=null,$sort="id")
		{
			global $PDO;

			$options = array();
			if (isset($fields['id'])) { $options[] = "id='$fields[id]'"; }
			if (isset($fields['committee_id'])) { $options[] = "committee_id='$fields[committee_id]'"; }
			if (isset($fields['resumePath'])) { $options[] = "resumePath='$fields[resumePath]'"; }
			if (isset($fields['firstname'])) { $options[] = "firstname='$fields[firstname]'"; }
			if (isset($fields['lastname'])) { $options[] = "lastname='$fields[lastname]'"; }
			if (isset($fields['email'])) { $options[] = "email='$fields[email]'"; }
			if (isset($fields['address'])) { $options[] = "address='$fields[address]'"; }
			if (isset($fields['city'])) { $options[] = "city='$fields[city]'"; }
			if (isset($fields['zipcode'])) { $options[] = "zipcode='$fields[zipcode]'"; }
			if (isset($fields['homePhone'])) { $options[] = "homePhone='$fields[homePhone]'"; }
			if (isset($fields['workPhone'])) { $options[] = "workPhone='$fields[workPhone]'"; }
			if (isset($fields['resident'])) { $options[] = "resident='$fields[resident]'"; }
			if (isset($fields['occupation'])) { $options[] = "occupation='$fields[occupation]'"; }
			if (isset($fields['interest'])) { $options[] = "interest='$fields[interest]'"; }
			if (isset($fields['qualifications'])) { $options[] = "qualifications='$fields[qualifications]'"; }
			if (isset($fields['timestamp'])) { $options[] = "timestamp='$fields[timestamp]'"; }


			# Finding on fields from other tables required joining those tables.
			# You can add fields from other tables to $options by adding the join SQL
			# to $this->joins here


			if (count($options)) { $this->where = "where ".implode(" and ",$options); }
			$sql = "select id from applications {$this->joins} {$this->where} order by $sort";

			$result = $PDO->query($sql);
			if ($result)
			{
				foreach($result as $row) { $this->list[] = $row['id']; }
			}
			else { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
		}
	

		protected function loadResult($key) { return new Application($this->list[$key]); }
	}
?>