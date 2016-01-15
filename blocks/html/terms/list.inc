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
$helper = $this->template->getHelper('buttonLink');
?>
<div>
	<h2><?= $title; ?></h2>
	<?php
        $t = current($this->terms);
        if ($t && Person::isAllowed('terms', 'generate')) {
            echo $helper->buttonLink(
                BASE_URI.'/terms/generate?direction=next;term_id='.$t->getId(),
                $this->_('term_add'),
                'add'
            );
        }
        #$this->terms->rewind();
	?>
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

				$uri = BASE_URI.'/terms/view?term_id='.$term->getId();
				echo "
				<tr><td>$editButton</td>
					<td><a href=\"$uri\">{$term->getStartDate(DATE_FORMAT)}</a></td>
					<td><a href=\"$uri\">{$term->getEndDate(DATE_FORMAT)}</a></td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
	<?php
        if ($term && Person::isAllowed('terms', 'generate')) {
            echo $helper->buttonLink(
                BASE_URI.'/terms/generate?direction=previous;term_id='.$term->getId(),
                $this->_('term_add'),
                'add'
            );
        }
        if (isset($this->seat)  && !count($this->terms)
            && Person::isAllowed('terms', 'add')) {
            echo $helper->buttonLink(
                BASE_URI.'/terms/update?seat_id='.$this->seat->getId(),
                $this->_('term_add'),
                'add'
            );
        }
	?>
</div>
