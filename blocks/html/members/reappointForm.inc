<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Member $this->member
 * @param Member $this->newMember
 */
?>
<section    class="fn1-uiBlock">
    <div    class="fn1-uiBlock-content">
        <form method="post" action="<?= BASE_URI; ?>/members/reappoint">
            <input name="member_id" type="hidden" value="<?= $this->member->getId(); ?>" />
            <input name="confirm"   type="hidden" value="yes" />
            <div class="fn1-btn-container">
                <?php
                    $helper = $this->template->getHelper('saveAndCancelButtons');
                    echo $helper->saveAndCancelButtons(
                        BASE_URI.'/committees/members?committee_id='.$this->newMember->getCommittee_id()
                    );
                ?>
            </div>
        </form>
    </div>
</section>
