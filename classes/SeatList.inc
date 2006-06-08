<?php

	class SeatList extends PDOResultIterator
	{
		private $joins = "";
		private $where = "";

		public function __construct($fields=null) { if (is_array($fields)) $this->find($fields); }


		public function find($fields=null,$sort="id")
		{
			global $PDO;

			$options = array();
			if (isset($fields['id'])) { $options[] = "id='$fields[id]'"; }
			if (isset($fields['commission_id'])) { $options[] = "commission_id='$fields[commission_id]'"; }
			if (isset($fields['category_id'])) { $options[] = "category_id='$fields[category_id]'"; }
			if (isset($fields['title'])) { $options[] = "title='$fields[title]'"; }
			if (isset($fields['vacancy'])) { $options[] = "vacancy='$fields[vacancy]'"; }


			# Finding on fields from other tables required joining those tables.
			# You can add fields from other tables to $options by adding the join SQL
			# to $this->joins here


			if (count($options)) { $this->where = "where ".implode(" and ",$options); }
			$sql = "select id from seats {$this->joins} {$this->where} order by $sort";

			$result = $PDO->query($sql);
			if ($result)
			{
				foreach($result as $row) { $this->list[] = $row['id']; }
			}
			else { $e = $PDO->errorInfo(); throw new Exception($sql.$e[2]); }
		}
	

		protected function loadResult($key) { return new Seat($this->list[$key]); }
	}
?>