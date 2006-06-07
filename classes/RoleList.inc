<?php
/*
	Roles are really just an attribute array for users.  For now, I'm not loading them in as objects.
*/
	require_once(GLOBAL_INCLUDES."/classes/PDOResultIterator.inc");

	class RoleList extends PDOResultIterator
	{

		public function find($fields=null,$sort='role')
		{
			global $PDO;

			$options = array();
			if (isset($fields['role'])) { $options[] = "role='$fields[role]'"; }

			if (count($options)) { $where = " where ".implode(" and ",$options); } else { $where = ""; }
			$sql = "select role from roles $where order by $sort";

			foreach($PDO->query($sql) as $row) { $this->list[] = $row['role']; }
		}


		protected function loadResult($key) { return $this->list[$key]; }
	}
?>