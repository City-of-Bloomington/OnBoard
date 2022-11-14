<?php
/**
 * @copyright 2016-2022 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param array             $this->params         Query parameters that produced the data
 * @param Laminas\Db\Result $this->data           Data structure returned as SeatTable::$dataFields
 * @param Committee         $this->committee      (optional)
 * @param string            $this->title          (optional)
 * @param bool              $this->disableButtons (optional)
 */
use Application\Models\Person;
use Application\Models\Term;
use Application\Templates\Helpers\ButtonLink;
use Web\Url;

$current_url  = Url::current_url(BASE_HOST);
$title = $this->title
         ? self::escape($this->title)
         : $this->_(['seat', 'seats', count($this->data['results'])]);
if (isset($this->params['current'])) {
    $title.= ' ('.$this->params['current']->format(DATE_FORMAT).')';
}
?>
<section>
    <header>
        <h2><?= $title; ?></h2>
        <div class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            if (!$this->disableButtons) {
                $url = new Url(Url::current_url(BASE_HOST));
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

    <table>
        <thead>
            <tr><?php
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
            $editSeatButton        = '';
            $userCanEditSeats      = !$this->disableButtons && Person::isAllowed('seats', 'update');
            $userCanEditAlternates = !$this->disableButtons && Person::isAllowed('alternates', 'update');

            foreach ($this->data['results'] as $row) {
                /**
                 * Create local variables for each of the result columns
                 *
                 * @see SeatTable::$dataFields
                 */
                foreach ($row as $f=>$v) {
                    switch ($f) {
                        case 'seat_startDate':
                        case 'seat_endDate':
                        case 'term_startDate':
                        case 'term_endDate':
                        case 'member_startDate':
                        case 'member_endDate':
                        case 'member_termStart':
                        case 'member_termEnd':
                        case 'alternate_startDate':
                        case 'alternate_endDate':
                        case 'alternate_termStart':
                        case 'alternate_termEnd':
                            if ($v) {
                                $d = new \DateTime($v);
                                $$f = $d->format(DATE_FORMAT);
                            }
                            else {
                                $$f = null;
                            }
                        break;

                        case 'seat_voting':
                            $$f = $v ? " (voting)" : '';
                        break;

                        default:
                            $$f = self::escape($v);
                    }
                }

                $actions    = [];
                $classes    = [];

                if ($userCanEditSeats) {
                    $actions[] = [
                        'url'   => parent::generateUri('seats.update')."?seat_id=$seat_id",
                        'label' => $this->_('seat_edit')
                    ];
                }

                $committeeName = !$this->committee ? "<td>$committee_name</td>" : '';
                $seat_uri      = parent::generateUri('seats.view')."?seat_id=$seat_id";
                $alternate     = '';

                if ($this->committee && $this->committee->allowsAlternates()) {
                    if ($alternate_person_id) {
                        $uri       = parent::generateUri('people.view')."?person_id=$alternate_person_id";
                        $alternate = "<a href=\"$uri\">({$this->_('alternate')}: $alternate_firstname $alternate_lastname)</a>";
                        if ($userCanEditAlternates) {
                            $uri       = parent::generateUri('alternates.update')."?alternate_id=$alternate_id;return_url=".urlencode($current_url);
                            $actions[] = ['url'=>$uri, 'label'=>$this->_('alternate_edit')];
                        }
                    }
                    else {
                        if ($userCanEditAlternates) {
                            $uri       = parent::generateUri('alternates.update')."?return_url=".urlencode($current_url);
                            $actions[] = ['url'=>$uri, 'label'=>$this->_('alternate_add')];
                        }
                    }
                }

                if ($member_person_id) {
                    $uri       = parent::generateUri('people.view')."?person_id=$member_person_id";
                    $member    = "<a href=\"$uri\">$member_firstname $member_lastname</a>";
                    $termDates = "$member_termStart - $member_termEnd";

                    // Check if near the end of term
                    if ($termEndsSoon) { $classes[] = 'termEndsSoon'; }
                    if ($carryOver) {
                        $classes[] = 'carryOver vacant';
                        // Appoint
                        if (!$this->disableButtons && Person::isAllowed('members', 'appoint')) {
                            $uri = $seat_type === 'termed'
                                   ? parent::generateUri('members.appoint')."?term_id=$term_id"
                                   : parent::generateUri('members.appoint')."?seat_id=$seat_id";

                            $actions[] = ['url'=>$uri, 'label'=>$this->_('member_add')];
                        }
                    }

                    if ($seat_type === 'termed') {
                        $t = new Term($term_id);
                        $n = $t->getNextTerm();

                        if ($n->isVacant()) {
                            if (!$this->disableButtons && Person::isAllowed('members', 'reappoint')) {
                                $uri = parent::generateUri('members.reappoint')."?member_id=$member_id";
                                $actions[] = ['url'=>$uri, 'label'=>$this->_('member_continue')];
                            }
                        }
                        else {
                            // Since the next term is not vacant, we should not set the CSS status
                            // for 'termEndsSoon'.
                            // Otherwise, users will see the status, but cannot appoint someone for
                            // the next term, since there's already someone scheduled to serve.
                            if ($termEndsSoon) {
                                $key = array_search('termEndsSoon', $classes);
                                if ($key !== false) {
                                    unset($classes[$key]);
                                }
                            }
                        }
                    }

                    if ((!$member_endDate || strtotime($member_endDate) > time())
                        && !$this->disableButtons
                        && Person::isAllowed('members', 'resign')) {

                        $uri = parent::generateUri('members.resign')."?member_id=$member_id";
                        $actions[] = ['url'=>$uri, 'label'=>$this->_('member_end')];
                    }
                }
                else {
                    $classes[] = 'vacant';
                    $member    = $this->_('vacant');
                    $termDates = "$term_startDate - $term_endDate";

                    if (!$this->disableButtons && Person::isAllowed('members', 'appoint')) {
                        $uri = $seat_type === 'termed'
                               ? parent::generateUri('members.appoint')."?term_id=$term_id"
                               : parent::generateUri('members.appoint')."?seat_id=$seat_id";

                        $actions[] = ['url'=>$uri, 'label'=>$this->_('member_add')];
                    }
                }

                $userCanEditOffices = !$this->disableButtons && Person::isAllowed('offices', 'update');
                if ($userCanEditOffices) {
                    $actions[] = [
                        'url'   => parent::generateUri('offices.update')."?committee_id=$committee_id;person_id=$member_person_id",
                        'label' => $this->_('office_add')
                    ];
                }
                if ($offices) {
                    $ts = [];
                    foreach (explode(',',$offices) as $o) {
                        list($office_id, $office_title) = explode('|', $o);
                        $ts[] = $office_title;
                        if ($userCanEditOffices) {
                            $actions[] = [
                                'url'   => parent::generateUri('offices.update')."?office_id=$office_id",
                                'label' => "{$this->_('edit')} $office_title"
                            ];
                        }
                    }
                    $offices = '<span class="offices">'.implode(',', $ts).'</span>';
                }

                $classes   = count($classes) ? 'class="'.implode(' ', $classes).'"' : '';
                $moreLinks = '';
                if (isset($_SESSION['USER']) && count($actions)) {
                    $h = $this->template->getHelper('dropdown');
                    $moreLinks = $h->dropdown($actions, $this->_('more'), "seatActions_$seat_id");
                }
                echo "
                <tr $classes>
                    $committeeName
                    <td><a href=\"$seat_uri\">$seat_code <small>($seat_name)</small</a></td>
                    <td>$termDates</td>
                    <td>$appointer_name$seat_voting</td>
                    <td>$member $offices $alternate</td>
                    <td>$moreLinks</td>
                </tr>
                ";
            }
        ?>
        </tbody>
    </table>
    <?php
    if ($this->committee && !$this->disableButtons) {
        $helper       = $this->template->getHelper('buttonLink');
        $committee_id = $this->committee->getId();

        if ($userCanEditSeats) {
            echo $helper->buttonLink(
                parent::generateUri('seats.update')."?committee_id=$committee_id",
                $this->translate('seat_add'),
                'add'
            );
        }
        if ($this->committee->hasVacancy()) {
            echo $helper->buttonLink(
                parent::generateUri('applicants.apply')."?committee_id=$committee_id",
                $this->_('apply'),
                'apply'
            );
        }
    }
    ?>
</section>
