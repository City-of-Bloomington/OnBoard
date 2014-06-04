<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->voteTypes
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;
?>
<div>
	<h2><?php
			echo $this->_(['labels.voteType','labels.voteTypes',2]);

			if (Person::isAllowed('voteTypes')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/voteTypes/update',
					$this->translate('labels.add_voteType'),
					'add'
				);
			}
		?>
	</h2>
	<?php
		if (count($this->voteTypes)) {
			echo '<table>';
			foreach ($this->voteTypes as $voteType) {
				$editButton = '';
				if (Person::isAllowed('voteTypes')) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/voteTypes/update?voteType_id='.$voteType->getId(),
						$this->translate('labels.edit_voteType'),
						'edit',
						ButtonLink::SIZE_ICON
					);
				}

				$name = View::escape($voteType->getName());
				$order = (int)$voteType->getOrdering();
				echo "
				<tr><th>$editButton$order</th>
					<td>$name</td>
				</tr>
				";
			}
			echo '</table>';
		}
	?>
</div>
