<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->members
 *
 * @param Committee $this->committee
 * @param Seat $this->seat
 * @param string $this->title
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
?>
<section class="membersList">
    <table>
        <tbody>
        <?php
            $helper = $this->template->getHelper('buttonLink');

            foreach ($this->members as $member) {
                $editButton   = '';
                $deleteButton = '';
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
    <?php
        if (isset($this->committee) && Person::isAllowed('terms', 'add')) {

            $uri = BASE_URI."/members/update?committee_id={$this->committee->getId()}";
            if (isset($this->seat)) { $uri.= ";seat_id={$this->seat->getId()}"; }
            if (isset($this->term)) {
                $uri.= ";term_id={$this->term->getId()}";
            }

            echo $helper->buttonLink( $uri, $this->_('member_add'), 'add');
        }
    ?>
</section>
