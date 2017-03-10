<?php
/**
 * @copyright 2009-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @param Committee $this->committee
 */
use Application\Models\Committee;
use Application\Models\DepartmentTable;
use Application\Models\Person;
use Blossom\Classes\View;

$fields = [
    'type', 'yearFormed',
    'name', 'statutoryName', 'website', 'calendarId',
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
<section>
    <header>
        <h1><?= $this->_($title); ?></h1>
    </header>
    <form method="post" action="<?= BASE_URI; ?>/committees/update">
        <fieldset><legend><?= $this->_('committee_info'); ?></legend>
            <input name="committee_id" type="hidden" value="<?= $this->committee->getId(); ?>" />

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

                $fields = [
                    'name', 'yearFormed', 'statutoryName', 'calendarId',
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
                    echo $helper->field($p);
                }
            ?>

        </fieldset>

        <fieldset><legend><?= $this->_(['department', 'departments', 2]); ?></legend>
            <select name="departments[]" id="departments" multiple="true" size="8">
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
