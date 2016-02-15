<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Applicant $this->applicant
 * @param Committee $this->committee (optional)
 */
use Application\Models\Applicant;
use Application\Models\CommitteeTable;

$table = new CommitteeTable();
$committees = $table->find();

$fields = [
    'id', 'firstname', 'lastname', 'email', 'phone',
    'address', 'city', 'zip',
    'citylimits', 'occupation', 'referredFrom', 'referredOther', 'interest', 'qualifications'
];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = self::escape($this->applicant->$get());
}

$title = $this->committee
    ? sprintf($this->_('applyToCommittee', 'messages'), self::escape($this->committee->getName()))
    : $this->_('apply');
?>
<section>
    <h1><?= $title ?></h1>

    <form method="post" action="<?= BASE_URI; ?>/applicants/apply" id="applyForm">
        <fieldset>
        <?php
            include APPLICATION_HOME.'/blocks/html/people/partials/personalInfoFields.inc';

            foreach (['address', 'city', 'zip'] as $f) {
                echo "
                <dl class=\"fn1-input-field\">
                    <dt><label for=\"$f\">{$this->_($f)}</label></dt>
                    <dd><input name=\"$f\" id=\"$f\" value=\"{$$f}\" /></dd>
                </dl>
                ";
            }
            if ($this->committee) {
                echo "
                <input name=\"committee_id\" type=\"hidden\" value=\"{$this->committee->getId()}\" />
                <input name=\"committees[]\" type=\"hidden\" value=\"{$this->committee->getId()}\" />
                ";
            }
        ?>
        </fieldset>
        <?php
            if (!$this->committee) {
                echo "
                <fieldset id=\"committees\">
                    <dl class=\"fn1-input-field--checkbox\">
                        <dt>{$this->_(['committee', 'committees', count($committees)])}</dt>
                ";
                    $chosen = [];
                    if (isset($_REQUEST['committees'])) { $chosen = $_REQUEST['committees']; }

                    foreach ($committees as $c) {
                        $checked = in_array($c->getId(), $chosen)
                            ? 'checked="true"'
                            : '';
                        $name = self::escape($c->getName());
                        echo "
                        <dd><label>
                                <input name=\"committees[]\" type=\"checkbox\" value=\"{$c->getId()}\" $checked />
                                <span>$name</span>
                            </label>
                        </dd>
                        ";
                    }
                echo "
                    </dl>
                </fieldset>
                ";
            }
        ?>

        <fieldset>
            <dl class="fn1-input-field">
                <dt><label><?= $this->_('citylimits'); ?></label></dt>
                <dd><label>
                        <input name="citylimits" type="radio" value="1" <?= $citylimits ? 'checked="true"' : ''; ?> />
                        <?= $this->_('yes'); ?>
                    </label>
                    <label>
                        <input name="citylimits" type="radio" value="0" <?= $citylimits ? '' : 'checked="true"'; ?> />
                        <?= $this->_('no'); ?>
                    </label>
                </dd>
            </dl>

            <dl class="fn1-input-field">
                <dt><label for="occupation"><?= $this->_('occupation'); ?></label></dt>
                <dd><input name="occupation" id="occupation" value="<?= $occupation; ?> " /></dd>
            </dl>

            <dl class="fn1-input-field">
                <dt><label for="referredFrom"><?= $this->_('referredFrom'); ?></label></dt>
                <dd><select name="referredFrom" id="referredFrom"><option value=""></option>
                    <?php
                        $knownReferralOption = false;
                        foreach (Applicant::$referralOptions as $o) {
                            $selected = $referredFrom === $o
                                ? 'selected="true"'
                                : '';
                            echo "<option $selected>$o</option>";
                        }
                    ?>
                    </select>
                </dd>
            </dl>

            <dl class="fn1-input-field">
                <dt><label for="referredOther"><?= $this->_('referredOther'); ?></label></dt>
                <dd><input name="referredOther" id="referredOther" value="<?= $referredOther; ?>" /></dd>
            </dl>

        </fieldset>

        <fieldset><legend><?= $this->_('interest'); ?></legend>
            <textarea name="interest"><?= $interest; ?></textarea>
        </fieldset>

        <fieldset><legend><?= $this->_('qualifications'); ?></legend>
            <textarea name="qualifications"><?= $qualifications; ?></textarea>
        </fieldset>

        <fieldset>
        <?php
            $helper = $this->template->getHelper('saveAndCancelButtons');
            echo $helper->saveAndCancelButtons(BASE_URI);
        ?>
        </fieldset>
    </form>
</section>
