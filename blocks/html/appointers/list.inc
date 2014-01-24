<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->appointers
 */
use Application\Models\Person;
use Blossom\Classes\View;
?>
<div>
	<h2><?php
			echo $this->translate(['labels.appointer','labels.appointers',2]);

			if (Person::isAllowed('appointers')) {
				echo sprintf(
					'<a class="btn" href="%s"><i class="fa fa-plus"></i> %s</a>',
					BASE_URI.'/appointers/update',
					$this->translate('labels.add_appointer')
				);
			}
		?>
	</h2>
	<table>
		<?php
			foreach ($this->appointers as $appointer) {
				$editButton = '';
				if (Person::isAllowed('appointers')) {
					$editButton = sprintf(
						'<a class="fa fa-edit" href="%s"><i class="hidden-label">%s</i></a>',
						BASE_URI.'/appointers/update?appointer_id='.$appointer->getId(),
						$this->translate('labels.edit')
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
