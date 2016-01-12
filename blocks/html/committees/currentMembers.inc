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
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php
            $currentUrl = new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
            $helper = $this->template->getHelper('buttonLink');

            $committee_id = $this->committee->getId();


            if ($this->committee->getType() === 'seated') {
                foreach ($this->committee->getSeats(time()) as $seat) {
                    $seatName  = self::escape($seat);
                    $appointer = $seat->getAppointer_id() ? self::escape($seat->getAppointer()->getName()) : '';
                    $term      = '';
                    $offices   = '';

                    $member = $seat->getMember(time());
                    if ($member) {
                        $memberName = self::escape($member->getPerson()->getFullname());
                        $term = $member->getTerm_id()
                            ? "{$member->getTerm()->getStartDate(DATE_FORMAT)} - {$member->getTerm()->getEndDate(DATE_FORMAT)}"
                            : '';

                        $offices = [];
                        foreach ($member->getPerson()->getOffices($this->committee, date('Y-m-d')) as $office) {
                            $offices[] = self::escape($office->getTitle());
                        }
                        if(count($offices < 0)) {
                          $offices = '<span class="onboard-offices">'.implode(', ',$offices).'</span> ';
                        } else {
                          $offices = '';
                        }
                    }
                    else {
                        $memberName = $this->_('vacancy');

                        if ($seat->getType() === 'termed') {
                            $t = $seat->getTerm(time());
                            if ($t) {
                                $term = "{$t->getStartDate(DATE_FORMAT)} - {$t->getEndDate(DATE_FORMAT)}";
                            }
                        }
                    }

                    echo "
                    <tr><td>{$offices}{$memberName}</td>
                        <td>$seatName</td>
                        <td>$appointer</td>
                        <td>$term</td>
                        <td></td>
                        <td></td>
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
  </div>
</section>
