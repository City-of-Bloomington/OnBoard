<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Vote $this->vote
 */
?>
<h1>Update Vote</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="yui-skin-sam">
<fieldset><legend>Vote Info</legend>
	<input name="vote_id" type="hidden" value="<?php echo $this->vote->getId(); ?>" />

	<table>
	<tr><td><label for="vote-date">Date</label></td>
			<input name="vote[date]"
					id="vote-date"
					size="10"
					maxlength="10"
					value="<?php echo $this->vote->getDate('n/j/Y'); ?>"
					onfocus="FRAMEWORK.popupCalendar(this);" />
		</td>
	</tr>
	<tr><td><label for="vote-voteType_id">Type</label></td>
		<td><select name="vote[voteType_id]" id="vote-voteType_id">
			<?php
				$types = new VoteTypeList()
				$types->find();
				foreach($types as $type) {
					$name = View::escape($type);
					$selected = $this->vote->getVoteType_id()==$type->getId() ? ' selected="selected"' : '';
					echo "<option value=\"{$type->getId()}\"$selected>$name</option>";
				}
			?>
			</select>
		</td>
	</tr>

	</table>

	<button type="submit" class="submit">Submit</button>
	<a class="cancel button" href="<?php echo $this->vote->getTopic()->getURL(); ?>">Cancel</a>
</fieldset>
</form>
