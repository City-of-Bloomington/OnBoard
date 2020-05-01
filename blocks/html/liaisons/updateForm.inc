<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
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
    <header>
        <h1><?= $this->_('liaison_add'); ?></h1>
    </header>
    <form method="post">
        <fieldset>
            <input name="liaison_id"   type="hidden" value="<?= $id; ?>" />
            <input name="committee_id" type="hidden" value="<?= $committee_id; ?>" />
            <?php
                $helper = $this->template->getHelper('field');

                $options = [];
                foreach (Liaison::$types as $t) { $options[] = ['value'=>$t, 'label'=>$this->_($t)]; }
                echo $helper->field([
                    'name'     => 'type',
                    'id'       => 'type',
                    'label'    => $this->_('type'),
                    'value'    => $type,
                    'type'     => 'select',
                    'options'  => $options,
                    'required' => true
                ]);

                echo $helper->field([
                    'name'     => 'person_id',
                    'id'       => 'person_id',
                    'label'    => $this->_('person'),
                    'value'    => $this->liaison->getPerson(),
                    'type'     => 'person',
                    'required' => true
                ]);

                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(
                    parent::generateUri('committees.info')."?committee_id=$committee_id"
                );
            ?>
        </fieldset>
    </form>
</section>
