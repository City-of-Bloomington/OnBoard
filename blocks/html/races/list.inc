<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->races
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;
?>
<div>
	<h2><?php
			echo $this->translate(['labels.race','labels.races',2]);

			if (Person::isAllowed('races')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/races/update',
					$this->translate('labels.add_race'),
					'add'
				);
			}
		?>
	</h2>
	<table>
		<?php
			foreach ($this->races as $race) {
				$editButton = '';
				if (Person::isAllowed('races')) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/races/update?race_id='.$race->getId(),
						$this->translate('labels.edit'),
						'edit',
						ButtonLink::SIZE_ICON
					);
				}

				$name = View::escape($race->getName());
				echo "
				<tr><td>$editButton</td>
					<td>$name</td>
				</tr>
				";
			}
		?>
	</table>
</div>
