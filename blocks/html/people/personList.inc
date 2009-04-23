<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->personList
 */
$people = array('Current Members'=>array(),
				'Past Members'=>array(),
				'Users'=>array(),
				'Others'=>array());
foreach ($this->personList as $person) {
	if (count($person->getTerms(array('current'=>time())))) {
		$people['Current Members'][] = $person;
	}
	elseif (count($person->getTerms(array('current'=>time())))) {
		$people['Past Members'][] = $person;
	}
	elseif ($person->getUser_id()) {
		$people['Users'][] = $person;
	}
	else {
		$people['Others'][] = $person;
	}
}
?>
<div class="interfaceBox">
<?php
	$addButton = '';
	if (userHasRole('Administrator')) {
		$addButton = "
		<a class=\"add button\" href=\"".BASE_URL."/people/addPerson.php\" title=\"Add a person\">
			Add
		</a>
		";
	}
	foreach (array_keys($people) as $key) {
		if ((in_array($key,array('Current Members','Past Members'))
			&& count($people[$key]))
			|| userHasRole(array('Administrator','Clerk'))) {
			echo "
			<h1>$addButton$key</h1>
			<table>
			";
			foreach ($people[$key] as $person) {
				$editButton = '';
				if (userHasRole('Administrator')) {
					$url = new URL(BASE_URL.'/people/updatePerson.php');
					$url->person_id = $person->getId();
					$editButton = "
					<a class=\"edit button\" href=\"$url\" title=\"Edit Person\">
						Edit
					</a>
					";
				}

				$name = View::escape($person->getFullname());
				echo "
				<tr><td>$editButton</td>
					<td><a href=\"{$person->getURL()}\">$name</a></td>
					<td>{$person->getEmail()}</td>
				</tr>
				";
			}
			echo "</table>";
		}
	}
?>
</div>
