<?php
/**
 * Generates a Collection class for each the ActiveRecord objects
 *
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../configuration.inc';
$PDO = Database::getConnection();

$tables = array();
foreach ($PDO->query('show tables') as $row) {
	list($tables[]) = $row;
}

foreach ($tables as $tableName) {
	$fields = array();
	foreach ($PDO->query("describe $tableName") as $row) {
		$type = ereg_replace("[^a-z]","",$row['Type']);

		// Translate any MySQL datatype names into PHP datatype names
		if (ereg('int',$type)) {
			$type = 'int';
		}
		if (ereg('enum',$type) || ereg('varchar',$type)) {
			$type = 'string';
		}


		$fields[] = array('Field'=>$row['Field'],'Type'=>$type);
	}

	$result = $PDO->query("show index from $tableName where key_name='PRIMARY'")->fetchAll();
	if (count($result) != 1) {
		continue;
	}
	$key = $result[0];


	$className = Inflector::classify($tableName);
	//--------------------------------------------------------------------------
	// Constructor
	//--------------------------------------------------------------------------
	$constructor = "
	/**
	 * Creates a basic select statement for the collection.
	 * Populates the collection if you pass in \$fields
	 *
	 * @param array \$fields
	 */
	public function __construct(\$fields=null)
	{
		\$this->select = 'select $tableName.$key[Column_name] as id from $tableName';
		if (is_array(\$fields)) {
			\$this->find(\$fields);
		}
	}
";


	//--------------------------------------------------------------------------
	// Find
	//--------------------------------------------------------------------------
	$findFunction = "
	/**
	 * Populates the collection from the database based on the \$fields you handle
	 *
	 * @param array \$fields
	 * @param string \$sort
	 * @param int \$limit
	 * @param string \$groupBy
	 */
	public function find(\$fields=null,\$sort='id',\$limit=null,\$groupBy=null)
	{
		\$this->sort = \$sort;
		\$this->limit = \$limit;
		\$this->groupBy = \$groupBy;
		\$this->joins = '';

		\$options = array();
		\$parameters = array();
";
	foreach ($fields as $field) {
		$findFunction.= "
		if (isset(\$fields['$field[Field]'])) {
			\$options[] = '$field[Field]=:$field[Field]';
			\$parameters[':$field[Field]'] = \$fields['$field[Field]'];
		}
";
	}
	$findFunction.= "

		// Finding on fields from other tables required joining those tables.
		// You can add fields from other tables to \$options by adding the join SQL
		// to \$this->joins here

		\$this->populateList(\$options,\$parameters);
	}
";



	//--------------------------------------------------------------------------
	// Output the class
	//--------------------------------------------------------------------------
$contents = "<?php
/**
 * A collection class for $className objects
 *
 * This class creates a select statement, only selecting the ID from each row
 * PDOResultIterator handles iterating and paginating those results.
 * As the results are iterated over, PDOResultIterator will pass each desired
 * ID back to this class's loadResult() which will be responsible for hydrating
 * each $className object
 *
 * Beyond the basic \$fields handled, you will need to write your own handling
 * of whatever extra \$fields you need
 *
 * The PDOResultIterator uses prepared queries; it is recommended to use bound
 * parameters for each of the options you handle
 */
";
$contents.= COPYRIGHT;
$contents.="
class {$className}List extends PDOResultIterator
{
$constructor
$findFunction

	/**
	 * Loads a single $className object for the key returned from PDOResultIterator
	 * @param int \$key
	 */
	protected function loadResult(\$key)
	{
		return new $className(\$this->list[\$key]);
	}
}
";
	$dir = APPLICATION_HOME.'/scripts/stubs/classes';
	if (!is_dir($dir)) {
		mkdir($dir,0770,true);
	}
	file_put_contents("$dir/{$className}List.php",$contents);
	echo "$className\n";
}
