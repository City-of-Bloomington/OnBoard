<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Member $this->newMember
 * @param Member $this->currentMember (optional)
 */
use Application\Models\AppointerTable;
use Application\Models\Seat;
?>
<section    class="fn1-uiBlock">
    <header class="fn1-uiBlock-header">
        <h1 class="fn1-uiBlock-title"><?= $this->_('member_add'); ?></h1>
    </header>
    <div    class="fn1-uiBlock-content">
        <form method="post" action="<?= BASE_URI; ?>/seats/appoint">
            <?php
                if ($this->currentMember) {
                    include APPLICATION_HOME.'/blocks/html/members/partials/memberEndFieldset.inc';
                }
            ?>

            <fieldset><legend><?= $this->_('member_add'); ?></legend>
                <input name="newMember[seat_id]" type="hidden" value="<?= $this->newMember->getSeat_id(); ?>" />

                <table>
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
                                $options = '';
                                foreach ($list as $term) {
                                    $dates   = "{$term->getStartDate(DATE_FORMAT)} - {$term->getEndDate(DATE_FORMAT)}";
                                    $options.= "<option value=\"{$term->getId()}\">$dates</option>";
                                }

                                echo "
                                <tr><td><label   for=\"newMember-term_id\" class=\"required\">{$this->_('term')}</label></td>
                                    <td><select name=\"newMember[term_id]\" id=\"newMember-term_id\">
                                            $options
                                        </select>
                                    </td>
                                </tr>
                                ";
                            }
                        }
                    ?>
                    <tr><td><label for="newMember-person_id" class="required"><?= $this->_(['person', 'people', 1]); ?></label></td>
                        <td><?php
                                $person = $this->newMember->getPerson();
                                $helper = $this->template->getHelper('personChooser');
                                echo $helper->personChooser('newMember[person_id]', 'newMember-person_id', $person);
                            ?>
                        </td>
                    </tr>
                    <tr><td><label for="newMember-startDate"><?= $this->_('startDate'); ?></label></td>
                        <td><input name="newMember[startDate]" id="newMember-startDate"
                                value="<?= $this->newMember->getStartDate(DATE_FORMAT); ?>" />
                        </td>
                    </tr>
                </table>
            </fieldset>

            <fieldset>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(
                    BASE_URI.'/committees/members?committee_id='.$this->newMember->getCommittee_id()
				);
			?>
            </fieldset>
        </form>
    </div>
</section>
