<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../configuration.inc';
$PDO = Database::getConnection();

$tables = array();
foreach($PDO->query("show tables") as $row) { list($tables[]) = $row; }

foreach($tables as $tableName)
{
	$fields = array();
	foreach($PDO->query("describe $tableName") as $row)
	{
		$type = ereg_replace("[^a-z]","",$row['Type']);

		if (ereg("int",$type)) { $type = "int"; }
		if (ereg("enum",$type) || ereg("varchar",$type)) { $type = "string"; }


		$fields[] = array('Field'=>$row['Field'],'Type'=>$type);
	}

	$result = $PDO->query("show index from $tableName where key_name='PRIMARY'")->fetchAll();
	if (count($result) != 1) { continue; }
	$key = $result[0];


	$className = Inflector::classify($tableName);
	#--------------------------------------------------------------------------
	# Constructor
	#--------------------------------------------------------------------------
	$constructor = "
	public function __construct(\$fields=null)
	{
		\$this->select = 'select $tableName.$key[Column_name] as id from $tableName';
		if (is_array(\$fields)) \$this->find(\$fields);
	}
	";


	#--------------------------------------------------------------------------
	# Find
	#--------------------------------------------------------------------------
	$findFunction = "
	public function find(\$fields=null,\$sort='id',\$limit=null,\$groupBy=null)
	{
		\$this->sort = \$sort;
		\$this->limit = \$limit;
		\$this->groupBy = \$groupBy;
		\$this->joins = '';

		\$options = array();
		\$parameters = array();
";
	foreach($fields as $field)
	{
		$findFunction.="\t\tif (isset(\$fields['$field[Field]']))
		{
			\$options[] = '$field[Field]=:$field[Field]';
			\$parameters[':$field[Field]'] = \$fields['$field[Field]'];
		}\n";
	}
	$findFunction.="

		# Finding on fields from other tables required joining those tables.
		# You can add fields from other tables to \$options by adding the join SQL
		# to \$this->joins here

		\$this->populateList(\$options,\$parameters);
	}
	";



	#--------------------------------------------------------------------------
	# Output the class
	#--------------------------------------------------------------------------
$contents = "<?php\n";
$contents.= COPYRIGHT;
$contents.="
class {$className}List extends PDOResultIterator
{
$constructor
$findFunction

	protected function loadResult(\$key) { return new $className(\$this->list[\$key]); }
}
";
	$dir = APPLICATION_HOME.'/scripts/stubs/classes';
	if (!is_dir($dir)) { mkdir($dir,0770,true); }
	file_put_contents("$dir/{$className}List.inc",$contents);
	echo "$className\n";
}
