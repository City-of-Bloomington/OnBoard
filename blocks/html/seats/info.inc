<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Seat $this->seat
 */
use Application\Models\Person;
use Blossom\Classes\View;

$name      = View::escape($this->seat->getName());
$appointer = View::escape($this->seat->getAppointer());
$committee = View::escape($this->seat->getCommittee()->getName());


$addTermButton = '';
$editSeatButton = '';
$editRequirementsButton = '';
if (Person::isAllowed('seats', 'edit')) {
	$addTermButton = sprintf(
		'<a class="btn" href="%s"><i class="fa fa-plus"></i> %s</a>',
		BASE_URI.'/terms/update?seat_id='.$this->seat->getId(),
		$this->translate('labels.add_term')
	);
	$editSeatButton = sprintf(
		'<a class="btn" href="%s"><i class="fa fa-edit"></i> %s</a>',
		BASE_URI.'/seats/update?seat_id='.$this->seat->getId(),
		$this->translate('labels.edit_seat')
	);
	$editRequirementsButton = sprintf(
		'<a class="btn" href="%s"><i class="fa fa-edit"></i> %s</a>',
		BASE_URI.'/seats/updateRequirements?seat_id='.$this->seat->getId(),
		$this->translate('labels.edit')
	);
}
echo "
<div>
	<h2>$name $editSeatButton $addTermButton</h2>
	<table>
		<tr><th>{$this->_('labels.appointed_by')}</th>
			<td>$appointer</td>
		</tr>
		<tr><th>{$this->_(['labels.committee', 'labels.committees', 1])}</th>
			<td>$committee</td>
		</tr>
		<tr><th>{$this->_('labels.maxCurrentTerms')}</th>
			<td>{$this->seat->getMaxCurrentTerms()}</td>
		</tr>
		<tr><th>{$this->_(['labels.requirement', 'labels.requirements', 2])}
			</th>
			<td>$editRequirementsButton

";
				if ($this->seat->hasRequirements()) {
					echo '<ul>';
					foreach ($this->seat->getRequirements() as $requirement) {
						echo '<li>'.View::escape($requirement).'</li>';
					}
					echo '</ul>';
				}
echo "
			</td>
		</tr>
	</table>
</div>
";
