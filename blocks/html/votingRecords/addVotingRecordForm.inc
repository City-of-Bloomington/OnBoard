<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */

$voteOptions = array('yes','no','abstain','absent');
$topic = $this->vote->getTopic();
$committee_id = $topic->getCommittee_id();
$members = new MemberList();
$members->find(array('committee_id'=>$committee_id));
?>
<h1>Add Voting Records</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>VotingRecord Info</legend>
	<table>
	<tr><th>Members</th><th>Yes</th><th>No</th><th>Abstain</th><th>Absent</th></tr>
	<?php 
		foreach($members as $member){
			$name = $member;
			$id= $member->getId();
			echo "
				<tr><td><label>
		 		$name</label></td>
				<td><input type=\"radio\" name=\"$id\" value='yes'>Yes</td>
 				<td><input type=\"radio\" name=\"$id\" value='no'>No</td>
				<td><input type=\"radio\" name=\"$id\" value='abstain'>Abstain</td>
				<td><input type=\"radio\" name=\"$id\" value='absent'>Absent</td>
			</tr>";
		}
	?>
		
	<input type="hidden" name="votingRecord[vote_id]" value="<?php echo $this->vote->getId(); ?>" /></td></tr>
		

	</table>

	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo BASE_URL; ?>/votingRecords">Cancel</a>
</fieldset>
</form>