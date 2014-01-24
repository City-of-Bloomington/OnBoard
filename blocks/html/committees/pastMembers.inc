<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Blossom\Classes\View;
?>
<div>
	<h2><?php echo $this->_('labels.past_members'); ?></h2>
	<table>
		<?php
			foreach ($this->committee->getPeople() as $person) {
				$fullname = View::escape($person->getFullname());
				$earliestStart = time();
				$latestEnd = null;

				$terms = $person->getTerms(['committee_id'=>$this->committee->getId()]);
				foreach ($terms as $term) {
					$termStart = $term->getTerm_start('U');
					$termEnd   = $term->getTerm_end  ('U');
					if ($termStart < $earliestStart) {
						$earliestStart = $termStart;
					}
					if (!$latestEnd || $termEnd > $latestEnd) {
						$latestEnd = $termEnd;
					}
				}
				$earliestStart = date(DATE_FORMAT, $earliestStart);
				$latestEnd = $latestEnd ? date(DATE_FORMAT, $latestEnd) : '';

				$offices = array();
				foreach ($person->getOffices($this->committee) as $office) {
					$offices[] = View::escape($office->getTitle());
				}
				$offices = implode(',',$offices);

				echo "
				<tr><td><a href=\"{$person->getUri()}\">$fullname</a></td>
					<td>$earliestStart - $latestEnd</td>
					<td>$offices</td>
				</tr>
				";
			}
		?>
	</table>
	<div>
		<a href="<?php echo $this->committee->getUri(); ?>">
			<?php echo $this->_('labels.view_current_members'); ?>
		</a>
	</div>
</div>
