<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
$members = new MemberList();
$members.find();
$voteOptions = new MemberVoteList();
$options.find();
?>
<h1>Update VotingRecord</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>VotingRecord Info</legend>
	<input name="id" type="hidden" value="<?php echo $this->votingRecord->getId(); ?>" />
	<table>

	<tr><td><label for="votingRecord-member_id">Member</label></td>
		<td><select name="votingRecord[member_id]" id="votingRecord-member_id">
	<?php 
		foreach($members as $member){
			if($member->getId() == $this->votingRecord->getMember_id())
		 		echo "
				<option selected=\"selected\" value=\"{$member->getId()}\">$member</option>";
			else
		 		echo "
				<option value=\"{$member->getId()}\">$member</option>";
		}
	?>
	</select></td></tr>
		
	<tr><td><label for="votingRecord-vote_id">Vote</label></td>
		<td><select name="votingRecord[memberVote_id]" id="votingRecord-memberVote_id"></td>
	<?php 
		foreach($voteOptions as $option){
			if($option->getId() == $this->votingRecord->getVote_id()){
		 		echo "
				<option selected=\"selected\" value=\"{$option->getId()}\">$option</option>";
			}
			else{
		 		echo "
				<option value=\"{$option->getId()}\">$option</option>";
			}
		}
	?>
	</select></td></tr>

		
	</table>
	<input type="hidden" name="votingRecord[vote_id]" value="<?php echo $this->votingRecord->getVote_id(); ?>" />
	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo BASE_URL; ?>/votingRecords">Cancel</a>
</fieldset>
</form>