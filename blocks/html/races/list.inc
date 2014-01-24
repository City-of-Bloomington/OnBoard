<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->races
 */
use Application\Models\Person;
use Blossom\Classes\View;
?>
<div>
	<h2><?php
			echo $this->translate(['labels.race','labels.races',2]);

			if (Person::isAllowed('races')) {
				echo sprintf(
					'<a class="btn" href="%s"><i class="fa fa-plus"></i> %s</a>',
					BASE_URI.'/races/update',
					$this->translate('labels.add_race')
				);
			}
		?>
	</h2>
	<table>
		<?php
			foreach ($this->races as $race) {
				$editButton = '';
				if (Person::isAllowed('races')) {
					$editButton = sprintf(
						'<a class="fa fa-edit" href="%s"><i class="hidden-label">%s</i></a>',
						BASE_URI.'/races/update?race_id='.$race->getId(),
						$this->translate('labels.edit')
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
