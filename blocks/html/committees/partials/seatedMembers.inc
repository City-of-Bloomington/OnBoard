<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Application\Models\Person;
?>
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

        foreach ($this->committee->getSeats(time()) as $seat) {
            $memberName = '';
            $seatName  = self::escape($seat->getName());
            $appointer = $seat->getAppointer_id() ? self::escape($seat->getAppointer()->getName()) : '';
            $termDates = '';
            $offices   = '';
            $buttons   = '';
            $classes   = [];

            $seatIsVacant = $seat->hasVacancy();

            if ($seatIsVacant) {
                $memberName = $this->_('vacancy');
                $classes[] = 'vacant';

                if ($seat->getType() === 'termed') {
                    $term = $seat->getTerm(time());
                    if ($term) {
                        $termDates  = "{$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}";
                        $appointURI = BASE_URI."/members/appoint?term_id={$term->getid()}";

                        if (strtotime('+3 days') > (int)$term->getEndDate('U')) {
                            $classes[] = 'termEndsSoon';
                        }
                    }
                }
                else {
                    $appointURI = BASE_URI."/members/appoint?seat_id={$seat->getId()}";
                }

                if (isset($appointURI) && Person::isAllowed('seats', 'appoint')) {
                    $buttons.= "
                    <a href=\"$appointURI\" class=\"fn1-dropdown-link\">
                        {$this->_('member_add')}
                    </a>
                    ";
                }
            }

            $member = $seat->getMember(time());
            if ($member) {
                $person_uri = BASE_URI.'/people/view?person_id='.$member->getPerson_id();
                $name = self::escape($member->getPerson()->getFullname());
                $name = "<a href=\"$person_uri\">$name</a>";

                $memberName.= $name;
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

                if (Person::isAllowed('members', 'reappoint')) {
                    $uri = BASE_URI."/members/reappoint?member_id={$member->getId()}";
                    $buttons.= "
                    <a href=\"$uri\" class=\"fn1-dropdown-link\">
                        {$this->_('member_continue')}
                    </a>
                    ";
                }

                if (!$member->getEndDate() && Person::isAllowed('members', 'resign')) {
                    $uri = BASE_URI."/members/resign?member_id={$member->getId()}";
                    $buttons.= "
                    <a href=\"$uri\" class=\"fn1-dropdown-link\">
                        {$this->_('member_end')}
                    </a>
                    ";
                }

                if ($seatIsVacant) { $classes[] = 'carryOver'; }
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

            $classes  = count($classes) ? 'class="'.implode(' ', $classes).'"' : '';
            $seat_uri = BASE_URI.'/seats/view?seat_id='.$seat->getId();
            echo "
            <tr $classes>
                <td>{$offices}{$memberName}</td>
                <td><a href=\"$seat_uri\">$seatName</a></td>
                <td>$appointer</td>
                <td>$termDates</td>
                <td>$buttons</td>
            </tr>
            ";
        }
    ?>
    </tbody>
</table>
<?php
    if (Person::isAllowed('seats', 'add')) {
        $helper = $this->template->getHelper('buttonLink');
        echo $helper->buttonLink(
            BASE_URI."/seats/update?committee_id={$this->committee->getId()}",
            $this->translate('seat_add'),
            'add'
        );
    }
?>
