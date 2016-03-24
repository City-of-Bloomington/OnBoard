<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\ResultSet $this->terms
 * @param Seat $this->seat (optional)
 * @param boolean $this->disableButtons
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\Block;

$title  = $this->title ? self::escape($this->title) : $this->_(['term', 'terms', 2]);
$helper = $this->template->getHelper('buttonLink');
?>
<section class="termsList">
    <header>
        <h1><?= $title; ?></h1>
        <?php
            $t = current($this->terms);
            if ($t && !$this->disableButtons
                && Person::isAllowed('terms', 'generate')) {

                echo '<div class="tools">';
                echo $helper->buttonLink(
                    BASE_URI.'/terms/generate?direction=next;term_id='.$t->getId(),
                    $this->_('term_add_next'),
                    'add'
                );
                echo '</div>';
            }
        ?>
    </header>
    <table class="onboard-termsTable">
    <?php
        $memberList = new Block('members/list.inc', ['disableButtons'=>$this->disableButtons]);
        if ($this->seat) {
            $memberList->seat      = $this->seat;
            $memberList->committee = $this->seat->getCommittee();
        }

        $actions = new Block('terms/partials/actions.inc');

        foreach ($this->terms as $term) {
            $term_id = $term->getId();

            $addButton = '';
            $moreLinks = '';
            if (!$this->disableButtons) {
                if (Person::isAllowed('members', 'edit')) {
                    $addButton = $helper->buttonLink(
                        BASE_URI."/members/update?term_id=$term_id",
                        $this->_('member_add'),
                        'add'
                    );
                }
                $actions->term = $term;
                $moreLinks = $actions->render('html', $this->template);
            }

            $uri = BASE_URI."/terms/view?term_id=$term_id";
            $memberList->members = $term->getMembers();
            echo "
            <tr><th>{$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}
                    $moreLinks
                </th>
                <td>{$memberList->render('html', $this->template)} $addButton</td>
            </tr>
            ";
        }

	?>
	</table>
	<?php
        if (!$this->disableButtons) {
            if (isset($term) && Person::isAllowed('terms', 'generate')) {
                echo $helper->buttonLink(
                    BASE_URI.'/terms/generate?direction=previous;term_id='.$term->getId(),
                    $this->_('term_add_previous'),
                    'add'
                );
            }
            if (isset($this->seat)  && !count($this->terms) && Person::isAllowed('terms', 'add')) {
                echo $helper->buttonLink(
                    BASE_URI.'/terms/update?seat_id='.$this->seat->getId(),
                    $this->_('term_add'),
                    'add'
                );
            }
        }
	?>
</section>
