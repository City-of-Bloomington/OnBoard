<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->requirements
 */
use Application\Models\Person;
use Blossom\Classes\View;
?>
<div>
	<h2><?php
			echo $this->translate(['labels.requirement','labels.requirements',2]);

			if (Person::isAllowed('requirements')) {
				echo sprintf(
					'<a class="btn" href="%s"><i class="fa fa-plus"></i> %s</a>',
					BASE_URI.'/requirements/update',
					$this->translate('labels.add_requirement')
				);
			}
		?>
	</h2>
	<table>
		<?php
			foreach ($this->requirements as $requirement) {
				$editButton = '';
				if (Person::isAllowed('requirements')) {
					$editButton = sprintf(
						'<a class="fa fa-edit" href="%s"><i class="hidden-label">%s</i></a>',
						BASE_URI.'/requirements/update?requirement_id='.$requirement->getId(),
						$this->translate('labels.edit')
					);
					$deleteButton = sprintf(
						'<a class="fa fa-times" href="%s"><i class="hidden-label">%s</i></a>',
						BASE_URI.'/requirements/delete?requirement_id='.$requirement->getId(),
						$this->translate('labels.delete')
					);
				}

				$text = View::escape($requirement->getText());
				echo "
				<tr><td>$editButton</td>
					<td>$deleteButton</td>
					<td>$text</td>
				</tr>
				";
			}
		?>
	</table>
</div>
