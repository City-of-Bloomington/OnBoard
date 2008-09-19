<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
$members = new MemberList();
$members.find();
$voteOptions = array('yes','no','abstain','absent');

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
		for($i;$i<count($voteOptions);$i++){
			if($voteOptions[i] == $this->votingRecord->getMemeberVote()){
		 		echo "
				<option selected=\"selected\">$option</option>";
			}
			else{
		 		echo "
				<option>$option</option>";
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