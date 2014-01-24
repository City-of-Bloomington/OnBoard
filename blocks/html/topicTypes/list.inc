<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->topicTypes
 */
use Application\Models\Person;
use Blossom\Classes\View;
?>
<div>
	<h2><?php
			echo $this->translate(['labels.topicType','labels.topicTypes',2]);

			if (Person::isAllowed('topicTypes')) {
				echo sprintf(
					'<a class="btn" href="%s"><i class="fa fa-plus"></i> %s</a>',
					BASE_URI.'/topicTypes/update',
					$this->translate('labels.add_topicType')
				);
			}
		?>
	</h2>
	<table>
		<?php
			foreach ($this->topicTypes as $topicType) {
				$editButton = '';
				if (Person::isAllowed('topicTypes')) {
					$editButton = sprintf(
						'<a class="fa fa-edit" href="%s"><i class="hidden-label">%s</i></a>',
						BASE_URI.'/topicTypes/update?topicType_id='.$topicType->getId(),
						$this->translate('labels.edit')
					);
				}

				$name = View::escape($topicType->getName());
				echo "
				<tr><td>$editButton</td>
					<td>$name</td>
				</tr>
				";
			}
		?>
	</table>
</div>
