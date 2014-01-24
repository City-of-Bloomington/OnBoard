<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->committees
 */
namespace Application\Models;

use Blossom\Classes\View;
?>
<div>
	<h2><?php
			echo sprintf('%s, %s, %s, &amp; %s',
				$this->_(['labels.council'   , 'labels.councils'   , 2]),
				$this->_(['labels.commission', 'labels.commissions', 2]),
				$this->_(['labels.committee' , 'labels.committees' , 2]),
				$this->_(['labels.board'     , 'labels.boards'     , 2])
			);
			if (Person::isAllowed('committees')) {
				echo sprintf(
					'<a class="btn" href="%s"><i class="fa fa-plus"></i> %s</a>',
					BASE_URI.'/committees/update',
					$this->_('labels.add_committee')
				);
			}
		?>
	</h2>
	<table>
		<thead>
			<tr><th></th>
				<th><?php echo $this->_('labels.name'); ?></th>
				<th><?php echo $this->_(['labels.seat','labels.seats',2]); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->committees as $c) {
				$name = View::escape($c->getName());
				$seats = $c->getMaxCurrentTerms();

				$editButton = '';
				if (Person::isAllowed('committees')) {
					$editButton = sprintf(
						'<a class="fa fa-edit" href="%s"><i class="hidden-label">%s</i></a>',
						BASE_URI.'/committees/update?committee_id='.$c->getId(),
						$this->_('labels.edit')
					);
				}
				$url = BASE_URI.'/committees/view?committee_id='.$c->getId();
				echo "
				<tr><td>$editButton</td>
					<td><a href=\"$url\">$name</a></td>
					<td>$seats</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
