<?php
	class SeatCategory
	{
		private $id;
		private $category;

		public function __construct($id=null)
		{
			global $PDO;
			if ($id) 
			{
				if (is_numeric($id)) { $sql = "select * from seatCategories where id=$id"; }
				else { $sql = "select * from seatCategories where category='$id'";}
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
					else { 
						if (is_numeric($id)) { throw new Exception($sql); } 
						else { $this->category = $id;}
					}
				}
				else { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
			}
			else
			{	
				
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
			$fields[] = $this->category ? "category='{$this->category}'" : "category=null";

			$fields = implode(",",$fields);


			if ($this->id) { $this->update($fields); }
			else { $this->insert($fields); }
		}

		private function update($fields)
		{
			global $PDO;

			$sql = "update seatCategories set $fields where id={$this->id}";
			if (false === $PDO->exec($sql)) { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
		}

		private function insert($fields)
		{
			global $PDO;

			$sql = "insert seatCategories set $fields";
			if (false === $PDO->exec($sql)) { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
			$this->id = $PDO->lastInsertID();
		}


		public function getId() { return $this->id; }
		public function getCategory() { return $this->category; }


		public function setId($int) { $this->id = $int; }
		public function setCategory($string) { $this->category = $string; }

	}
?>