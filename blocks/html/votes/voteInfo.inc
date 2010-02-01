<?php
/**
 * @copyright 2008-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->topic
 */
$type = View::escape($this->vote->getVoteType());
$date = $this->vote->getDate('n/j/Y');
$outcome = ucfirst($this->vote->getOutcome());

$editButton = '';
if (userHasRole(array('Administrator','Clerk'))) {
	$url = new URL(BASE_URL.'/votes/updateVote.php');
	$url->vote_id = $this->vote->getId();
	$editButton = "<button type =\"button\" class=\"edit\" nclick=\"document.location.href='$url';\">
	Edit
	</a>";
}
?>

<div class="interfaceBox">
	<h1><?php echo "$editButton Vote: $type $date $outcome"; ?></h1>
</div>
