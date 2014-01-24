<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Vote $this->vote
 */
use Application\Models\Person;
use Blossom\Classes\View;

$type = View::escape($this->vote->getVoteType());
$date = $this->vote->getDate(DATE_FORMAT);
$outcome = ucfirst($this->vote->getOutcome());

$editButton = '';
if (Person::isAllowed('votes', 'edit')) {
	$editButton = sprintf(
		'<a class="btn" href="%s"><i class="fa fa-edit"></i> %s</a>',
		BASE_URI.'/votes/update?vote_id='.$this->vote->getId(),
		$this->translate('labels.edit_vote')
	);
}
?>
<div>
	<h2><?php echo "$editButton Vote: $type $date $outcome"; ?></h2>
</div>
