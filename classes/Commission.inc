<?php
	class Commission
	{
		private $id;
		private $name;
		private $count;

		public function __construct($id=null)
		{
			global $PDO;

			if ($id)
			{
				$sql = "select * from commissions where id=$id";
				$result = $PDO->query($sql);
				if ($result)
				{
					if ($row = $result->fetch())
					{
						# This will load all fields in the table as properties of this class.
						# You may want to replace this with, or add your own extra, custom loading
						foreach($row as $field=>$value) { if ($value) $this->$field = $value; }


						$result->closeCursor();
					}
					else { throw new Exception($sql); }
				}
				else { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
			}
			else
			{
				# This is where the code goes to generate a new, empty instance.
				# Set any default values for properties that need it here
			}
		}
		

		public function save()
		{
			# Check for required fields here.  Throw an exception if anything is missing.


			# This generates generic SQL that should work right away.
			# You can (and maybe should) replace this $fields code with your own custom SQL
			# for each property of this class,
			$fields = array();
			$fields[] = $this->id ? "id='{$this->id}'" : "id=null";
			$fields[] = $this->name ? "name='{$this->name}'" : "name=null";
			$fields[] = $this->count ? "count='{$this->count}'" : "name=count";

			$fields = implode(",",$fields);


			if ($this->id) { $this->update($fields); }
			else { $this->insert($fields); }
		}

		private function update($fields)
		{
			global $PDO;

			$sql = "update commissions set $fields where id={$this->id}";
			if (false === $PDO->exec($sql)) { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
		}

		private function insert($fields)
		{
			global $PDO;

			$sql = "insert commissions set $fields";
			if (false === $PDO->exec($sql)) { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
			$this->id = $PDO->lastInsertID();
		}

		public function getId() { return $this->id; }
		public function getName() { return $this->name; }
		public function getCount() { return $this->count; }

		public function setId($int) { $this->id = $int; }
		public function setName($string) { $this->name = $string; }
		public function setCount($int) { $this->count = $int; }

	}
?>