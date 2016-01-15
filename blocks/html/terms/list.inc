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
use Blossom\Classes\Block;

$title = $this->title ? self::escape($this->title) : $this->_(['term', 'terms', 2]);
$helper = $this->template->getHelper('buttonLink');
?>
<section class="termsList">
	<h1><?= $title; ?></h1>
	<?php
        $t = current($this->terms);
        if ($t && Person::isAllowed('terms', 'generate')) {
            echo $helper->buttonLink(
                BASE_URI.'/terms/generate?direction=next;term_id='.$t->getId(),
                $this->_('term_add_next'),
                'add'
            );
        }

        $memberList = new Block('members/list.inc');
        if ($this->seat) {
            $memberList->seat      = $this->seat;
            $memberList->committee = $this->seat->getCommittee();
        }

        foreach ($this->terms as $term) {
            $editButton = '';
            if (Person::isAllowed('terms', 'edit')) {
                $editButton = $helper->buttonLink(
                    BASE_URI."/terms/update?term_id={$term->getId()}",
                    $this->_('term_edit'),
                    'edit'
                );
            }

            $uri = BASE_URI.'/terms/view?term_id='.$term->getId();
            $memberList->members = $term->getMembers();
            echo "
            <article>
                <h1>{$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}
                    $editButton
                </h1>
                {$memberList->render('html', $this->template)}
            </article>
            ";
        }

        if ($term && Person::isAllowed('terms', 'generate')) {
            echo $helper->buttonLink(
                BASE_URI.'/terms/generate?direction=previous;term_id='.$term->getId(),
                $this->_('term_add_previous'),
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
</section>