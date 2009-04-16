<?php
/**
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
		$type = preg_replace("/[^a-z]/","",$row['Type']);

		// Translate any MySQL datatype names into PHP datatype names
		if (preg_match('/int/',$type)) {
			$type = 'int';
		}
		if (preg_match('/enum/',$type) || preg_match('/varchar/',$type)) {
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
	$variableName = Inflector::singularize($tableName);

/**
 * Generate home.php
 */
$PHP = "
\${$variableName}List = new {$className}List();
\${$variableName}List->find();

\$template = new Template();
\$template->blocks[] = new Block('{$variableName}s/{$variableName}List.inc',array('{$variableName}List'=>\${$variableName}List));
echo \$template->render();";

$contents = "<?php\n";
$contents.= COPYRIGHT."\n";
$contents.= $PHP;

	$dir = APPLICATION_HOME."/scripts/stubs/html/$tableName";
	if (!is_dir($dir)) {
		mkdir($dir,0770,true);
	}
	file_put_contents("$dir/home.php",$contents);

/**
 * Generate the Add controller
 */
$PHP = "
verifyUser('Administrator');

if (isset(\$_POST['{$variableName}'])) {
	\${$variableName} = new {$className}();
	foreach (\$_POST['{$variableName}'] as \$field=>\$value) {
		\$set = 'set'.ucfirst(\$field);
		\${$variableName}->\$set(\$value);
	}

	try {
		\${$variableName}->save();
		header('Location: '.BASE_URL.'/$tableName');
		exit();
	}
	catch(Exception \$e) {
		\$_SESSION['errorMessages'][] = \$e;
	}
}

\$template = new Template();
\$template->blocks[] = new Block('{$variableName}s/add{$className}Form.inc');
echo \$template->render();";
$contents = "<?php\n";
$contents.= COPYRIGHT."\n";
$contents.= $PHP;
	file_put_contents("$dir/add{$className}.php",$contents);


/**
 * Generate the Update controller
 */
$PHP = "
verifyUser('Administrator');

\${$variableName} = new {$className}(\$_REQUEST['$key[Column_name]']);
if (isset(\$_POST['$variableName'])) {
	foreach (\$_POST['$variableName'] as \$field=>\$value) {
		\$set = 'set'.ucfirst(\$field);
		\${$variableName}->\$set(\$value);
	}

	try {
		\${$variableName}->save();
		header('Location: '.BASE_URL.'/$tableName');
		exit();
	}
	catch (Exception \$e) {
		\$_SESSION['errorMessages'][] = \$e;
	}
}

\$template = new Template();
\$template->blocks[] = new Block('{$variableName}s/update{$className}Form.inc',array('{$variableName}'=>\${$variableName}));
echo \$template->render();";
$contents = "<?php\n";
$contents.= COPYRIGHT."\n";
$contents.= $PHP;
	file_put_contents("$dir/update{$className}.php",$contents);
	echo "$className\n";
}
