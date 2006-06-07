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
		$constructor = "
		public function __construct(\$$key[Column_name]=null)
		{
			global \$PDO;

			if (\$$key[Column_name])
			{
				\$sql = \"select * from $tableName where $key[Column_name]=\$$key[Column_name]\";
				\$result = \$PDO->query(\$sql);
				if (\$result)
				{
					if (\$row = \$result->fetch())
					{
						# This will load all fields in the table as properties of this class.
						# You may want to replace this with, or add your own extra, custom loading
						foreach(\$row as \$field=>\$value) { if (\$value) \$this->\$field = \$value; }


						\$result->closeCursor();
					}
					else { throw new Exception(\$sql); }
				}
				else { \$e = \$PDO->errorInfo(); throw new Exception(\$sql.\$e[2]); }
			}
			else
			{
				# This is where the code goes to generate a new, empty instance.
				# Set any default values for properties that need it here
			}
		}
		";

	$properties = "";
	foreach($fields as $field) { $properties.= "\t\tprivate \$$field[Field];\n"; }

	$getters = "";
	foreach($fields as $field)
	{
		$fieldFunctionName = ucwords($field['Field']);
		$getters.= "\t\tpublic function get$fieldFunctionName() { return \$this->$field[Field]; }\n";
	}

	$setters = "";
	foreach($fields as $field)
	{
		$fieldFunctionName = ucwords($field['Field']);
		$setters.= "\t\tpublic function set$fieldFunctionName(\$$field[Type]) { \$this->$field[Field] = \$$field[Type]; }\n";
	}

$contents = "<?php
	class $className
	{
$properties

$constructor

		public function save()
		{
			# Check for required fields here.  Throw an exception if anything is missing.


			# This generates generic SQL that should work right away.
			# You can (and maybe should) replace this \$fields code with your own custom SQL
			# for each property of this class,
			\$fields = array();
";
			foreach($fields as $field) { $contents.="\t\t\t\$fields[] = \$this->$field[Field] ? \"$field[Field]='{\$this->$field[Field]}'\" : \"$field[Field]=null\";\n"; }
$contents.= "
			\$fields = implode(\",\",\$fields);


			if (\$this->$key[Column_name]) { \$this->update(\$fields); }
			else { \$this->insert(\$fields); }
		}

		private function update(\$fields)
		{
			global \$PDO;

			\$sql = \"update $tableName set \$fields where $key[Column_name]={\$this->$key[Column_name]}\";
			if (false === \$PDO->exec(\$sql)) { \$e = \$PDO->errorInfo(); throw new Exception(\$sql.\$e[2]); }
		}

		private function insert(\$fields)
		{
			global \$PDO;

			\$sql = \"insert $tableName set \$fields\";
			if (false === \$PDO->exec(\$sql)) { \$e = \$PDO->errorInfo(); throw new Exception(\$sql.\$e[2]); }
			\$this->$key[Column_name] = \$PDO->lastInsertID();
		}


$getters

$setters
	}
?>";
		file_put_contents("./classStubs/$className.inc",$contents);
	}
?>