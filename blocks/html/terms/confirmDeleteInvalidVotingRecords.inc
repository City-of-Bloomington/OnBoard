<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Term $this->term
 * @param string $this->return_url
 */
$start = $this->term->getTerm_start('n/j/Y');
$end = $this->term->getTerm_end('n/j/Y');
?>
<h1>Update Vote</h1>
<div id="warning" class="memberVR">
	<h1>You are about to delete voting records.</h1>
	<p>These voting records are no longer valid.
		The date of the vote no longer occurs during this term.</p>
	<h2>Term: <?php echo "$start - $end"; ?></h2>
	<table>
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
				$number = View::escape($record->getTopic()->getNumber());
				$date = $record->getDate('n/j/Y');
				$description = View::escape($record->getTopic()->getDescription());
				$lastname = View::escape($this->term->getPerson()->getLastname());
				$type = View::escape($record->getVoteType());
				echo "
				<div class=\"topic\">
					<h3 class=\"$position\">
						$lastname $action in a $type vote on
						<a href=\"{$record->getTopic()->getURL()}\" class=\"number\">$number</a>
						<span class=\"date\">$date</span>
					</h3>
				</div>
				";
			}
		?>
	</table>

	<?php
		$url = new URL($_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);
		$url->confirm = 'true';
		$url->return_url = $this->return_url;
	?>
	<button type="button" onclick="document.location.href='<?php echo $url; ?>';">
		Update term and delete invalid voting records
	</button>
	<button type="button" class="cancel" onclick="document.location.href='<?php echo $this->return_url; ?>';">
		Cancel
	</button>
</div>
