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
                        $editButton = $helper->buttonLink(
                            BASE_URI."/members/update?member_id={$member->getId()}",
                            $this->_('member_edit'),
                            'edit'
                        );
                    }
                    if (Person::isAllowed('members', 'delete')) {
                        $deleteButton = $helper->buttonLink(
                            BASE_URI.'/members/delete?member_id='.$member->getId(),
                            $this->_('member_delete'),
                            'delete'
                        );
                    }
                }

                $name = self::escape($member->getPerson()->getFullname());

                echo "
                <tr><td>$editButton</td>
                    <td>$name</td>
                    <td>{$member->getStartDate(DATE_FORMAT)} - {$member->getEndDate(DATE_FORMAT)}</td>
                    <td>$deleteButton</td>
                </tr>
                ";
            }
        ?>
        </tbody>
    </table>
</section>
