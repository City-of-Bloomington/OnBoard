<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Committee $this->committee
 */
use Blossom\Classes\Url;
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
?>
<section class="fn1-uiBlock">
  <header class="fn1-uiBlock-header">
    <h2><?= $this->_(['current_member', 'current_members', 2]); ?></h2>
  </header>
  <div class="fn1-uiBlock-content">
    <table class="fn1-table">
        <thead>
            <tr><th><?= $this->_('name'); ?></th>
                <th><?= $this->_('seat'); ?></th>
                <th><?= $this->_('appointed_by'); ?></th>
                <th><?= $this->_(['term','term', 1]); ?></th>
                <th><?= $this->_('actions'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
            $currentUrl = new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
            $helper = $this->template->getHelper('buttonLink');

            $committee_id = $this->committee->getId();


            if ($this->committee->getType() === 'seated') {
                foreach ($this->committee->getSeats(time()) as $seat) {
                    $memberName = '';
                    $seatName  = self::escape($seat);
                    $appointer = $seat->getAppointer_id() ? self::escape($seat->getAppointer()->getName()) : '';
                    $termDates = '';
                    $offices   = '';
                    $buttons   = '';
                    $classes   = [];

                    $seatIsVacant = $seat->hasVacancy();

                    if ($seatIsVacant) {
                        $memberName = $this->_('vacancy');
                        $appointURI = BASE_URI.'/seats/appoint';
                        $classes[] = 'vacant';

                        if ($seat->getType() === 'termed') {
                            $term = $seat->getTerm(time());
                            if ($term) {
                                $termDates  = "{$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}";
                                $appointURI.= "?term_id={$term->getid()}";

                                if (strtotime('+3 days') > (int)$term->getEndDate('U')) {
                                    $classes[] = 'termEndsSoon';
                                }
                            }
                        }
                        else {  $appointURI.= "?seat_id={$seat->getId()}"; }

                        if (Person::isAllowed('seats', 'appoint')) {
                            $buttons.= '<a href="'.$appointURI.'" class="fn1-dropdown-link">'
                                .$this->_('member_add').'</a>';
                        }
                    }

                    $member = $seat->getMember(time());
                    if ($member) {
                        $memberName.= self::escape($member->getPerson()->getFullname());
                        $termDates = $member->getTerm_id()
                            ? "{$member->getTerm()->getStartDate(DATE_FORMAT)} - {$member->getTerm()->getEndDate(DATE_FORMAT)}"
                            : '';

                        $offices = [];
                        foreach ($member->getPerson()->getOffices($this->committee, date('Y-m-d')) as $office) {
                            $offices[] = self::escape($office->getTitle());
                        }
                        $offices = count($offices)
                            ? '<span class="onboard-offices">'.implode(', ',$offices).'</span> '
                            : '';
                        if ($seatIsVacant && Person::isAllowed('members', 'reappoint')) {
                            $buttons.= '<a href="'.BASE_URI.'/members/reappoint?member_id='.$member->getId().'" class="fn1-dropdown-link">'
                                .$this->_('member_continue').'</a>';
                        }

                        if (!$member->getEndDate() && Person::isAllowed('members', 'resign')) {
                            $buttons .= '<a href="'.BASE_URI.'/members/resign?member_id='.$member->getId().'" class="fn1-dropdown-link">'
                                .$this->_('member_end').'</a>';
                        }

                        if ($seatIsVacant) { $classes[] = 'carryover'; }
                    }

                    if ($buttons != '') {
                        $buttons = "
                            <div        class=\"fn1-dropdown fn1-table-actions\">
                                <button class=\"fn1-dropdown-launcher\" aria-haspopup=\"true\" aria-expanded=\"false\">
                                    {$this->_('more')}
                                </button>
                                <div    class=\"fn1-dropdown-links\">
                                    $buttons
                                </div>
                            </div>
                        ";
                    }

                    $classes = count($classes) ? 'class="'.implode(' ', $classes).'"' : '';
                    $uri = BASE_URI.'/seats/view?seat_id='.$seat->getId();
                    echo "
                    <tr $classes>
                        <td>{$offices}{$memberName}</td>
                        <td><a href=\"$uri\">$seatName</a></td>
                        <td>$appointer</td>
                        <td>$termDates</td>
                        <td>$buttons</td>
                    </tr>
                    ";
                }
            }
            else {
                // Display current committee members
                // These members will not be associated with a seat
                foreach ($this->committee->getMembers() as $m) {
                    $name = self::escape($m->getPerson()->getFullname());
                    echo "
                    <tr><td>$name</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    ";
                }
            }
        ?>
        </tbody>
    </table>
    <?php
        if (Person::isAllowed('seats', 'add')) {
            echo $helper->buttonLink(
                BASE_URI."/seats/update?committee_id={$this->committee->getId()}",
                $this->translate('seat_add'),
                'add'
            );
        }
    ?>
  </div>
</section>
