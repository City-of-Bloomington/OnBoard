<?php
/**
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->voteTypes
 */
use Application\Models\Person;
use Blossom\Classes\View;
?>
<div>
	<h2><?php
			echo $this->_(['labels.voteType','labels.voteTypes',2]);

			if (Person::isAllowed('voteTypes')) {
				echo sprintf(
					'<a class="btn" href="%s"><i class="fa fa-plus"></i> %s</a>',
					BASE_URI.'/voteTypes/update',
					$this->translate('labels.add_voteType')
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
					$editButton = sprintf(
						'<a class="fa fa-edit" href="%s"><i class="hidden-label">%s</i></a>',
						BASE_URI.'/voteTypes/update?voteType_id='.$voteType->getId(),
						$this->translate('labels.edit_voteType')
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
