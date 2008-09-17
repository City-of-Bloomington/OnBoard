<?php
/**
 * @copyright Copyright (C) 2006-2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Topic $this->topic
 * @param VoteList $this->voteList
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (isset($this->topic) && userHasRole(array('Administrator','Clerk')))
			{
				$url = new URL(BASE_URL.'/votes/addVote.php');
				$url->topic_id = $this->topic->getId();
				echo "<a class=\"add button\" href=\"$url\">Add</a>";
			}
		?>
		Votes
	</h1>
	<table>
	<?php
	    foreach($this->voteList as $vote)
		{
			$editButton = '';
			if (userHasRole(array('Administrator','Clerk')))
			{
				$url = new URL(BASE_URL.'/votes/updateVote.php');
				$url->vote_id = $vote->getId();
				$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";
			}

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
