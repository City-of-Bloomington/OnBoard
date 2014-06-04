<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Vote $this->vote
 */
use Application\Models\Person;
use Blossom\Classes\View;
?>
<div>
	<h2><?php
			echo View::escape($this->vote->getVoteType());

			if (Person::isAllowed('votes', 'edit')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/votes/recordVotes?vote_id='.$this->vote->getId(),
					$this->translate('labels.edit_votingRecords'),
					'edit'
				);
			}
		?>
	</h2>
	<table>
	<?php
		foreach ($this->vote->getVotingRecords() as $votingRecord) {
			$fullname = View::escape($votingRecord->getPerson()->getFullname());
			echo "
			<tr><td><a href=\"{$votingRecord->getPerson()->getUri()}\">$fullname</a></td>
				<td>{$votingRecord->getPosition()}</td>
			</tr>
			";
		}
	?>
	</table>
</div>
