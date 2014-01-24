<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Term $this->term
 */
$start = $this->term->getTerm_start(DATE_FORMAT);
$end   = $this->term->getTerm_end  (DATE_FORMAT);
?>
<div id="warning" class="memberVR">
	<h2>You are about to delete voting records.</h2>
	<p>These voting records are no longer valid.
		The date of the vote no longer occurs during this term.</p>
	<h2>Term: <?php echo "$start - $end"; ?></h2>
	<?php
		foreach ($this->term->getInvalidVotingRecords() as $record) {
			$position = View::escape($record->getPosition());
			switch ($position) {
				case 'yes':
				case 'no':
					$action = 'voted '.ucfirst($position);
					break;
				case 'absent':
					$action = "was Absent";
					break;
				case 'abstain':
					$action = "Abstained";
					break;
			}
			$date = $record->getDate(DATE_FORMAT);
			$number      = View::escape($record->getTopic()->getNumber());
			$description = View::escape($record->getTopic()->getDescription());
			$lastname    = View::escape($this->term->getPerson()->getLastname());
			$type        = View::escape($record->getVoteType());
			echo "
			<div class=\"topic\">
				<h3 class=\"$position\">
					$lastname $action in a $type vote on
					<a href=\"{$record->getTopic()->getUri()}\" class=\"number\">$number</a>
					<span class=\"date\">$date</span>
				</h3>
			</div>
			";
		}
	?>

	<?php
		$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);
		$url->confirm = 'true';
		$url->return_url = $this->return_url;
	?>
	<a href="<?php echo BASE_URI; ?>/terms/confirmDeleteInvalidVotingRecords?confirm=1">
		Update term and delete invalid voting records
	</a>
	<a class="btn" href="<?php echo $this->term->getSeat()->getUri(); ?>">
		<i class="fa fa-times"></i> Cancel</a>
		Cancel
	</a>
</div>
