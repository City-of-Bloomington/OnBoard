<?php
/**
 * @copyright 2009-2022 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Committee $this->committee
 */
use Application\Models\Committee;
use Application\Models\DepartmentTable;
use Application\Models\Person;
use Web\View;



$fields = [
    'id',
    'type', 'yearFormed',
    'name', 'statutoryName', 'code', 'website', 'videoArchive', 'calendarId',
    'email', 'phone', 'address', 'city', 'state', 'zip',
    'description', 'meetingSchedule',
    'termEndWarningDays', 'applicationLifetime', 'legislative', 'alternates'
];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = View::escape($this->committee->$get());
}

$title = $id ? $this->_('committee_edit') : $this->_('committee_add');
?>
<section>
    <header>
        <h1><?= $this->_($title); ?></h1>
    </header>
    <form method="post">
        <fieldset><legend><?= $this->_('committee_info'); ?></legend>
            <input name="committee_id" type="hidden" value="<?= $id; ?>" />

            <?php
                $helper = $this->template->getHelper('field');

                $options = [];
                foreach (Committee::$types as $t) { $options[] = ['value'=>$t]; }
                echo $helper->field([
                    'name'    => 'type',
                    'id'      => 'type',
                    'label'   => $this->_('type'),
                    'type'    => 'select',
                    'value'   => $type,
                    'options' => $options
                ]);
            ?>
            <dl class="radio">
                <dt><?= $this->_('legislative'); ?></dt>
                <dd><label><input name="legislative" id="legislative" type="radio" value="1" <?= $legislative ? 'checked="checked"' : ''; ?> /> Yes</label></dd>
                <dd><label><input name="legislative" id="legislative" type="radio" value="0" <?= $legislative ? '' : 'checked="checked"'; ?> />  No</label></dd>
            </dl>
            <dl class="radio">
                <dt><?= $this->_('alternates'); ?></dt>
                <dd><label><input name="alternates" id="alternates" type="radio" value="1" <?= $alternates ? 'checked="checked"' : ''; ?> /> Yes</label></dd>
                <dd><label><input name="alternates" id="alternates" type="radio" value="0" <?= $alternates ? '' : 'checked="checked"'; ?> />  No</label></dd>
            </dl>

            <?php
                $fields = [
                    'name', 'yearFormed', 'statutoryName', 'code', 'calendarId',
                    'website', 'videoArchive', 'termEndWarningDays', 'applicationLifetime',
                ];
                foreach ($fields as $f) {
                    $p = [
                        'label' => $this->_($f),
                        'name'  => $f,
                        'id'    => $f,
                        'value' => $$f
                    ];
                    if ($f === 'endDate') { $p['type'] = 'date'; }
                    echo $helper->field($p);
                }
            ?>

        </fieldset>

        <fieldset><legend><?= $this->_(['department', 'departments', 2]); ?></legend>
            <dl><dt><?= $this->_(['department', 'departments', 2]); ?></dt>
                <dd><select name="departments[]" id="departments" multiple="true" size="8">
                        <option value=""></option>
                        <?php
                            $table = new DepartmentTable();
                            $list  = $table->find();
                            foreach ($list as $d) {
                                $name = self::escape($d->getName());
                                $selected = $this->committee->hasDepartment($d)
                                    ? 'selected="true"'
                                    : '';
                                echo "<option value=\"{$d->getId()}\" $selected>$name</option>";
                            }
                        ?>
                    </select>
                </dd>
            </dl>
        </fieldset>

        <fieldset><legend><?= $this->_('contactInfo'); ?></legend>
            <?php
                echo $helper->field([
                    'name'  => 'address',
                    'id'    => 'address',
                    'label' => $this->_('address'),
                    'value' => $address,
                    'type'  => 'textarea'

                ]);

                $fields = ['city', 'state', 'zip'];
                foreach ($fields as $f) {
                    echo $helper->field([
                        'label' => $this->_($f),
                        'name'  => $f,
                        'id'    => $f,
                        'value' => $$f
                    ]);
                }
                echo $helper->field([
                    'label' =>$this->_('email'),
                    'name'  =>'email',
                    'id'    =>'email',
                    'value' =>$email,
                    'type'  =>'email'
                ]);
                echo $helper->field([
                    'label' => $this->_('phone'),
                    'name'  => 'phone',
                    'id'    => 'phone',
                    'value' => $phone,
                    'type'  => 'tel'
                ]);

            ?>
        </fieldset>

        <fieldset><legend><?= $this->_('description'); ?></legend>
            <?php
                echo $helper->field([
                    'label' => $this->_('description'),
                    'name'  => 'description',
                    'id'    => 'description',
                    'type'  => 'textarea',
                    'value' => $description
                ]);
            ?>
        </fieldset>

        <fieldset><legend><?= $this->_('meetingSchedule'); ?></legend>
            <?php
                echo $helper->field([
                    'label' => $this->_('meetingSchedule'),
                    'name'  => 'meetingSchedule',
                    'id'    => 'meetingSchedule',
                    'type'  => 'textarea',
                    'value' => $meetingSchedule
                ]);
            ?>
        </fieldset>

        <fieldset><legend><?= $this->_('submit'); ?></legend>
        <?php
            $helper = $this->template->getHelper('saveAndCancelButtons');
            $uri    = $id
                    ? parent::generateUri('committees.info' )."?committee_id=$id"
                    : parent::generateUri('committees.index');
            echo $helper->saveAndCancelButtons($uri);
        ?>
        </fieldset>
    </form>
</section>
