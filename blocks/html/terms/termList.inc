<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TermList $this->termList
 */
$tableRows = array('current'=>'','past'=>'');

foreach ($this->termList as $term) {
	$editButton = '';
	$deleteButton = '';
	if (userHasRole(array('Administrator','Clerk'))) {
		$url = new URL(BASE_URL.'/terms/updateTerm.php');
		$url->term_id = $term->getId();
		$url->return_url = $_SERVER['REQUEST_URI'];
		$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";

		if ($term->isSafeToDelete()) {
			$url = new URL(BASE_URL.'/terms/deleteTerm.php');
			$url->term_id = $term->getId();
			$url->return_url = $_SERVER['REQUEST_URI'];
			$deleteButton = "<a class=\"delete button\" href=\"$url\">Delete</a>";
		}
	}
	$personName = View::escape($term->getPerson()->getFullname());
	$committee = $term->getSeat()->getCommittee();
	$committeeName = View::escape($committee->getName());
	$appointer = View::escape($term->getSeat()->getAppointer()->getName());

	// We want to display the terms as two tables...One for current, and one for past
	// Split the rows up into current and past terms.
	$now = time();
	$table = ($term->getTerm_start() <= $now && $now <= $term->getTerm_end())
			? 'current'
			: 'past';
	$tableRows[$table].= "
	<tr><td><a href=\"{$term->getPerson()->getURL()}\">$personName</a></td>
		<td><a href=\"{$committee->getURL()}\">$committeeName</a></td>
		<td>$appointer</td>
		<td>{$term->getTerm_start('n/j/Y')} - {$term->getTerm_end('n/j/Y')}</td>
		<td>$editButton $deleteButton</td>
	</tr>
	";
}
?>
<div class="interfaceBox memberList">
	<?php
		if ($tableRows['current']) {
			echo "
			<h1>Current Terms</h1>
			<table>
			<thead>
				<tr>
					<th></th>
					<th>Committee</th>
					<th>Appointed By</th>
					<th>Term</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tableRows[current]
			</tbody></table>
			";
		}

		if ($tableRows['past']) {
			echo "
			<h1>Past Terms</h1>
			<table>
			<tbody>
				<tr><th></th>
					<th>Committee</th>
					<th>Appointed By</th>
					<th>Term</th>
					<th></th>
				</tr>
				$tableRows[past]
			</tbody>
			</table>
			";
		}
	?>
</div>
