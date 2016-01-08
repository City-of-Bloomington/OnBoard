<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->terms
 * @param Seat $this->seat (optional)
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
$title = $this->title ? self::escape($this->title) : $this->_(['term', 'terms', 2]);
?>
<div>
	<h2><?= $title; ?></h2>
	<table>
		<thead>
			<tr><th></th>
				<th><?= $this->_('startDate'); ?></th>
				<th><?= $this->_('endDate'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
            $helper = $this->template->getHelper('buttonLink');

			foreach ($this->terms as $term) {
				$editButton = '';
				if (Person::isAllowed('terms', 'edit')) {
					$editButton = $helper->buttonLink(
						BASE_URI."/terms/update?term_id={$term->getId()}",
						$this->_('term_edit'),
						'edit',
						ButtonLink::SIZE_ICON
					);
				}

				echo "
				<tr><td>$editButton</td>
					<td>{$term->getStartDate(DATE_FORMAT)}</td>
					<td>{$term->getEndDate(DATE_FORMAT)}</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
	<?php
        if (isset($this->seat) && Person::isAllowed('terms', 'add')) {
            echo $helper->buttonLink(
                BASE_URI.'/terms/update?seat_id='.$this->seat->getId(),
                $this->_('term_add'),
                'add'
            );
        }
	?>
</div>
