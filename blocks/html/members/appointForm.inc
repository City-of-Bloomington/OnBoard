<?php
/**
 * @copyright 2009-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE
 * @param Member $this->newMember
 * @param Member $this->currentMember (optional)
 */
use Application\Models\AppointerTable;
use Application\Models\Seat;
use Web\Block;
use Michelf\MarkdownExtra;
?>
<form method="post">
    <?php if ($this->currentMember): ?>
        <section>
            <header>
                <h1><?= $this->_('retirePrevious'); ?></h1>
            </header>
            <div>
                <p><?php
                        $name = $this->currentMember->getPerson()->getFullname();
                        echo sprintf($this->_('endServiceHelpText', 'messages'), $name, $name);
                    ?>
                </p>
                <?php $this->_include('members/partials/memberEndFields.inc'); ?>
            </div>
        </section>
    <?php endif ?>

    <section>
        <header>
            <h1><?= sprintf($this->_('appointToSeat', 'messages'), $this->newMember->getCommittee()->getName()); ?></h1>
        </header>
        <?php
            $seat = $this->newMember->getSeat();
            if ($seat) {
                $requirements = $seat->getRequirements();
                if ($requirements) {
                    $markdown     = new MarkdownExtra();
                    $requirements = $markdown->transform($requirements);
                    echo "
                    <aside>
                        <dl><dt>{$this->_(['requirement', 'requirements', 2])}</dt>
                            <dd>$requirements</dd>
                        </dl>
                    </aside>
                    ";
                }
            }
        ?>
        <div>
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
            <?php
                $helper = $this->template->getHelper('field');

                echo $helper->field([
                    'name'     => 'newMember[person_id]',
                    'id'       => 'newMember-person_id',
                    'label'    => $this->_('person'),
                    'value'    => $this->newMember->getPerson(),
                    'type'     => 'person',
                    'required' => true
                ]);

                if ($seat) {
                    if ($seat->getType() === 'termed') {
                        $block = new Block('members/partials/termPicker.inc', ['seat'=>$seat]);
                        echo $block->render('html', $this->template);
                    }
                }

                echo $helper->field([
                    'name'     => 'newMember[startDate]',
                    'id'       => 'newMember-startDate',
                    'label'    => $this->_('startDate'),
                    'value'    => $this->newMember->getStartDate('U'),
                    'type'     => 'date',
                    'required' => true
                ]);
            ?>

            <div>
            <?php
                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(
                    parent::generateUri('committees.members').'?committee_id='.$this->newMember->getCommittee_id()
                );
            ?>
            </div>
        </div>
    </section>
</form>
