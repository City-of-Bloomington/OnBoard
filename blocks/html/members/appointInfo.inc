<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Member $this->newMember
 * @param Member $this->currentMember (optional)
 */
if ($this->currentMember) {
    echo "
    <section    class=\"fn1-uiBlock-content\">
        {$this->_include('html/members/partials/appointCurrentMemberHeader.inc')}
        <div    class=\"fn1-uiBlock-content\">
            <dl class=\"fn1-output-field\">
                <dt>{$this->_('endDate')}</dt>
                <dd>{$this->currentMember->getEndDate(DATE_FORMAT)}</dd>
            </dl>
        </div>
    </section>
    ";
}
?>
<section class="fn1-uiBlock">
    <?= $this->_include('html/members/partials/appointNewMemberHeader.inc'); ?>
    <div class="fn1-uiBlock-content">
        <dl class="fn1-output-field">
            <dt><?= $this->_('person'); ?></dt>
            <dd><?php
                    $person = $this->newMember->getPerson();
                    if ($person) { echo self::escape($person->getFullname()); }
                ?>
            </dd>
        </dl>

        <dl class="fn1-output-field">
            <dt><?= $this->_('term'); ?></dt>
            <dd><?php
                    $term = $this->newMember->getTerm();
                    echo "{$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}";
                ?>
            </dd>
        </dl>
    </div>
</section>