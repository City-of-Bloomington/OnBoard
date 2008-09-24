<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
$list = $this->votingRecordList;
?>

<h1>Add/Edit Voting Records</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<fieldset><legend>Voting Record Info</legend>
	<table>
	<tr><th>Members</th><th>Yes</th><th>No</th><th>Abstain</th><th>Absent</th></tr>
	<?php 
		foreach($list as $votingRecord){
			$id = $votingRecord->getId();
			$vote_id = $votingRecord->getVote_id();
			$member = $votingRecord->getMember();
			$voice = $votingRecord->getMemberVote();
			echo "
			<tr>
				<td><label>$member</label></td>
				<td><input type=\"radio\" name=\"votingRecord[id][$id]\" value='yes' "; 
			if($voice == 'yes') echo "checked='cheched'"; 
			echo ">Yes</td><td><input type=\"radio\" name=\"votingRecord[id][$id]\" value='no' "; 
			if($voice == 'no') echo "checked='checked'"; 
			echo ">No</td>
			     <td><input type=\"radio\" name=\"votingRecord[id][$id]\" value='abstain' "; if($voice == 'abstain') echo "checked='checked'"; 
			echo ">Abstain</td>
				<td><input type=\"radio\" name=\"votingRecord[id][$id]\" value='absent' "; if($voice == 'absent') echo "checked='checked'"; 
			echo ">Absent</td></tr>";
		}
	?>
	<input type="hidden" name="votingRecord[vote_id]" value="<?php echo $this->vote->getId(); ?>" /></td></tr>		
	</table>

	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo BASE_URL; ?>/votingRecords">Cancel</a>
</fieldset>
</form>