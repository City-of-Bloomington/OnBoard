<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Zend\Db\ResultSet $this->terms
 * @param Seat $this->seat (optional)
 * @param boolean $this->disableButtons
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
        $terms = $this->terms;

        $t = current($terms);
        if ($t && !$this->disableButtons
            && Person::isAllowed('terms', 'generate')) {

            echo $helper->buttonLink(
                BASE_URI.'/terms/generate?direction=next;term_id='.$t->getId(),
                $this->_('term_add_next'),
                'add'
            );
        }

        $memberList = new Block('members/list.inc', ['disableButtons'=>$this->disableButtons]);
        if ($this->seat) {
            $memberList->seat      = $this->seat;
            $memberList->committee = $this->seat->getCommittee();
        }

        echo '<table class="onboard-termsTable"><tbody>';

        foreach ($terms as $term) {
            $term_id = $term->getId();

            $addButton    = '';
            $editButton   = '';
            $deleteButton = '';
            if (!$this->disableButtons) {
                if (Person::isAllowed('members', 'edit')) {
                    $addButton = $helper->buttonLink(
                        BASE_URI."/members/update?term_id=$term_id",
                        $this->_('member_add'),
                        'add'
                    );                }
                if (Person::isAllowed('terms', 'edit')) {
                    $editButton = '<a href="'.BASE_URI.'/terms/update?term_id='.$term_id.'" class="fn1-dropdown-link">'.$this->_('term_edit').'</a>';
                }
                if (Person::isAllowed('terms', 'delete') && $term->isSafeToDelete()) {
                    $deleteButton = '<a href="'.BASE_URI.'/terms/delete?term_id='.$term_id.'" class="fn1-dropdown-link">'.$this->_('term_delete').'</a>';
                }
            }

            $uri = BASE_URI."/terms/view?term_id=$term_id";
            $memberList->members = $term->getMembers();
            echo "
                <tr>
                    <th>
                        {$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}
                        <div class=\"fn1-dropdown\">
                            <button class=\"fn1-dropdown-launcher\" aria-haspopup=\"true\" aria-expanded=\"false\">
                                {$this->_('more')}
                            </button>
                            <div    class=\"fn1-dropdown-links\">
								<div class=\"fn1-dropdown-subgroup\">
									$editButton
								</div>
								<div class=\"fn1-dropdown-subgroup\">
									$deleteButton
								</div>
                            </div>
                        </div>
                    </th>
                    <td>{$memberList->render('html', $this->template)} $addButton</td>
                </tr>

            ";
        }
        echo '</tbody></table>';
        if (!$this->disableButtons) {
            if (isset($term) && Person::isAllowed('terms', 'generate')) {
                echo $helper->buttonLink(
                    BASE_URI.'/terms/generate?direction=previous;term_id='.$term->getId(),
                    $this->_('term_add_previous'),
                    'add'
                );
            }
            if (isset($this->seat)  && !count($terms) && Person::isAllowed('terms', 'add')) {
                echo $helper->buttonLink(
                    BASE_URI.'/terms/update?seat_id='.$this->seat->getId(),
                    $this->_('term_add'),
                    'add'
                );
            }
        }

	?>
</section>
