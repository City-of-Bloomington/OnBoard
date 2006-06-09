<?php
/*
	Restrictions are really just an attribute array for users.  For now, I'm not loading them in as objects.
*/
	require_once(GLOBAL_INCLUDES."/classes/PDOResultIterator.inc");

	class RestrictionList extends PDOResultIterator
	{

		public function find($fields=null,$sort='restriction')
		{
			global $PDO;

			$options = array();
			if (isset($fields['restriction'])) { $options[] = "restriction='$fields[restriction]'"; }

			if (count($options)) { $where = " where ".implode(" and ",$options); } else { $where = ""; }
			$sql = "select restriction from restrictions $where order by $sort";

			foreach($PDO->query($sql) as $row) { $this->list[] = $row['restriction']; }
		}


		protected function loadResult($key) { return $this->list[$key]; }
	}
?>