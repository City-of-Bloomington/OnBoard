<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Member $this->currentMember
 */
?>
<section>
    <header>
        <h1><?= $this->_('member_end'); ?></h1>
    </header>

    <form method="post" action="<?= BASE_URI; ?>/members/resign">
        <fieldset>
        <?php
            $this->_include('members/partials/memberEndFields.inc');

            $helper = $this->template->getHelper('saveAndCancelButtons');
            echo $helper->saveAndCancelButtons(
                BASE_URI.'/committees/members?committee_id='.$this->currentMember->getCommittee_id()
            );
        ?>
        </fieldset>
    </form>
</section>
