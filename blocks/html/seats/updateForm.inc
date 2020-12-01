<?php
/**
 * @copyright 2009-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/agpl.html GNU/AGPL, see LICENSE
 * @param Seat $this->seat
 */
use Application\Models\AppointerTable;
use Application\Models\Seat;

$fields = [
    'id', 'type', 'code', 'committee_id', 'appointer_id',
    'name', 'requirements', 'termLength', 'termModifier', 'voting', 'takesApplications'
];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = $this->seat->$get();
}

$title  = $this->seat->getId() ? $this->_('seat_edit') : $this->_('seat_add');
$picker = $this->template->getHelper('datePicker');
?>
<section>
    <header>
        <h1><?= $title; ?></h1>
    </header>
    <form method="post">
        <fieldset><legend><?= $this->_('info_seat'); ?></legend>
            <input name="seat_id"       type="hidden" value="<?= $id; ?>" />
            <input name="committee_id"  type="hidden" value="<?= $committee_id; ?>" />
            <?php
                $helper = $this->template->getHelper('field');

                echo $helper->field(['label'=>$this->_('code'), 'name'=>'code', 'id'=>'code', 'value'=>$code ]);
                echo $helper->field(['label'=>$this->_('name'), 'name'=>'name', 'id'=>'name', 'value'=>$name, 'required'=>true]);

                $options = [];
                $table   = new AppointerTable();
                $list    = $table->find();
                foreach ($list as $a) { $options[] = ['value'=>$a->getId(), 'label'=>parent::escape($a->getName())]; }
                echo $helper->field([
                    'name'     => 'appointer_id',
                    'id'       => 'appointer_id',
                    'label'    => $this->_('appointer'),
                    'value'    => $appointer_id,
                    'type'     => 'select',
                    'options'  => $options,
                    'required' => true
                ]);

                echo $helper->field([
                    'label'    => $this->_('startDate'),
                    'name'     => 'startDate',
                    'id'       => 'startDate',
                    'type'     => 'date',
                    'value'    => new \DateTime($this->seat->getStartDate()),
                    'required' => true
                ]);

                echo $helper->field([
                    'name'    => 'voting',
                    'label'   => $this->_('voting'),
                    'value'   => $voting,
                    'type'    => 'radio',
                    'options' => [
                        ['value'=>1, 'label'=>$this->_('yes')],
                        ['value'=>0, 'label'=>$this->_('no' )]
                    ]
                ]);
                echo $helper->field([
                    'name'    => 'takesApplications',
                    'label'   => $this->_('takesApplications'),
                    'value'   => $takesApplications,
                    'type'    => 'radio',
                    'options' => [
                        ['value'=>1, 'label'=>$this->_('yes')],
                        ['value'=>0, 'label'=>$this->_('no' )]
                    ]
                ]);
            ?>
        </fieldset>

        <fieldset><legend><?= $this->_('info_term'); ?></legend>
            <?php
                $options = [];
                foreach (Seat::$types as $t) { $options[] = ['value'=>$t, 'label'=>$this->_($t)]; }
                echo $helper->field([
                    'name'     => 'type',
                    'id'       => 'type',
                    'label'    => $this->_('type'),
                    'value'    => $type,
                    'type'     => 'select',
                    'options'  => $options,
                    'required' => true
                ]);

                $options = [];
                foreach (Seat::$termIntervals as $interval => $label) { $options[] = ['value'=>$interval, 'label'=>$label]; }
                echo $helper->field([
                    'name'    => 'termLength',
                    'id'      => 'termLength',
                    'label'   => $this->_('termLength'),
                    'value'   => $termLength,
                    'type'    => 'select',
                    'options' => $options
                ]);

                $options = [['value'=>'']];
                foreach (Seat::$termModifiers as $modifier => $label) { $options[] = ['value'=>$modifier, 'label'=>$label]; }
                echo $helper->field([
                    'name'    => 'termModifier',
                    'id'      => 'termModifier',
                    'label'   => $this->_('termModifier'),
                    'value'   => $termModifier,
                    'type'    => 'select',
                    'options' => $options
                ]);
            ?>
        </fieldset>

        <fieldset><legend><?= $this->_(['requirement', 'requirements', 2]); ?></legend>
            <textarea name="requirements"><?= $requirements; ?></textarea>
        </fieldset>

        <fieldset>
        <?php
            $helper = $this->template->getHelper('saveAndCancelButtons');
            $uri    = $id
                      ? parent::generateUri('seats.view')."?seat_id=$id"
                      : parent::generateUri('committees.members')."?committee_id=$committee_id";
            echo $helper->saveAndCancelButtons($uri);
        ?>
        </fieldset>
    </form>
</section>
