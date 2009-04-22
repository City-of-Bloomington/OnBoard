<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TermList $this->termList
 */
?>
<div class="interfaceBox memberList">
	<h1>Terms</h1>
	<table>
		<tr><th>Committee</th>
			<th>Appointed By</th>
			<th>Term</th>
			<th></th>
		</tr>
		<?php
			foreach ($this->termList as $term) {
				$editButton = '';
				if (userHasRole('Administrator')) {
					$url = new URL(BASE_URL.'/terms/updateTerm.php');
					$url->term_id = $term->getId();
					$url->return_url = $_SERVER['REQUEST_URI'];
					$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
				}
				$committee = $term->getSeat()->getCommittee();
				$committeeName = View::escape($committee->getName());
				$appointer = View::escape($term->getSeat()->getAppointer()->getName());
				echo "
				<tr><td><a href=\"{$committee->getURL()}\">$committeeName</a></td>
					<td>$appointer</td>
					<td>{$term->getTerm_start('n/j/Y')} - {$term->getTerm_end('n/j/Y')}</td>
					<td>$editButton</td>
				</tr>
				";
			}
		?>
	</table>
</div>
