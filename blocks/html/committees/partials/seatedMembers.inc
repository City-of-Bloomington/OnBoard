<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->seats
 * @param string $this->title (optional)
 * @param Committee $this->committee (optional)
 */
use Application\Models\Person;
use Blossom\Classes\Url;

$title = $this->title
    ? self::escape($this->title)
    : $this->_(['seat', 'seats', count($this->seats)]);
?>
<section     class="fn1-uiBlock">
    <header  class="fn1-uiBlock-header"><h1><?= $title; ?></h1>
        <div class="fn1-uiBlock-headerTools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            $url = new Url(Url::current_url());
            $url->format = 'csv';
            echo $helper->buttonLink($url, $this->_('csv'), 'download');
        ?>
        </div>
    </header>
    <div    class="fn1-uiBlock-content">

<table class="fn1-table">
    <thead>
        <tr>
            <th><?= $this->_('seat'); ?></th>
            <th><?= $this->_('appointed_by'); ?></th>
            <th><?= $this->_('name'); ?></th>
            <th><?= $this->_(['term','term', 1]); ?></th>
            <th><?= $this->_('actions'); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php

        foreach ($this->seats as $seat) {
            $seat_id    = $seat->getId();
            $memberName = '';
            $seatName   = self::escape($seat->getName());
            $appointer  = $seat->getAppointer_id() ? self::escape($seat->getAppointer()->getName()) : '';
            $termDates  = '';
            $offices    = '';
            $officeTitles = '';
            $actions    = '';
            $actionLink = '<a href="%s" class="fn1-dropdown-link">%s</a>';
            $classes    = [];

            $seatIsVacant = $seat->hasVacancy();

            if ($seatIsVacant) {
                $classes[] = 'vacant';

                if ($seat->getType() === 'termed') {
                    $term = $seat->getTerm(time());
                    if ($term) {
                        $termDates  = "{$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}";
                        $appointURI = BASE_URI."/members/appoint?term_id={$term->getid()}";

                    }
                }
                else {
                    $appointURI = BASE_URI."/members/appoint?seat_id=$seat_id";
                }

                if (isset($appointURI) && Person::isAllowed('members', 'appoint')) {
                    $actions.= sprintf($actionLink, $appointURI, $this->_('member_add'));
                }
            }

            $member = $seat->getMember(time());
            if ($member) {
                $member_id = $member->getId();
                $person_id = $member->getPerson_id();
                $person_uri = BASE_URI."/people/view?person_id=$person_id";
                $name = self::escape($member->getPerson()->getFullname());
                $name = "<a href=\"$person_uri\">$name</a>";

                $memberName.= $name;
                $term = $member->getTerm();
                $termDates = $term
                    ? "{$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}"
                    : "{$member->getStartDate(DATE_FORMAT)} - {$member->getEndDate(DATE_FORMAT)}";

                $termEndWarningDays = $seat->getCommittee()->getTermEndWarningDays();
                if ($term && $term->isInEndWarningPeriod(time())) {
                    $classes[] = 'termEndsSoon';
                }

                if (Person::isAllowed('members', 'reappoint')) {
                    $uri = BASE_URI."/members/reappoint?member_id=$member_id";
                    $actions.= sprintf($actionLink, $uri, $this->_('member_continue'));
                }

                if (!$member->getEndDate() && Person::isAllowed('members', 'resign')) {
                    $uri = BASE_URI."/members/resign?member_id=$member_id";
                    $actions.= sprintf($actionLink, $uri, $this->_('member_end'));
                }

                $offices = $member->getPerson()->getOffices($seat->getCommittee(), date('Y-m-d'));
                if (Person::isAllowed('offices', 'add')) {
                    $uri = BASE_URI."/offices/update?committee_id={$seat->getCommittee_id()};person_id=$person_id";
                    $actions.= sprintf($actionLink, $uri, $this->_('office_add'));
                }
                if (Person::isAllowed('offices', 'edit')) {
                    foreach ($offices as $o) {
                        $uri = BASE_URI."/offices/update?office_id={$o->getId()}";
                        $actions.= sprintf($actionLink, $uri, "{$this->_('edit')} {$o->getTitle()}");
                    }
                }
                $officeTitles = count($offices)
                    ? ' <span class="onboard-offices">'.implode(', ',$offices).'</span>'
                    : '';

                if ($seatIsVacant) { $classes[] = 'carryOver'; }
            }

            if ($actions != '') {
                $actions = "
                    <div        class=\"fn1-dropdown fn1-table-actions\">
                        <button class=\"fn1-dropdown-launcher\" aria-haspopup=\"true\" aria-expanded=\"false\">
                            {$this->_('more')}
                        </button>
                        <div    class=\"fn1-dropdown-links\">
                            $actions
                        </div>
                    </div>
                ";
            }

            $classes  = count($classes) ? 'class="'.implode(' ', $classes).'"' : '';
            $seat_uri = BASE_URI."/seats/view?seat_id=$seat_id";
            if (!$memberName) { $memberName = $this->_('vacant'); }
            echo "
            <tr $classes>
                <td><a href=\"$seat_uri\">$seatName</a></td>
                <td>$appointer</td>
                <td>{$memberName}{$officeTitles}</td>
                <td>$termDates</td>
                <td>$actions</td>
            </tr>
            ";
        }
    ?>
    </tbody>
</table>
<?php
    if ($this->committee && Person::isAllowed('seats', 'add')) {
        $helper = $this->template->getHelper('buttonLink');
        echo $helper->buttonLink(
            BASE_URI."/seats/update?committee_id={$this->committee->getId()}",
            $this->translate('seat_add'),
            'add'
        );
    }
?>
    </div>
</section>
