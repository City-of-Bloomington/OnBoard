<?php
/**
 * @copyright 2009-2022 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE
 * @param array   $this->terms
 * @param Seat    $this->seat           (optional)
 * @param boolean $this->disableButtons (optional)
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Web\Block;

$title  = $this->title ? self::escape($this->title) : $this->_(['term', 'terms', 2]);
$helper = $this->template->getHelper('buttonLink');
?>
<section class="termsList">
    <header>
        <h2><?= $title; ?></h2>
        <?php
            if (isset($this->terms[0]) && !$this->disableButtons
                && Person::isAllowed('terms', 'generate')) {

                echo '<div class="tools">';
                echo $helper->buttonLink(
                    parent::generateUri('terms.generate').'?direction=next;term_id='.$this->terms[0]->getId(),
                    $this->_('term_add_next'),
                    'add'
                );
                echo '</div>';
            }
        ?>
    </header>
    <table class="terms">
    <thead>
        <tr><th>Terms</th><th>Members</th><th>Alternates</th></tr>
    </thead>
    <tbody>
    <?php
        $memberList    = new Block(   'members/list.inc', ['disableButtons'=>$this->disableButtons]);
        $alternateList = new Block('alternates/list.inc', ['disableButtons'=>$this->disableButtons]);
        $actions       = new Block('terms/partials/actions.inc');

        foreach ($this->terms as $term) {
            $term_id   = $term->getId();
            $moreLinks = '';
            if (!$this->disableButtons) {
                $actions->term = $term;
                $moreLinks     = $actions->render('html', $this->template);
            }

            $memberList   ->term = $term;
            $alternateList->term = $term;
            $memberList   ->members    = $term->getMembers();
            $alternateList->alternates = $term->getAlternates();
            echo "
            <tr><th>{$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}
                    $moreLinks
                </th>
                <td>{$memberList   ->render('html', $this->template)}</td>
                <td>{$alternateList->render('html', $this->template)}</td>
            </tr>
            ";
        }

	?>
	</tbody>
	</table>
	<?php
        if (!$this->disableButtons) {
            if (isset($term) && Person::isAllowed('terms', 'generate')) {
                echo $helper->buttonLink(
                    parent::generateUri('terms.generate').'?direction=previous;term_id='.$term->getId(),
                    $this->_('term_add_previous'),
                    'add'
                );
            }
            if (isset($this->seat)  && !count($this->terms) && Person::isAllowed('terms', 'update')) {
                echo $helper->buttonLink(
                    parent::generateUri('terms.update').'?seat_id='.$this->seat->getId(),
                    $this->_('term_add'),
                    'add'
                );
            }
        }
	?>
</section>
