<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Member $this->member
 * @param Member $this->newMember
 */
?>
<section    class="fn1-uiBlock">
    <header class="fn1-uiBlock-header">
        <h1 class="fn1-uiBlock-title"><?= $this->_('member_continue'); ?></h1>
    </header>
    <div    class="fn1-uiBlock-content">
        <form method="post" action="<?= BASE_URI; ?>/members/reappoint">
            <fieldset>
                <input name="member_id" type="hidden" value="<?= $this->member->getId(); ?>" />

                <dl><dt><label><?= $this->_('confirm'); ?></label></dt>
                    <dd><label>
                            <input name="confirm" type="radio" value="yes" />
                            <?= $this->_('yes'); ?>
                        </label>
                        <label>
                            <input name="confirm" type="radio" value="no" checked="true" />
                            <?= $this->_('no'); ?>
                        </label>
                    </dd>
            </fieldset>
            <?php
                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(
                    BASE_URI.'/committees/members?committee_id='.$this->newMember->getCommittee_id()
                );
            ?>
        </form>
    </div>
</section>