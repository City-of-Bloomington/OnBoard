<?php
mysql_connect(":/tmp/mysql.sock","username","password") or die(mysql_error());
mysql_select_db("database") or die(mysql_error());

$sql = "show tables";
$tables = mysql_query($sql) or die($sql.mysql_error());
while(list($tableName) = mysql_fetch_array($tables))
{
	$className = ucwords($tableName);
	echo "$className\n";

	$sql = "describe $tableName";
	$description = mysql_query($sql) or die($sql.mysql_error());
	$fields = array();
	while($row = mysql_fetch_array($description))
	{
		$type = ereg_replace("[^a-z]","",$row['Type']);

		if (ereg("int",$type)) { $type = "int"; }
		if (ereg("enum",$type) || ereg("varchar",$type)) { $type = "string"; }


		$fields[] = array('Field'=>$row['Field'],'Type'=>$type);

		#echo "\t$row[Field] - $type\n";
	}

	$constructor = "";
	$sql = "show index from $tableName where key_name='PRIMARY'";
	$temp = mysql_query($sql) or die($sql.mysql_error());

	# This code should really only be run on tables with a single primary key
	# The other tables are either linking tables, or are multiple attributes of a single-keyed table
	if (mysql_num_rows($temp) != 1) { continue; }
	$key = mysql_fetch_array($temp);



	$findFunction = "
		public function find(\$fields=null,\$sort=\"$key[Column_name]\")
		{
			global \$PDO;

			\$options = array();
";
			foreach($fields as $field) { $findFunction.="\t\t\tif (isset(\$fields['$field[Field]'])) { \$options[] = \"$field[Field]='\$fields[$field[Field]]'\"; }\n"; }
	$findFunction.="

			# Finding on fields from other tables required joining those tables.
			# You can add fields from other tables to \$options by adding the join SQL
			# to \$this->joins here


			if (count(\$options)) { \$this->where = \"where \".implode(\" and \",\$options); }
			\$sql = \"select $key[Column_name] from $tableName {\$this->joins} {\$this->where} order by \$sort\";

			\$result = \$PDO->query(\$sql);
			if (\$result)
			{
				foreach(\$result as \$row) { \$this->list[] = \$row['$key[Column_name]']; }
			}
			else { \$e = \$PDO->errorInfo(); throw new Exception(\$sql.\$e[2]); }
		}
	";



$contents = "<?php

	class {$className}List extends PDOResultIterator
	{
		private \$joins = \"\";
		private \$where = \"\";

		public function __construct(\$fields=null) { if (is_array(\$fields)) \$this->find(\$fields); }

$findFunction

		protected function loadResult(\$key) { return new $className(\$this->list[\$key]); }
	}
?>";
		file_put_contents("./classStubs/{$className}List.inc",$contents);
	}
?>