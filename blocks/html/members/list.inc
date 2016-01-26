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
            $actionLink = $actionLink = '<a href="%s" class="fn1-dropdown-link">%s</a>';

            foreach ($this->members as $member) {
                $member_id = $member->getId();

                $editButton   = '';
                $deleteButton = '';
                if (!$this->disableButtons) {
                    if (Person::isAllowed('members', 'edit')) {
                        $uri = BASE_URI."/members/update?member_id=$member_id";
                        $editButton = sprintf($actionLink, $uri, $this->_('member_edit'));
                    }
                    if (Person::isAllowed('members', 'delete')) {
                        $uri = BASE_URI."/members/delete?member_id=$member_id";
                        $deleteButton = sprintf($actionLink, $uri, $this->_('member_delete'));
                    }
                }

                $addOfficeButton  = '';
                if (!$this->disableButtons && Person::isAllowed('offices', 'edit')) {
                    $uri = BASE_URI."/offices/update?committee_id={$member->getCommittee_id()};person_id={$member->getPerson_id()}";
                    $addOfficeButton = sprintf($actionLink, $uri, $this->_('office_add'));
                }

                $offices = [];
                $editOfficeLinks = '';
                foreach ($member->getPerson()->getOffices($member->getCommittee(), date('Y-m-d')) as $office) {
                    $title = self::escape($office->getTitle());

                    if (Person::isAllowed('offices','edit')) {
                        $uri = BASE_URI."/offices/update?office_id={$office->getId()}";
                        $editOfficeLinks.= sprintf($actionLink, $uri, sprintf($this->_('office_edit', 'messages'), $title));
                    }
                    $offices[] = $title;
                }
                $offices = implode(', ',$offices);
                if (count($offices > 0)) {
                    $offices = " <span class=\"onboard-offices\">$offices</span>";
                }

                $name = self::escape($member->getPerson()->getFullname());

                echo "
                <tr><td>$name{$offices}</td>
                    <td>{$member->getStartDate(DATE_FORMAT)} - {$member->getEndDate(DATE_FORMAT)}</td>
                    <td><div         class=\"fn1-dropdown fn1-table-actions\">
                            <button  class=\"fn1-dropdown-launcher\" aria-haspopup=\"true\" aria-expanded=\"false\">{$this->_('more')}</button>
                            <div     class=\"fn1-dropdown-links\">
                                <div class=\"fn1-dropdown-subgroup\">$addOfficeButton $editOfficeLinks</div>
                                <div class=\"fn1-dropdown-subgroup\">$editButton</div>
                                <div class=\"fn1-dropdown-subgroup\">$deleteButton</div>
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
        if ($this->seat && $this->seat->getType() === 'open' && !$this->disableButtons) {
            if (Person::isAllowed('members', 'update')) {
                echo $helper->buttonLink(
                    BASE_URI.'/members/update?seat_id='.$this->seat->getId(),
                    $this->_('member_add'),
                    'add'
                );
            }
            elseif (Person::isAllowed('members', 'appoint')) {
                echo $helper->buttonLink(
                    BASE_URI.'/members/appoint?seat_id='.$this->seat->getId(),
                    $this->_('member_add'),
                    'add'
                );
            }
        }
    ?>
</section>
