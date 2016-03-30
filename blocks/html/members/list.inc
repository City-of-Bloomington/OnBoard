<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->members
 *
 * @param Committee $this->committee      (optional)
 * @param Seat      $this->seat           (optional)
 * @param string    $this->title          (optional)
 * @param boolean   $this->disableButtons (optional)
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\Block;
?>
<section class="membersList">
<?php
    if ($this->title) { echo "<header><h1>{$this->title}</h1></header>"; }
?>
    <table class="fn1-table">
    <?php
        $actions = new Block('members/partials/actions.inc');

        foreach ($this->members as $member) {
            $member_id = $member->getId();

            $name = self::escape($member->getPerson()->getFullname());

            $officeTitles = [];
            $offices = $member->getPerson()->getOffices($member->getCommittee(), date('Y-m-d'));
            foreach ($offices as $office) {
                $officeTitles[] = self::escape($office->getTitle());
            }
            $officeTitles = implode(', ',$officeTitles);
            if (count($officeTitles > 0)) {
                $officeTitles = " <span class=\"onboard-offices\">$officeTitles</span>";
            }

            $moreLinks = '';
            if (!$this->disableButtons) {
                $actions->member  = $member;
                $actions->offices = $offices;
                $moreLinks = $actions->render('html', $this->template);
            }
            echo "
            <tr><td>$name{$officeTitles}</td>
                <td>{$member->getStartDate(DATE_FORMAT)} - {$member->getEndDate(DATE_FORMAT)}</td>
                <td>$moreLinks</td>
            </tr>
            ";
        }
    ?>
    </table>
    <?php
        $helper = $this->template->getHelper('buttonLink');

        if (!$this->disableButtons) {
            if ($this->term) {
                if (Person::isAllowed('members', 'edit')) {
                    echo $helper->buttonLink(
                        BASE_URI.'/members/update?term_id='.$this->term->getId(),
                        $this->_('member_add'),
                        'add'
                    );
                }
            }
            elseif ($this->seat && $this->seat->getType() === 'open') {
                if (Person::isAllowed('members', 'update')) {
                    $uri = BASE_URI.'/members/update?seat_id='.$this->seat->getId();
                }
                elseif (Person::isAllowed('members', 'appoint')) {
                    $uri = BASE_URI.'/members/appoint?seat_id='.$this->seat->getId();
                }

                if (isset($uri)) {
                    echo $helper->buttonLink($uri, $this->_('member_add'), 'add');
                }
            }
            elseif ($this->committee) {
                if (Person::isAllowed('members', 'update')) {
                    $uri = BASE_URI.'/members/update?committee_id='.$this->committee->getId();
                }
                elseif (Person::isAllowed('members', 'appoint')) {
                    $uri = BASE_URI.'/members/appoint?committee_id='.$this->committee->getid();
                }

                if (isset($uri)) {
                    echo $helper->buttonLink($uri, $this->_('member_add'), 'add');
                }

                if ($this->committee->hasVacancy()) {
                    echo $helper->buttonLink(
                        BASE_URI.'/applicants/apply?committee_id='.$this->committee->getId(),
                        $this->_('apply'),
                        'apply'
                    );
                }
            }
        }
    ?>
</section>
