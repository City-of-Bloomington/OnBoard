<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Application\Models\Committee;
use Application\Models\DepartmentTable;
use Application\Models\Person;
use Blossom\Classes\View;

$fields = [
    'type', 'yearFormed',
    'name', 'statutoryName', 'website',
    'email', 'phone', 'address', 'city', 'state', 'zip',
    'description', 'contactInfo', 'meetingSchedule',
    'termEndWarningDays', 'applicationLifetime'
];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = View::escape($this->committee->$get());
}

$title = $this->committee->getId() ? $this->_('committee_edit') : $this->_('committee_add');
?>
<section    class="fn1-uiBlock">
    <header class="fn1-uiBlock-header">
        <h1 class="fn1-uiBlock-title"><?= $this->_($title); ?></h1>
    </header>
    <form   class="fn1-uiBlock-content" method="post" action="<?= BASE_URI; ?>/committees/update">
        <fieldset><legend><?= $this->_('committee_info'); ?></legend>
            <input name="committee_id" type="hidden" value="<?= $this->committee->getId(); ?>" />

            <dl class="fn1-input-field">
                <dt><label   for="type" class="required"><?= $this->_('type'); ?></label></dt>
                <dd><select name="type">
                    <?php
                        foreach (Committee::$types as $t) {
                            $selected = $type === $t
                                ? 'selected="true"'
                                : '';
                            echo "<option value=\"$t\" $selected>$t</option>";
                        }
                    ?>
                    </select>
                </dd>
            </dl>
            <?php
                $field = $this->template->getHelper('field');

                $fields = [
                    'name', 'yearFormed', 'statutoryName',
                    'website', 'termEndWarningDays', 'applicationLifetime',
                ];
                foreach ($fields as $f) {
                    $p = [
                        'label' => $this->_($f),
                        'name'  => $f,
                        'id'    => $f,
                        'value' => $$f
                    ];
                    if ($f === 'endDate') { $p['type'] = 'date'; }
                    echo $field->field($p);
                }
            ?>

        </fieldset>

        <fieldset><legend><?= $this->_(['department', 'departments', 2]); ?></legend>
            <select name="departments[]" id="departments" multiple="true" size="8">
                <option value=""></option>
                <?php
                    $table = new DepartmentTable();
                    $list = $table->find();
                    foreach ($list as $d) {
                        $name = self::escape($d->getName());
                        $selected = $this->committee->hasDepartment($d)
                            ? 'selected="true"'
                            : '';
                        echo "<option value=\"{$d->getId()}\" $selected>$name</option>";
                    }
                ?>
            </select>
        </fieldset>

        <fieldset><legend><?= $this->_('contactInfo'); ?></legend>
            <dl class="fn1-input-field">
                <dt><label     for="address"><?= $this->_('address'); ?></label></dt>
                <dd><textarea name="address" id="address"><?= $address; ?></textarea></dd>
            </dl>
            <?php
                $fields = ['city', 'state', 'zip', 'email', 'phone'];
                foreach ($fields as $f) {
                    echo $field->field([
                        'label' => $this->_($f),
                        'name'  => $f,
                        'id'    => $f,
                        'value' => $$f
                    ]);
                }

            ?>
        </fieldset>

        <fieldset><legend><?= $this->_('description'); ?></legend>
            <textarea name="description" id="description" class="markdown"><?= $description; ?></textarea>
        </fieldset>

        <fieldset><legend><?= $this->_('meetingSchedule'); ?></legend>
            <textarea name="meetingSchedule" id="meetingSchedule"><?= $meetingSchedule; ?></textarea>
        </fieldset>

        <fieldset><legend><?= $this->_('contactInfo'); ?></legend>
            <textarea name="contactInfo" id="contactInfo"><?= $contactInfo; ?></textarea>
        </fieldset>

        <fieldset><legend><?= $this->_('submit'); ?></legend>
        <?php
            $helper = $this->template->getHelper('saveAndCancelButtons');

            $uri = $this->committee->getId()
                    ? BASE_URI."/committees/info?committee_id={$this->committee->getId()}"
                    : BASE_URI."/committees";
            echo $helper->saveAndCancelButtons($uri);
        ?>
        </fieldset>
    </form>
</section>
