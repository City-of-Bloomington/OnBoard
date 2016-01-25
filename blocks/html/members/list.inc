<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->members
 *
 * @param Committee $this->committee
 * @param Seat $this->seat
 * @param string $this->title
 * @param boolean $this->disableButtons
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
?>
<section class="membersList">
    <table class="fn1-table">
        <tbody>
        <?php
            $helper = $this->template->getHelper('buttonLink');

            foreach ($this->members as $member) {
                $editButton   = '';
                $deleteButton = '';
                if (!$this->disableButtons) {
                    if (Person::isAllowed('members', 'edit')) {
                        $editButton = '<a href="'.BASE_URI.'/members/update?member_id='.$member->getId().'" class="fn1-dropdown-link">'.$this->_('member_edit').'</a>';
                    }
                    if (Person::isAllowed('members', 'delete')) {
                        $deleteButton = '<a href="'.BASE_URI.'/members/delete?member_id='.$member->getId().'" class="fn1-dropdown-link">'.$this->_('member_delete').'</a>';
                    }
                }

                $addOfficeButton  = '';
                if (!$this->disableButtons && Person::isAllowed('offices', 'edit')) {
                    $addOfficeButton = $helper->buttonLink(
                        BASE_URI."/offices/update?committee_id={$member->getCommittee_id()};person_id={$member->getPerson_id()}",
                        $this->_('office_add'),
                        'add'
                    );
                    $addOfficeButton = '<a href="'.BASE_URI.'/offices/update?committee_id='.$member->getCommittee_id().';person_id='.$member->getPerson_id().'." class="fn1-dropdown-link">'.$this->_('office_add').'</a>';
                }

                $offices = [];
                foreach ($member->getPerson()->getOffices($member->getCommittee(), date('Y-m-d')) as $office) {
                    $editOfficeButton = '';
                    if (Person::isAllowed('offices','edit')) {
                        $editOfficeButton = $helper->buttonLink(
                            BASE_URI.'/offices/update?office_id='.$office->getId(),
                            $this->_('office_edit'),
                            'edit'
                        );
                    }
                    $offices[] = self::escape($office->getTitle()).$editOfficeButton;
                }
                $offices = implode(',',$offices);
                if (count($offices > 0)) {
                    $offices = " <span class=\"onboard-offices\">$offices</span>";
                }

                $name = self::escape($member->getPerson()->getFullname());

                echo "
                <tr><td></td>
                    <td>$name{$offices}</td>
                    <td>{$member->getStartDate(DATE_FORMAT)} - {$member->getEndDate(DATE_FORMAT)}</td>
                    <td>
                        <div class=\"fn1-dropdown fn1-table-actions\">
                            <button class=\"fn1-dropdown-launcher\" aria-haspopup=\"true\" aria-expanded=\"false\">
                                {$this->_('more')}
                            </button>
                            <div    class=\"fn1-dropdown-links\">
                                <div class=\"fn1-dropdown-subgroup\">
                                    $addOfficeButton
                                    $editButton
                                </div>
                                <div class=\"fn1-dropdown-subgroup\">
                                    $deleteButton
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                ";
            }
        ?>
        </tbody>
    </table>
    <?php
        if ($this->seat && $this->seat->getType() === 'open'
            && !$this->disableButtons
            && Person::isAllowed('members', 'appoint')) {

            echo $helper->buttonLink(
                BASE_URI.'/members/appoint?seat_id='.$this->seat->getId(),
                $this->_('member_add'),
                'add'
            );
        }
    ?>
</section>
