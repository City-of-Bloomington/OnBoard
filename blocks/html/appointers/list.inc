<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->appointers
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;
?>
<div>
	<h2><?php
			echo $this->translate(['labels.appointer','labels.appointers',2]);

			$helper = $this->template->getHelper('buttonLink');
			if (Person::isAllowed('appointers')) {
				echo $helper->buttonLink(
					BASE_URI.'/appointers/update',
					$this->translate('labels.add_appointer'),
					'add'
				);
			}
		?>
	</h2>
	<table>
		<?php
			foreach ($this->appointers as $appointer) {
				$editButton = '';
				if (Person::isAllowed('appointers')) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/appointers/update?appointer_id='.$appointer->getId(),
						$this->translate('labels.edit'),
						'edit',
						ButtonLink::SIZE_ICON
					);
				}

				$name = View::escape($appointer->getName());
				echo "
				<tr><td>$editButton</td>
					<td>$name</td>
				</tr>
				";
			}
		?>
	</table>
</div>
