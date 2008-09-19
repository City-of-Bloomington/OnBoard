<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
$members = new MemberList();
$members.find();
$voteOptions = array('yes','no','abstain','absent');
?>
<h1>Add VotingRecord</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>VotingRecord Info</legend>
	<table>
	<tr><td><label for="votingRecord-member_id">Member</label></td>
		<td><select name="votingRecord[member_id]" id="votingRecord-member_id">
	<?php 
		foreach($members as $member){
		 	echo "
				<option value=\"{$member->getId()}\">$member</option>";
		}
	?>
	</select></td></tr>	
		
	<input type="hidden" name="votingRecord[vote_id]" value="<?php echo $this->vote->getId(); ?>" /></td></tr>
		
	<tr><td><label for="votingRecord-memberVote">Vote</label></td>
		<td><select name="votingRecord[memberVote]" id="votingRecord-memberVote">
	<?php 
		for($i;$i<count($voteOptions);$i++){
		 	echo "
				<option>{$option[i]}</option>";
		}
	?>
	</select></td></tr>
	</table>

	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo BASE_URL; ?>/votingRecords">Cancel</a>
</fieldset>
</form>