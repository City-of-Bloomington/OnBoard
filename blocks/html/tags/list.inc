<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->tags
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\View;
?>
<div>
	<h2><?php
			echo $this->translate(['labels.tag','labels.tags',2]);

			if (Person::isAllowed('tags')) {
				$helper = $this->template->getHelper('buttonLink');
				echo $helper->buttonLink(
					BASE_URI.'/tags/update',
					$this->translate('labels.add_tag'),
					'add'
				);
			}
		?>
	</h2>
	<table>
		<?php
			foreach ($this->tags as $tag) {
				$editButton = '';
				$deleteButton = '';
				if (Person::isAllowed('tags')) {
					$editButton = $helper->buttonLink(
						BASE_URI.'/tags/update?tag_id='.$tag->getId(),
						$this->translate('labels.edit'),
						'edit',
						ButtonLink::SIZE_ICON
					);
					$deleteButton = $helper->buttonLink(
						BASE_URI.'/tags/delete?tag_id='.$tag->getId(),
						$this->translate('labels.delete'),
						'delete',
						ButtonLink::SIZE_ICON
					);
				}

				$name = View::escape($tag->getName());
				echo "
				<tr><td>$editButton</td>
					<td><a href=\"{$tag->getUri()}\">$name</a></td>
					<td>$deleteButton</td>
				</tr>
				";
			}
		?>
	</table>
</div>
