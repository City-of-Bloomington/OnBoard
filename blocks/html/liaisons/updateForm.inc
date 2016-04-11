<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Liaison $this->liaison
 */
use Application\Models\Liaison;

$fields = ['id', 'type', 'committee_id'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = self::escape($this->liaison->$get());
}
?>
<section>
    <h1><?= $this->_('liaison_add'); ?></h1>
    <form method="post" action="<?= BASE_URI; ?>/liaisons/update">
        <fieldset>
            <input name="liaison_id"   type="hidden" value="<?= $id; ?>" />
            <input name="committee_id" type="hidden" value="<?= $committee_id; ?>" />

            <dl class="fn1-input-field--select">
                <dt><label   for="type"><?= $this->_('type'); ?></label></dt>
                <dd><select name="type" id="type" class="required">
                    <?php
                        foreach (Liaison::$types as $t) {
                            $selected = $type === $t ? 'selected="selected"' : '';
                            $name = $this->_($t);
                            echo "<option value=\"$t\" $selected>$name</option>";
                        }
                    ?>
                    </select>
                </dd>
            </dl>

            <dl class="fn1-input-field--select">
                <dt><label for="person_id"><?= $this->_('person'); ?></label></dt>
                <dd><?php
                        $helper = $this->template->getHelper('personChooser');
                        echo $helper->personChooser('person_id', 'person_id', $this->liaison->getPerson());
                    ?>
                </dd>
            </dl>

            <?php
                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(
                    BASE_URI.'/committees/info?committee_id='.$committee_id
                );
            ?>
        </fieldset>
    </form>
</section>
