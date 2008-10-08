<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../configuration.inc';
$dir = APPLICATION_HOME.'/scripts/stubs/tests';
if (!is_dir($dir)) { mkdir($dir,0770,true); }

$dir = APPLICATION_HOME.'/scripts/stubs/tests/DatabaseTests';
if (!is_dir($dir)) { mkdir($dir,0770,true); }

$dir = APPLICATION_HOME.'/scripts/stubs/tests/UnitTests';
if (!is_dir($dir)) { mkdir($dir,0770,true); }

$dir = APPLICATION_HOME.'/scripts/stubs/tests';


$PDO = Database::getConnection();
$tables = array();
foreach($PDO->query("show tables") as $row) { list($tables[]) = $row; }

$classes = array();
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
	$classes[] = $className;

	$variable = strtolower($className);

#------------------------------------------------------------------------------
# Generate the Unit Tests
#------------------------------------------------------------------------------
$contents = "<?php
require_once 'PHPUnit/Framework.php';

class {$className}UnitTest extends PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		\${$variable} = new {$className}();
		try
		{
			\${$variable}->validate();
			\$this->fail('Missing name failed to throw exception');
		}
		catch (Exception \$e) { }

		\${$variable}->setName('Test {$className}');
		\${$variable}->validate();
	}
}
";
file_put_contents("$dir/UnitTests/{$className}UnitTest.php",$contents);

#------------------------------------------------------------------------------
# Generate the Database Tests
#------------------------------------------------------------------------------
$contents = "<?php
require_once 'PHPUnit/Framework.php';

class {$className}DbTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		\$dir = dirname(__FILE__);

		\$PDO = Database::getConnection();
		\$PDO->exec('drop database '.DB_NAME);
		\$PDO->exec('create database '.DB_NAME);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME.\" < \$dir/../testData.sql\");

		\$PDO = Database::getConnection(true);
	}

    public function testSaveLoad()
    {
		\${$variable} = new {$className}();
		\${$variable}->setName('Test {$className}');
    	try
		{
			\${$variable}->save();
			\$id = \${$variable}->getId();
			\$this->assertGreaterThan(0,\$id);
		}
		catch (Exception \$e) { \$this->fail(\$e->getMessage()); }

		\${$variable} = new {$className}(\$id);
		\$this->assertEquals(\${$variable}->getName(),'Test {$className}');

		\${$variable}->setName('Test');
		\${$variable}->save();

		\${$variable} = new {$className}(\$id);
		\$this->assertEquals(\${$variable}->getName(),'Test');
    }
}
";
file_put_contents("$dir/DatabaseTests/{$className}DbTest.php",$contents);

#------------------------------------------------------------------------------
# Generate the Database List Tests
#------------------------------------------------------------------------------
$contents = "<?php
require_once 'PHPUnit/Framework.php';

class {$className}ListDbTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		\$dir = dirname(__FILE__);

		\$PDO = Database::getConnection();
		\$PDO->exec('drop database '.DB_NAME);
		\$PDO->exec('create database '.DB_NAME);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME.\" < \$dir/../testData.sql\");

		\$PDO = Database::getConnection(true);
	}

	/**
	 * Makes sure find returns all $tableName ordered correctly by default
	 */
	public function testFindOrderedByName()
	{
		\$PDO = Database::getConnection();
		\$query = \$PDO->query('select id from $tableName order by id');
		\$result = \$query->fetchAll();

		\$list = new {$className}List();
		\$list->find();
		\$this->assertEquals(\$list->getSort(),'id');

		foreach(\$list as \$i=>\${$variable})
		{
			\$this->assertEquals(\${$variable}->getId(),\$result[\$i]['id']);
		}
    }
}
";
file_put_contents("$dir/DatabaseTests/{$className}ListDbTest.php",$contents);

echo "$className\n";
}

#------------------------------------------------------------------------------
# Generate the All Tests Suite
#------------------------------------------------------------------------------
$contents = "<?php
require_once 'PHPUnit/Framework.php';

require_once 'UnitTests.php';
require_once 'DatabaseTests.php';

class AllTests extends PHPUnit_Framework_TestSuite
{
	public static function suite()
	{
		\$suite = new AllTests('".APPLICATION_NAME."');
		\$suite->addTest(UnitTests::suite());
		\$suite->addTest(DatabaseTests::suite());
		return \$suite;
	}
}
";
file_put_contents("$dir/AllTests.php",$contents);

#------------------------------------------------------------------------------
# Generate the All Tests Suite
#------------------------------------------------------------------------------
$contents = "<?php\nrequire_once 'PHPUnit/Framework.php';\n\n";
foreach($classes as $className)
{
	$contents.= "require_once 'DatabaseTests/{$className}DbTest.php';\n";
	$contents.= "require_once 'DatabaseTests/{$className}ListDbTest.php';\n";
}
$contents.= "
class DatabaseTests extends PHPUnit_Framework_TestSuite
{
	protected function setUp()
	{
		\$dir = dirname(__FILE__);

		\$PDO = Database::getConnection();
		\$PDO->exec('drop database '.DB_NAME);
		\$PDO->exec('create database '.DB_NAME);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME.\" < \$dir/testData.sql\");

		\$PDO = Database::getConnection(true);
	}

	public static function suite()
	{
		\$suite = new DatabaseTests('".APPLICATION_NAME." Classes');

";
foreach($classes as $className)
{
	$contents.= "\t\t\$suite->addTestSuite('{$className}DbTest');\n";
	$contents.= "\t\t\$suite->addTestSuite('{$className}ListDbTest');\n";
}
$contents.= "
		return \$suite;
	}
}
";
file_put_contents("$dir/DatabaseTests.php",$contents);

#------------------------------------------------------------------------------
# Generate the Unit Tests Suite
#------------------------------------------------------------------------------
$contents = "<?php\nrequire_once 'PHPUnit/Framework.php';\n\n";
foreach($classes as $className)
{
	$contents.= "require_once 'UnitTests/{$className}UnitTest.php';\n";
}
$contents.= "
class UnitTests extends PHPUnit_Framework_TestSuite
{
	protected function setUp()
	{
		\$dir = dirname(__FILE__);

		\$PDO = Database::getConnection();
		\$PDO->exec('drop database '.DB_NAME);
		\$PDO->exec('create database '.DB_NAME);
		exec('/usr/local/mysql/bin/mysql -u '.DB_USER.' -p'.DB_PASS.' '.DB_NAME.\" < \$dir/testData.sql\");

		\$PDO = Database::getConnection(true);
	}

	public static function suite()
	{
		\$suite = new UnitTests('".APPLICATION_NAME." Classes');

";
foreach($classes as $className)
{
	$contents.= "\t\t\$suite->addTestSuite('{$className}UnitTest');\n";
}
$contents.= "
		return \$suite;
	}
}
";
file_put_contents("$dir/UnitTests.php",$contents);
