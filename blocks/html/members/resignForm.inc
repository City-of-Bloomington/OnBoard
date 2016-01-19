<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Member $this->currentMember
 */
?>
<section    class="fn1-uiBlock">
    <header class="fn1-uiBlock-header">
        <h1 class="fn1-uiBlock-title"><?= $this->_('member_end'); ?></h1>
    </header>
    <div    class="fn1-uiBlock-content">
        <form method="post" action="<?= BASE_URI; ?>/members/resign">
            <?php
                include __DIR__.'/partials/memberEndFieldset.inc';
            ?>

            <fieldset>
            <?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(
                    BASE_URI.'/committees/members?committee_id='.$this->currentMember->getCommittee_id()
				);
            ?>
            </fieldset>
        </form>
    </div>
</section>
