<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
 * @param Member $this->newMember
 * @param Member $this->currentMember (optional)
 */
use Application\Models\AppointerTable;
use Application\Models\Seat;
use Blossom\Classes\Block;
use Michelf\MarkdownExtra;
?>
<form  class="fn1-uiBlock-content" method="post" action="<?= BASE_URI; ?>/members/appoint">
    <?php if ($this->currentMember): ?>
        <section    class="fn1-uiBlock">
            <?= $this->_include('members/partials/appointCurrentMemberHeader.inc'); ?>
            <div class="fn1-uiBlock-content">
                <p><?php
                        $name = $this->currentMember->getPerson()->getFullname();
                        echo sprintf($this->_('endServiceHelpText', 'messages'), $name, $name);
                    ?>
                </p>
                <?= $this->_include('members/partials/memberEndFields.inc'); ?>
            </div>
        </section>
    <?php endif ?>

    <section    class="fn1-uiBlock">
        <header class="fn1-uiBlock-header">
            <h1 class="fn1-uiBlock-title"><?= sprintf($this->_('appointToSeat', 'messages'), $this->newMember->getCommittee()->getName()); ?></h1>
        </header>
        <?php
            $seat = $this->newMember->getSeat();
            if ($seat) {
                $requirements = $seat->getRequirements();
                if ($requirements) {
                    $markdown     = new MarkdownExtra();
                    $requirements = $markdown->transform($requirements);
                    echo "
                    <aside class=\"onboard-contextInfo\">
                        <dl class=\"fn1-output-field\">
                            <dt>{$this->_(['requirement', 'requirements', 2])}</dt>
                            <dd>$requirements</dd>
                        </dl>
                    </aside>
                    ";
                }
            }
        ?>
        <div class="fn1-uiBlock-content">
            <input name="newMember[committee_id]" type="hidden" value="<?= $this->newMember->getCommittee_id(); ?>" />
            <input name="newMember[seat_id]"      type="hidden" value="<?= $this->newMember->getSeat_id(); ?>" />

            <?php
                if ($seat) {
                    if ($seat->getType() === 'termed') {
                        $block = new Block('terms/recentHolders.inc', ['seat'=>$seat]);
                        echo $block->render('html', $this->template);
                    }
                }
            ?>
            <h2><?= $this->_('new_member') ?></h2>
            <dl class="fn1-input-field--select">
                <dt><label for="newMember-person_id" class="required"><?= $this->_(['person', 'people', 1]); ?></label></dt>
                <dd><?php
                        $person = $this->newMember->getPerson();
                        $helper = $this->template->getHelper('personChooser');
                        echo $helper->personChooser('newMember[person_id]', 'newMember-person_id', $person);
                    ?>
                </dd>
            </dl>
            <?php
                if ($seat) {
                    if ($seat->getType() === 'termed') {
                        $block = new Block('members/partials/termPicker.inc', ['seat'=>$seat]);
                        echo $block->render('html', $this->template);
                    }
                }
            ?>
            <dl class="fn1-input-field">
                <dt><label for="newMember-startDate"><?= $this->_('startDate'); ?></label></dt>
                <dd><?php
                        $picker = $this->template->getHelper('datePicker');
                        echo $picker->datePicker(
                            'newMember[startDate]',
                            'newMember-startDate',
                            $this->newMember->getStartDate('U')
                        );
                    ?>
                </dd>
            </dl>

            <div class="fn1-btn-container">
            <?php
                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(
                    BASE_URI.'/committees/members?committee_id='.$this->newMember->getCommittee_id()
                );
            ?>
            </div>
        </div>
    </section>
</form>
