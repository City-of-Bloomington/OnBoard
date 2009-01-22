<?php
/**
 * @copyright 2006-2008 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Vote $this->vote
 */
?>
<div class="interfaceBox">
	<h1>
		<?php
			echo View::escape($this->vote->getVoteType());
			if (userHasRole(array('Administrator','Clerk')))
			{
				$url = new URL(BASE_URL.'/votes/recordVotes.php');
				$url->vote_id = $this->vote->getId();
				echo "<button type=\"button\" onclick=\"document.location.href='$url';\">Enter Votes</button>";
			}
		?>
	</h1>
	<table>
	<?php
		foreach ($this->vote->getVotingRecords() as $votingRecord)
		{
			$fullname = View::escape($votingRecord->getMember()->getFullname());
			echo "
			<tr><td><a href=\"{$votingRecord->getMember()->getURL()}\">
					$fullname</a>
				</td>
				<td>{$votingRecord->getMemberVote()}</td>
			</tr>
			";
		}
	?>
	</table>
</div>
