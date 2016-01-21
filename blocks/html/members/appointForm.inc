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
?>
<section   class="fn1-uiBlock">
    <form  class="fn1-uiBlock-content" method="post" action="<?= BASE_URI; ?>/members/appoint">
        <fieldset>
            <?php
                if ($this->currentMember) {
                    $this->_include('html/members/partials/memberEndFieldset.inc');
                }
            ?>

            <input name="newMember[seat_id]" type="hidden" value="<?= $this->newMember->getSeat_id(); ?>" />

            <?php
                $seat = $this->newMember->getSeat();
                if ($seat->getType() === 'termed') {
                    $termLength = new \DateInterval($seat->getTermLength());

                    $currentTerm = $seat->getTerm(time());
                    if ($currentTerm) {
                        $d = new \DateTime();
                        $d->add($termLength);
                        $nextTerm = $seat->getTerm($d->format('U'));

                        $list = [$nextTerm, $currentTerm];

                        $d = new \DateTime();
                        $d->sub($termLength);
                        $previousTerm = $seat->getTerm($d->format('U'));
                        if ($previousTerm) {
                            $list[] = $previousTerm;
                        }

                        $block = new Block('terms/recentHolders.inc', ['terms'=>$list, 'disableButtons'=>true]);
                        echo $block->render('html', $this->template);
                    ?>

                    <h2><?= $this->_('member_add') ?></h2>
                    <dl class="fn1-input-field--select"><dt><label for="newMember-person_id" class="required"><?= $this->_(['person', 'people', 1]); ?></label></dt>
                        <dd><?php
                                $person = $this->newMember->getPerson();
                                $helper = $this->template->getHelper('personChooser');
                                echo $helper->personChooser('newMember[person_id]', 'newMember-person_id', $person);
                            ?>
                        </dd>
                    </dl>

                    <?php
                        $options = '';
                        foreach ($list as $term) {
                            if ($term->isVacant()) {
                                $dates   = "{$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}";
                                $options.= "<option value=\"{$term->getId()}\">$dates</option>";
                            }
                        }

                        echo "
                        <dl class=\"fn1-input-field--select\"><dt><label   for=\"newMember-term_id\" class=\"required\">{$this->_('term')}</label></dt>
                            <dd><select name=\"newMember[term_id]\" id=\"newMember-term_id\">
                                    $options
                                </select>
                            </dd>
                        </dl>
                        ";
                    }
                }
            ?>
            <dl class="fn1-input-field"><dt><label for="newMember-startDate"><?= $this->_('startDate'); ?></label></dt>
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
        </fieldset>
    </form>
</section>
