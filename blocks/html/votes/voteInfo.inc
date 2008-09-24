<?php
/**
 * @copyright Copyright (C) 2008 City of Bloomington, Indiana. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->topic
 */
$type = View::escape($this->vote->getVoteType());
$date = $this->vote->getDate('n/j/Y');
$outcome = $this->vote->getOutcome();
if($outcome) $outcome = ucfirst($outcome);

$editButton = '';
$viewInto ='';
if (userHasRole(array('Administrator','Clerk')))
	{
		$url = new URL(BASE_URL.'/votes/updateVote.php');
		$url->vote_id = $this->vote->getId();
		$editButton = "<a class=\"edit button\" href=\"$url\">Edit</a>";		
	}

	$url = new URL(BASE_URL.'/votes/viewVote.php');
	$url->vote_id = $this->vote->getId();

?>

<div class="interfaceBox">
	<h1><?php echo "$editButton Vote: "; echo "$type $date $outcome"; ?></h1>
</div>
