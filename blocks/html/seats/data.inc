<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->data
 * @param Committee $this->committee (optional)
 * @param string $this->title (optional)
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\Url;

$title = $this->title
    ? self::escape($this->title)
    : $this->_(['seat', 'seats', count($this->data['results'])]);
?>
<section>
    <header>
        <h1><?= $title; ?></h1>
        <div class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            $url = new Url(Url::current_url());
            $url->format = 'csv';
            echo $helper->buttonLink($url, $this->_('csv'), 'download');
        ?>
        </div>
    </header>

    <table class="fn1-table">
        <thead>
            <tr><th></th>
                <?php
                    if (!$this->committee) {
                        echo "<th>{$this->_('committee')}</th>";
                    }
                ?>
                <th><?= $this->_('seat'); ?></th>
                <th><?= $this->_('appointed_by'); ?></th>
                <th><?= $this->_('name'); ?></th>
                <th><?= $this->_('term'); ?></th>
                <?php
                    if (isset($_SESSION['USER'])) {
                        echo "<th>{$this->_('actions')}</th>";
                    }
                ?>
            </tr>
        </thead>
        <tbody>
        <?php
            $editSeatButton = '';
            $userCanEdit = Person::isAllowed('seats', 'edit');

            foreach ($this->data['results'] as $row) {
                foreach ($row as $f=>$v) { $$f = self::escape($v); }

                $actions    = [];
                $classes    = [];

                if ($userCanEdit) {
                    $editSeatButton = $helper->buttonLink(
                        BASE_URI."/seats/update?seat_id=$seat_id",
                        $this->_('seats_edit'),
                        'edit',
                        ButtonLink::SIZE_ICON
                    );
                }

                $committeeName = !$this->committee ? "<td>$committee</td>" : '';
                $seat_uri      = BASE_URI."/seats/view?seat_id=$seat_id";
                if ($person_id) {
                    $uri       = BASE_URI."/people/view?person_id=$person_id";
                    $member    = "<a href=\"$uri\">$firstname $lastname</a>";
                    $termDates = "$memberTermStart - $memberTermEnd";

                    // Check if near the end of term
                    if ($termEndsSoon) { $classes[] = 'termEndsSoon'; }
                    if ($carryOver) {
                        $classes[] = 'carryOver vacant';
                        // Appoint
                        $uri = $seatType === 'termed'
                            ? BASE_URI."/members/appoint?term_id=$term_id"
                            : BASE_URI."/members/appoint?seat_id=$seat_id";

                        $actions[] = ['url'=>$uri, 'label'=>$this->_('member_add')];
                    }
                    if (Person::isAllowed('members', 'reappoint')) {
                        $uri = BASE_URI."/members/reappoint?member_id=$member_id";
                        $actions[] = ['url'=>$uri, 'label'=>$this->_('member_continue')];
                    }

                    if (!$memberEnd && Person::isAllowed('members', 'resign')) {
                        $uri = BASE_URI."/members/resign?member_id=$member_id";
                        $actions[] = ['url'=>$uri, 'label'=>$this->_('member_end')];
                    }
                }
                else {
                    $classes[] = 'vacant';
                    $member    = $this->_('vacant');
                    $termDates = "$termStart - $termEnd";

                    if (Person::isAllowed('members', 'appoint')) {
                        $uri = $seatType === 'termed'
                            ? BASE_URI."/members/appoint?term_id=$term_id"
                            : BASE_URI."/members/appoint?seat_id=$seat_id";

                        $actions[] = ['url'=>$uri, 'label'=>$this->_('member_add')];
                    }
                }

                if (Person::isAllowed('offices', 'add')) {
                    $uri = BASE_URI."/offices/update?committee_id=$committee_id;person_id=$person_id";
                    $actions[] = ['url'=>$uri, 'label'=>$this->_('office_add')];
                }
                $userCanEditOffices = Person::isAllowed('offices', 'edit');
                if ($offices) {
                    $ts = [];
                    foreach (explode(',',$offices) as $o) {
                        list($office_id, $office_title) = explode('|', $o);
                        $ts[] = $office_title;
                        if ($userCanEditOffices) {
                            $uri = BASE_URI."/offices/update?office_id=$office_id";
                            $actions[] = ['url'=>$uri, 'label'=>"{$this->_('edit')} $office_title"];
                        }
                    }
                    $offices = '<span class="onboard-offices">'.implode(',', $ts).'</span>';
                }

                $classes  = count($classes) ? 'class="'.implode(' ', $classes).'"' : '';
                $moreLinks = '';
                if (isset($_SESSION['USER']) && count($actions)) {
                    $h = $this->template->getHelper('dropdown');
                    $moreLinks = '<td>'.$h->dropdown($actions, $this->_('more'), 'fn1-table-actions').'</td>';
                }
                echo "
                <tr $classes>
                    <td>$editSeatButton</td>
                    $committeeName
                    <td><a href=\"$seat_uri\">$code <small>($seat)</small</a></td>
                    <td>$appointer</td>
                    <td>$firstname $lastname $offices</td>
                    <td>$termDates</td>
                    $moreLinks
                </tr>
                ";
            }
        ?>
        </tbody>
    </table>
    <?php
    if ($this->committee) {
        $helper = $this->template->getHelper('buttonLink');

        if (Person::isAllowed('seats', 'add')) {
            echo $helper->buttonLink(
                BASE_URI."/seats/update?committee_id={$this->committee->getId()}",
                $this->translate('seat_add'),
                'add'
            );
        }
        if ($this->committee->hasVacancy()) {
            echo $helper->buttonLink(
                BASE_URI.'/applicants/apply?committee_id='.$this->committee->getId(),
                $this->_('apply'),
                'apply'
            );
        }
    }
    ?>
</section>
