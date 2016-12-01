<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array          $this->params         Query parameters that produced the data
 * @param Zend\Db\Result $this->data
 * @param Committee      $this->committee      (optional)
 * @param string         $this->title          (optional)
 * @param bool           $this->disableButtons (optional)
 */
use Application\Models\Person;
use Application\Models\Term;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\Url;

$title = $this->title
    ? self::escape($this->title)
    : $this->_(['seat', 'seats', count($this->data['results'])]);

if (isset($this->params['current'])) {
    $title.= ' ('.$this->params['current']->format(DATE_FORMAT).')';
}
?>
<section>
    <header>
        <h1><?= $title; ?></h1>
        <div class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            if (!$this->disableButtons) {
                $url = new Url(Url::current_url());
                $url->format = 'csv';
                echo $helper->buttonLink($url, $this->_('csv'), 'download');
            }
        ?>
        </div>
    </header>

        <?php
        /* DISABLED THE EFFECTIVE DATE SELECTION FOR THE TIME BEING
            echo '<form method="get"><fieldset>';

            $f = $this->template->getHelper('field');
            echo $f->field([
                'name'=>'current',
                'id' => 'current',
                'label'=>$this->_('date'),
                'value'=>isset($this->params['current']) ? $this->params['current']->format('U') : '',
                'type'=>'date'
            ]);
            echo '<button type="submit" class="submit">{$this->_('refresh')}</button>';
            echo '</fieldset></form>';
        */
        ?>

    <table class="fn1-table">
        <thead>
            <tr><th></th>
                <?php
                    if (!$this->committee) {
                        echo "<th>{$this->_('committee')}</th>";
                    }
                ?>
                <th><?= $this->_('seat'); ?></th>
                <th><?= $this->_('seat_term'); ?></th>
                <th><?= $this->_('appointed_by'); ?></th>
                <th><?= $this->_('name'); ?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php
            $editSeatButton = '';
            $userCanEdit = !$this->disableButtons && Person::isAllowed('seats', 'edit');

            foreach ($this->data['results'] as $row) {
                /**
                 * Create local variables for each of the result columns
                 *
                 * @see SeatTable::$dataFields
                 */
                foreach ($row as $f=>$v) {
                    switch ($f) {
                        case 'seatStart':
                        case 'seatEnd':
                        case 'memberStart':
                        case 'memberEnd':
                        case 'memberTermStart':
                        case 'memberTermEnd':
                        case 'termStart':
                        case 'termEnd':
                            if ($v) {
                                $d = new \DateTime($v);
                                $$f = $d->format(DATE_FORMAT);
                            }
                            else {
                                $$f = null;
                            }
                        break;

                        default:
                            $$f = self::escape($v);
                    }
                }

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
                        if (!$this->disableButtons && Person::isAllowed('members', 'appoint')) {
                            $uri = $seatType === 'termed'
                                ? BASE_URI."/members/appoint?term_id=$term_id"
                                : BASE_URI."/members/appoint?seat_id=$seat_id";

                            $actions[] = ['url'=>$uri, 'label'=>$this->_('member_add')];
                        }
                    }

                    if (!$this->disableButtons
                        && Person::isAllowed('members', 'reappoint')
                        && $seatType === 'termed') {

                        $t = new Term($term_id);
                        $n = $t->getNextTerm();
                        if ($n->isVacant()) {
                            $uri = BASE_URI."/members/reappoint?member_id=$member_id";
                            $actions[] = ['url'=>$uri, 'label'=>$this->_('member_continue')];
                        }
                    }

                    if ((!$memberEnd || strtotime($memberEnd) > time())
                        && !$this->disableButtons
                        && Person::isAllowed('members', 'resign')) {

                        $uri = BASE_URI."/members/resign?member_id=$member_id";
                        $actions[] = ['url'=>$uri, 'label'=>$this->_('member_end')];
                    }
                }
                else {
                    $classes[] = 'vacant';
                    $member    = $this->_('vacant');
                    $termDates = "$termStart - $termEnd";

                    if (!$this->disableButtons && Person::isAllowed('members', 'appoint')) {
                        $uri = $seatType === 'termed'
                            ? BASE_URI."/members/appoint?term_id=$term_id"
                            : BASE_URI."/members/appoint?seat_id=$seat_id";

                        $actions[] = ['url'=>$uri, 'label'=>$this->_('member_add')];
                    }
                }

                if (!$this->disableButtons && Person::isAllowed('offices', 'add')) {
                    $uri = BASE_URI."/offices/update?committee_id=$committee_id;person_id=$person_id";
                    $actions[] = ['url'=>$uri, 'label'=>$this->_('office_add')];
                }
                $userCanEditOffices = !$this->disableButtons && Person::isAllowed('offices', 'edit');
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
                    $moreLinks = $h->dropdown($actions, $this->_('more'), 'fn1-table-actions');
                }
                echo "
                <tr $classes>
                    <td>$editSeatButton</td>
                    $committeeName
                    <td><a href=\"$seat_uri\">$code <small>($seat)</small</a></td>
                    <td>$termDates</td>
                    <td>$appointer</td>
                    <td>$member $offices</td>
                    <td>$moreLinks</td>
                </tr>
                ";
            }
        ?>
        </tbody>
    </table>
    <?php
    if ($this->committee && !$this->disableButtons) {
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
