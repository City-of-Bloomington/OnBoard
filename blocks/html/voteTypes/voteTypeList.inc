<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param VoteTypeList $this->voteTypeList
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userHasRole('Administrator')) {
				echo "
				<a class=\"add button\" href=\"".BASE_URL."/voteTypes/addVoteType.php\">
					Add
				</a>
				";
			}
		?>
		VoteTypes
	</h1>
	<table>
		<?php
			foreach ($this->voteTypeList as $voteType) {
				$editButton = '';
				if (userHasRole('Administrator')) {
					$url = new URL(BASE_URL.'/voteTypes/updateVoteType.php');
					$url->voteType_id = $voteType->getId();
					$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
				}

				$name = View::escape($voteType->getName());
				echo "
				<tr><th>$editButton{$voteType->getOrdering()}</th>
					<td>$name</td>
				</tr>
				";
			}
		?>
	</table>
</div>
