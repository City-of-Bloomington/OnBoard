<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->committees
 */
namespace Application\Models;

use Application\Templates\Helpers\ButtonLink;

$userCanEdit = Person::isAllowed('committees', 'edit');
?>
<div>
	<h2><?php
			echo sprintf('%s, %s, %s, &amp; %s',
				$this->_(['council'   , 'councils'   , 2]),
				$this->_(['commission', 'commissions', 2]),
				$this->_(['committee' , 'committees' , 2]),
				$this->_(['board'     , 'boards'     , 2])
			);
			if ($userCanEdit) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/committees/update',
					$this->_('committee_add'),
					'add'
				);
			}
		?>
	</h2>
	<table>
		<thead>
			<tr><th></th>
				<th><?= $this->_('name'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->committees as $c) {
				$name = self::escape($c->getName());

				$editButton = '';
				if ($userCanEdit) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/committees/update?committee_id='.$c->getId(),
						$this->_('committee_edit'),
						'edit',
						ButtonLink::SIZE_ICON
					);
				}
				$url = BASE_URI.'/committees/view?committee_id='.$c->getId();
				echo "
				<tr><td>$editButton</td>
					<td><a href=\"$url\">$name</a></td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
