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
	 * Generate the list block
	 */
	$getId = "get".ucwords($key['Column_name']);
	$HTML = "<div class=\"interfaceBox\">
	<h1>
		<?php
			if (userHasRole('Administrator')) {
				echo \"<a class=\\\"add button\\\" href=\\\"\".BASE_URL.\"/$tableName/add$className.php\\\">Add</a>\";
			}
		?>
		{$className}s
	</h1>
	<ul><?php
			foreach (\$this->{$variableName}List as \${$variableName}) {
				\$editButton = '';
				if (userHasRole('Administrator')) {
					\$url = new URL(BASE_URL.'/$tableName/update$className.php');
					\$url->$key[Column_name] = \${$variableName}->{$getId}();
					\$editButton = \"<a class=\\\"edit button\\\" href=\\\"\$url\\\">Edit</a>\";
				}
				echo \"<li>\$editButton \$$variableName</li>\";
			}
		?>
	</ul>
</div>";

$contents = "<?php\n";
$contents.= COPYRIGHT;
$contents.="
?>
$HTML";

	$dir = APPLICATION_HOME."/scripts/stubs/blocks/$tableName";
	if (!is_dir($dir)) {
		mkdir($dir,0770,true);
	}
	file_put_contents("$dir/{$variableName}List.inc",$contents);


/**
 * Generate the addForm
 */
$HTML = "<h1>Add $className</h1>
<form method=\"post\" action=\"<?php echo \$_SERVER['SCRIPT_NAME']; ?>\">
	<fieldset><legend>$className Info</legend>
		<table>
";
		foreach ($fields as $field) {
			if ($field['Field'] != $key['Column_name']) {
				$fieldFunctionName = ucwords($field['Field']);
				switch ($field['Type']) {
					case 'date':
					$HTML.="
			<tr><td><label for=\"{$variableName}-$field[Field]-mon\">$field[Field]</label></td>
				<td><select name=\"{$variableName}[$field[Field]][mon]\" id=\"{$variableName}-$field[Field]-mon\">
						<option></option>
						<?php
							\$now = getdate();
							for (\$i=1; \$i<=12; \$i++) {
								\$selected = (\$i==\$now['mon']) ? 'selected=\"selected\"' : '';
								echo \"<option \$selected>\$i</option>\";
							}
						?>
					</select>
					<select name=\"{$variableName}[$field[Field]][mday]\">
						<option></option>
						<?php
							for (\$i=1; \$i<=31; \$i++) {
								\$selected = (\$i==\$now['mday']) ? 'selected=\"selected\"' : '';
								echo \"<option \$selected>\$i</option>\";
							}
						?>
					</select>
					<input name=\"{$variableName}[$field[Field]][year]\" id=\"{$variableName}-$field[Field]-year\" size=\"4\" maxlength=\"4\" value=\"<?php echo \$now['year']; ?>\" />
				</td>
			</tr>";
						break;

					case 'datetime':
					case 'timestamp':
					$HTML.="
			<tr><td><label for=\"{$variableName}-$field[Field]-mon\">$field[Field]</label></td>
				<td><select name=\"{$variableName}[$field[Field]][mon]\" id=\"{$variableName}-$field[Field]-mon\">
						<option></option>
						<?php
							\$now = getdate();
							for (\$i=1; \$i<=12; \$i++) {
								\$selected = (\$i==\$now['mon']) ? 'selected=\"selected\"' : '';
								echo \"<option \$selected>\$i</option>\";
							}
						?>
					</select>
					<select name=\"{$variableName}[$field[Field]][mday]\">
						<option></option>
						<?php
							for (\$i=1; \$i<=31; \$i++) {
								\$selected = (\$i==\$now['mday']) ? 'selected=\"selected\"' : '';
								echo \"<option \$selected>\$i</option>\";
							}
						?>
					</select>
					<input name=\"{$variableName}[$field[Field]][year]\" id=\"{$variableName}-$field[Field]-year\" size=\"4\" maxlength=\"4\" value=\"<?php echo \$now['year']; ?>\" />
					<select name=\"{$variableName}[$field[Field]][hours]\" id=\"{$variableName}-$field[Field]-hours\">
						<?php
							for (\$i=0; \$i<=23; \$i++) {
								\$selected = (\$i==\$now['hours']) ? 'selected=\"selected\"' : '';
								echo \"<option \$selected>\$i</option>\";
							}
						?>
					</select>
					<select name=\"{$variableName}[$field[Field]][minutes]\" id=\"{$variableName}-$field[Field]-minutes\">
						<?php
							for (\$i=0; \$i<=59; \$i+=15) {
								\$selected = (\$i==\$now['minutes']) ? 'selected=\"selected\"' : '';
								echo \"<option \$selected>\$i</option>\";
							}
						?>
					</select>
				</td>
			</tr>";
						break;

					case 'text':
				$HTML.= "
			<tr><td><label for=\"{$variableName}-$field[Field]\">$field[Field]</label></td>
				<td><textarea name=\"{$variableName}[$field[Field]]\" id=\"{$variableName}-$field[Field]\" rows=\"3\" cols=\"60\"></textarea>
				</td>
			</tr>
				";
						break;

					default:
				$HTML.= "
			<tr><td><label for=\"{$variableName}-$field[Field]\">$field[Field]</label></td>
				<td><input name=\"{$variableName}[$field[Field]]\" id=\"{$variableName}-$field[Field]\" />
				</td>
			</tr>
				";
				}
			}
		}
	$HTML.= "
		</table>

		<button type=\"submit\" class=\"submit\">Submit</button>
		<button type=\"button\" class=\"cancel\" onclick=\"document.location.href='<?php echo BASE_URL; ?>/{$variableName}s';\">
			Cancel
		</button>
	</fieldset>
</form>";

$contents = "<?php\n";
$contents.= COPYRIGHT;
$contents.="
?>
$HTML";
file_put_contents("$dir/add{$className}Form.inc",$contents);

/**
 * Generate the Update Form
 */
$HTML = "<h1>Update $className</h1>
<form method=\"post\" action=\"<?php echo \$_SERVER['SCRIPT_NAME']; ?>\">
	<fieldset><legend>$className Info</legend>
		<input name=\"$key[Column_name]\" type=\"hidden\" value=\"<?php echo \$this->{$variableName}->{$getId}(); ?>\" />
		<table>
";
		foreach ($fields as $field) {
			if ($field['Field'] != $key['Column_name']) {
				$fieldFunctionName = ucwords($field['Field']);
				switch ($field['Type']) {
					case 'date':
					$HTML.="
			<tr><td><label for=\"{$variableName}-$field[Field]-mon\">$field[Field]</label></td>
				<td><select name=\"{$variableName}[$field[Field]][mon]\" id=\"{$variableName}-$field[Field]-mon\">
						<option></option>
						<?php
							\$$field[Field] = \$this->{$variableName}->dateStringToArray(\$this->{$variableName}->get$fieldFunctionName());
							for (\$i=1; \$i<=12; \$i++) {
								\$selected = (\$i==\$$field[Field]['mon']) ? 'selected=\"selected\"' : '';
								echo \"<option \$selected\">\$i</option>\";
							}
						?>
					</select>
					<select name=\"{$variableName}[$field[Field]][mday]\">
						<option></option>
						<?php
							for (\$i=1; \$i<=31; \$i++) {
								\$selected = (\$i==\$$field[Field]['mday']) ? 'selected=\"selected\"' : '';
								echo \"<option \$selected>\$i</option>\";
							}
						?>
					</select>
					<input name=\"{$variableName}[$field[Field]][year]\" id=\"{$variableName}-$field[Field]-year\" size=\"4\" maxlength=\"4\" value=\"<?php echo \$$field[Field]['year']; ?>\" />
				</td>
			</tr>";
						break;

					case 'datetime':
					case 'timestamp':
					$HTML.="
			<tr><td><label for=\"{$variableName}-$field[Field]-mon\">$field[Field]</label></td>
				<td><select name=\"{$variableName}[$field[Field]][mon]\" id=\"{$variableName}-$field[Field]-mon\">
						<option></option>
						<?php
							\$$field[Field] = \$this->{$variableName}->dateStringToArray(\$this->{$variableName}->get$fieldFunctionName());
							for (\$i=1; \$i<=12; \$i++) {
								\$selected = (\$i==\$$field[Field]['mon']) ? 'selected=\"selected\"' : '';
								echo \"<option \$selected>\$i</option>\";
							}
						?>
					</select>
					<select name=\"{$variableName}[$field[Field]][mday]\">
						<option></option>
						<?php
							for (\$i=1; \$i<=31; \$i++) {
								\$selected = (\$i==\$$field[Field]['mday']) ? 'selected=\"selected\"' : '';
								echo \"<option \$selected>\$i</option>\";
							}
						?>
					</select>
					<input name=\"{$variableName}[$field[Field]][year]\" id=\"{$variableName}-$field[Field]-year\" size=\"4\" maxlength=\"4\" value=\"<?php echo \$$field[Field]['year']; ?>\" />
					<select name=\"{$variableName}[$field[Field]][hours]\" id=\"{$variableName}-$field[Field]-hours\">
					<?php
						for (\$i=0; \$i<=23; \$i++) {
							\$selected = (\$i==\$$field[Field]['hours']) ? 'selected=\"selected\"' : '';
							echo \"<option \$selected>\$i</option>\";
						}
					?>
					</select>
					<select name=\"{$variableName}[$field[Field]][minutes]\" id=\"{$variableName}-$field[Field]-minutes\">
					<?php
						for (\$i=0; \$i<=59; \$i+=15) {
							\$selected = (\$i==\$$field[Field]['minutes']) ? 'selected=\"selected\"' : '';
							echo \"<option \$selected>\$i</option>\";
						}
					?>
					</select>
				</td>
			</tr>";
						break;

					case 'text':
				$HTML.= "
			<tr><td><label for=\"{$variableName}-$field[Field]\">$field[Field]</label></td>
				<td><textarea name=\"{$variableName}[$field[Field]]\" id=\"{$variableName}-$field[Field]\" rows=\"3\" cols=\"60\"><?php echo \$this->{$variableName}->get$fieldFunctionName(); ?></textarea>
				</td>
			</tr>
				";
						break;

					default:
				$HTML.= "
			<tr><td><label for=\"{$variableName}-$field[Field]\">$field[Field]</label></td>
				<td><input name=\"{$variableName}[$field[Field]]\" id=\"{$variableName}-$field[Field]\" value=\"<?php echo \$this->{$variableName}->get$fieldFunctionName(); ?>\" />
				</td>
			</tr>
				";
				}
			}
		}
	$HTML.= "
		</table>

		<button type=\"submit\" class=\"submit\">Submit</button>
		<button type=\"button\" class=\"cancel\" onclick=\"document.location.href='<?php echo BASE_URL; ?>/{$variableName}s';\">
			Cancel
		</button>
	</fieldset>
</form>";
$contents = "<?php\n";
$contents.= COPYRIGHT;
$contents.="
?>
$HTML";
file_put_contents("$dir/update{$className}Form.inc",$contents);

echo "$className\n";
}
