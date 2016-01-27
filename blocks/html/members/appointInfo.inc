<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Member $this->newMember
 * @param Member $this->currentMember (optional)
 */
 $seat = $this->newMember->getSeat();
 $term = $this->newMember->getTerm();
?>
<section class="fn1-uiBlock">
    <?= $this->_include('html/members/partials/appointNewMemberHeader.inc'); ?>
    <div class="fn1-uiBlock-content">
        <dl class="fn1-output-field">
            <dt><?= $this->_('seat'); ?></dt>
            <dd><?php
                    if ($seat) { echo self::escape($seat->getName()); }
                ?>
            </dd>
        </dl>

        <dl class="fn1-output-field">
            <dt><?= $this->_('appointed_by'); ?></dt>
            <dd><?php
                    echo self::escape($seat->getAppointer());
                ?>
            </dd>
        </dl>

        <dl class="fn1-output-field">
            <dt><?= $this->_('term'); ?></dt>
            <dd><?php
                    echo "{$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}";
                ?>
            </dd>
        </dl>

    </div>
</section>
