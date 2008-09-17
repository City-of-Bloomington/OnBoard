<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 */
?>
<div class="interfaceBox">
	<h1>
		<?php
			if (userHasRole('Administrator'))
			{
				echo "<a class=\"add button\" href=\"".BASE_URL."/votes/addVote.php?topic_id=".$this->topic->getId()."\">Add</a>";
			}
		?>
		Votes
	</h1>
	<table>
	<?php
       
	    foreach($this->voteList as $vote)
		{
			$editButton = userHasRole('Administrator') ? "<a class=\"edit button\" href=\"".BASE_URL."/votes/updateVote.php?id={$vote->getId()}\">Edit</a>" : '';
	$results = array();
	
	$results[] = count($vote->getRecords('pass'));
	$results[] = count($vote->getRecords('fail'));
	$results[] = count($vote->getRecords('absent'));
	$results[] = count($vote->getRecords('abstain'));
	$results = implode('-',$results); 
			echo "
			    <tr><td>$editButton</td>
				<td>{$vote->getDate('n/j/Y')}</td>
				<td>$results</td>
				<td>{$vote->getOutcome()}</td>
			    </tr>
				";
			}
		?>
	</table>
</div>