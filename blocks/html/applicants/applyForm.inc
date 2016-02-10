<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Applicant $this->applicant
 */
use Application\Models\CommitteeTable;
$table = new CommitteeTable();
$committees = $table->find();

$fields = ['id', 'firstname', 'lastname', 'email', 'phone'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = self::escape($this->applicant->$get());
}
?>
<section>
    <h1><?= $this->_('apply'); ?></h1>

    <form method="post" action="<?= BASE_URI; ?>/applicants/apply" enctype="multipart/form-data">
        <fieldset>
            <?php
                include APPLICATION_HOME.'/blocks/html/people/partials/personalInfoFields.inc';
            ?>
            <dl class="fn1-input-field">
                <dt><label for="committees">
                        <?= $this->_(['committee', 'committees', count($committees)]); ?>
                    </label>
                </dt>
                <dd><select name="committees[]" id="committees" multiple="true">
                    <?php
                        foreach ($committees as $c) {
                            $name = self::escape($c->getName());

                            echo "<option value=\"{$c->getId()}\">$name</option>";
                        }
                    ?>
                    </select>
                </dd>
            </dl>
            <dl class="fn1-input-field">
                <dt><label for="media"><?= $this->_('media'); ?></label>
                <dd><input type="file" name="mediafile" /></dd>
            </dl>

        <?php
            $helper = $this->template->getHelper('saveAndCancelButtons');
            echo $helper->saveAndCancelButtons(BASE_URI);
        ?>
        </fieldset>
    </form>
</section>
