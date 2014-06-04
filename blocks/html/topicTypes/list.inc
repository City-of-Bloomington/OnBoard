<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->topicTypes
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;
?>
<div>
	<h2><?php
			echo $this->translate(['labels.topicType','labels.topicTypes',2]);

			if (Person::isAllowed('topicTypes')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/topicTypes/update',
					$this->translate('labels.add_topicType'),
					'add'
				);
			}
		?>
	</h2>
	<table>
		<?php
			foreach ($this->topicTypes as $topicType) {
				$editButton = '';
				if (Person::isAllowed('topicTypes')) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/topicTypes/update?topicType_id='.$topicType->getId(),
						$this->translate('labels.edit'),
						'edit',
						ButtonLink::SIZE_ICON
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
