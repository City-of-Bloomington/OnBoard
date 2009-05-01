<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Seat $this->seat
 */
?>
<h1>New Term</h1>
<form method="get" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Find a person</legend>
		<?php
			foreach ($this->vars as $field=>$value) {
				if (is_string($value)) {
					$value = View::escape($value);
					echo "
					<input type=\"hidden\" name=\"$field\" value=\"$value\" />
					";
				}
			}
			$firstname = isset($_GET['firstname']) ? View::escape($_GET['firstname']) : '';
			$lastname = isset($_GET['lastname']) ? View::escape($_GET['lastname']) : '';
		?>
		<table>
			<tr><td><label for="firstname">Firstname</label></td>
				<td><input name="firstname" id="firstname" value="<?php echo $firstname; ?>" />
				</td>
			</tr>
			<tr><td><label for="lastname">Lastname</label></td>
				<td><input name="lastname" id="lastname" value="<?php echo $lastname; ?>" />
				</td>
			</tr>
		</table>
		<button type="submit" class="search">Search</button>
	</fieldset>
</form>
<?php
	if (isset($_GET['firstname']) || isset($_GET['lastname'])) {
		$personList = new PersonList();
		$personList->search(array('firstname'=>$_GET['firstname'],'lastname'=>$_GET['lastname']));

		$url = new URL(BASE_URL.'/terms/addTerm.php');
		$url->seat_id = $this->seat_id;

		if (count($personList)) {
			echo "
			<div class=\"interfaceBox results\">
				<h1>Results</h1>
				<table>
			";
				foreach ($personList as $person) {
					$url->person_id = $person->getId();

					$name = View::escape($person->getFullname());
					$committees = array();
					foreach ($person->getCommittees() as $committee) {
						$committees[] = $committee->getName();
					}
					$committees = View::escape(implode(', ',$committees));
					echo "
					<tr><td><a href=\"$url\">$name</a></td>
						<td>$committees</td>
					</tr>
					";
				}
			$url->person_id = null;
			echo "
				</table>
				<button type=\"button\" onclick=\"document.location.href='$url';\">
					Add New Person
				</button>
			</div>
			";
		}
		else {
			$url->person_id = null;
			echo "
			<div class\"interfaceBox results\">
				<h1>Results</h1>
				<p>No People Found</p>
				<button type=\"button\" onclick=\"document.location.href='$url';\">
					Add New Person
				</button>
			</div>
			";
		}
	}
?>
