<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->members
 * @param Committee $this->committee
 * @param Seat $this->seat
 */
use Application\Models\Person;
use Application\Templates\Helpers\ButtonLink;
?>
<div>
    <h2><?= $this->_(['member', 'members', 2]); ?></h2>
    <table>
        <thead>
            <tr><th></th>
                <th><?= $this->_('name'); ?></th>
                <th><?= $this->_('seat'); ?></th>
                <th><?= $this->_('term'); ?></th>
                <th><?= $this->_('startDate'); ?></th>
                <th><?= $this->_('endDate'); ?></th>
                <th><?= $this->_(['office', 'offices', 2]); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
            $userCanEdit = Person::isAllowed('members', 'edit');
            $helper = $this->template->getHelper('buttonLink');

            foreach ($this->members as $member) {
                $editButton = '';
                if ($userCanEdit) {
                    $editButton = $helper->buttonLink(
                        BASE_URI."/members/update?term_id={$term->getId()}",
                        $this->_('member_edit'),
                        'edit'
                    );
                }

                $seat = $member->getSeat_id() ? self::escape($member->getSeat()->getName()) : '';
                $t = $member->getTerm();
                if ($t) {
                    $term = "{$t->getStartDate(DATE_FORMAT)} - {$t->getEndDate(DATE_FORMAT)}";
                }

                echo "
                <tr><td>$editButton</td>
                    <td>$seat</td>
                    <td>$term</td>
                    <td>{$member->getStartDate(DATE_FORMAT)}</td>
                    <td>{$member->getEndDate(DATE_FORMAT)}</td>
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
            if (isset($this->term)) { $uri.= ";term_id={$this->term->getId()}"; }

            echo $helper->buttonLink( $uri, $this->_('member_add'), 'add');
        }
    ?>
</div>