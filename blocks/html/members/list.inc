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
<section    class="fn1-uiBlock">
    <header class="fn1-uiBlock-header">
        <h1 class="fn1-uiBlock-title">
            <?= $this->title ? $this->title : $this->_(['member', 'members', 2]); ?>
        </h1>
    </header>
    <table class="fn1-table">
        <thead>
            <tr><th></th>
                <th><?= $this->_('name'); ?></th>
                <th><?= $this->_('seat'); ?></th>
                <th><?= $this->_('term'); ?></th>
                <th><?= $this->_('startDate'); ?></th>
                <th><?= $this->_('endDate'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
            $userCanEdit = Person::isAllowed('members', 'edit') && !$this->disableButtons;
            $helper = $this->template->getHelper('buttonLink');

            foreach ($this->members as $member) {
                $editButton = '';
                if ($userCanEdit) {
                    $editButton = $helper->buttonLink(
                        BASE_URI."/members/update?member_id={$member->getId()}",
                        $this->_('member_edit'),
                        'edit'
                    );
                }

                $name = self::escape($member->getPerson()->getFullname());

                $seat = $member->getSeat_id() ? self::escape($member->getSeat()->getName()) : '';
                $t = $member->getTerm();
                if ($t) {
                    $term = "{$t->getStartDate(DATE_FORMAT)} - {$t->getEndDate(DATE_FORMAT)}";
                }

                echo "
                <tr><td>$editButton</td>
                    <td>$name</td>
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
        if (isset($this->committee)
            && !$this->disableButtons
            && Person::isAllowed('terms', 'add')) {

            $uri = BASE_URI."/members/update?committee_id={$this->committee->getId()}";
            if (isset($this->seat)) { $uri.= ";seat_id={$this->seat->getId()}"; }
            if (isset($this->term)) {
                $uri.= ";term_id={$this->term->getId()}";
            }

            echo $helper->buttonLink( $uri, $this->_('member_add'), 'add');
        }
    ?>
</section>
