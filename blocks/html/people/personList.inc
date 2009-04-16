<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->personList
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userHasRole('Administrator')) {
				echo "
				<a class=\"add button\" href=\"".BASE_URL."/people/addPerson.php\" title=\"Add a person\">
					Add
				</a>
				";
			}
		?>
		People
	</h1>
	<table>
		<?php
			foreach ($this->personList as $person) {
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
					<td>{$person->getUsername()}</td>
					<td><a href=\"{$person->getURL()}\">$name</a></td>
					<td>{$person->getEmail()}</td>
				</tr>
				";
			}
		?>
	</table>
</div>
