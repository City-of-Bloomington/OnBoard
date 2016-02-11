<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Applicant $this->applicant
 */
use Application\Models\CommitteeTable;
$table = new CommitteeTable();
$committees = $table->find();

$fields = [
    'id', 'firstname', 'lastname', 'email', 'phone',
    'address', 'city', 'zip'
];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = self::escape($this->applicant->$get());
}
?>
<section>
    <h1><?= $this->_('apply'); ?></h1>

    <form method="post" action="<?= BASE_URI; ?>/applicants/apply">
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
            ?>
        </fieldset>

        <fieldset><legend><?= $this->_(['committee', 'committees', count($committees)]); ?></legend>
            <select name="committees[]" id="committees" multiple="true">
            <?php
                foreach ($committees as $c) {
                    $name = self::escape($c->getName());

                    echo "<option value=\"{$c->getId()}\">$name</option>";
                }
            ?>
            </select>
        </fieldset>

        <?php
            $helper = $this->template->getHelper('saveAndCancelButtons');
            echo $helper->saveAndCancelButtons(BASE_URI);
        ?>
        </fieldset>
    </form>
</section>
