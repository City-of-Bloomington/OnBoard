<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Member $this->member
 * @param Member $this->newMember
 */
use Blossom\Classes\Block;

$seat = $this->newMember->getSeat();
$term = $this->newMember->getTerm();
?>
<section    class="fn1-uiBlock">
    <header class="fn1-uiBlock-header">
        <h1 class="fn1-uiBlock-title">
            <?= sprintf($this->_('reappointToSeat', 'messages'), $this->newMember->getPerson()->getFullName() , $this->newMember->getCommittee()->getName()); ?>
        </h1>
    </header>
    <div    class="fn1-uiBlock-content">
        <?php
            if ($seat) {
                if ($seat->getType() === 'termed') {
                    $block = new Block('terms/recentHolders.inc', ['seat'=>$seat]);
                    echo $block->render('html', $this->template);
                }
            }
        ?>
        <section class="fn1-uiBlock">
            <div class="fn1-uiBlock-content">
                <dl class="fn1-output-field">
                    <dt><?= $this->_('seat'); ?></dt>
                    <dd><?= self::escape($seat->getName()); ?></dd>
                </dl>

                <dl class="fn1-output-field">
                    <dt><?= $this->_('appointed_by'); ?></dt>
                    <dd><?= self::escape($seat->getAppointer()); ?></dd>
                </dl>

                <dl class="fn1-output-field">
                    <dt><?= $this->_('term'); ?></dt>
                    <dd><?= "{$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}"; ?></dd>
                </dl>
            </div>
        </section>

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
