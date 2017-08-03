<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Legislation $this->legislation
 */
use Application\Models\CommitteeTable;
use Application\Models\Legislation\TypesTable;

$fields = ['id', 'committee_id', 'type_id', 'year', 'number', 'title', 'synopsis'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = parent::escape($this->legislation->$get());
}

$heading = $id ? $this->_('legislation_edit') : $this->_('legislation_add');
?>
<section>
    <header>
        <h1><?= $heading; ?></h1>
    </header>
    <form method="post" action="<?= BASE_URI; ?>/legislation/update">
        <fieldset>
            <input name="id" type="hidden" value="<?= $id; ?>" />
        <?php
            $h = $this->template->getHelper('field');

            echo $h->field([
                'name'     => 'year',
                'id'       => 'year',
                'label'    => $this->_('year'),
                'value'    => $year,
                'type'     => 'number',
                'required' => true
            ]);

            $options = [];
            $table   = new TypesTable();
            $list    = $table->find();
            foreach ($list as $t) { $options[] = ['value'=>$t->getId(), 'label'=>parent::escape($t->getName())]; }
            echo $h->field([
                'name'     => 'type_id',
                'id'       => 'type_id',
                'label'    => $this->_('type'),
                'type'     => 'select',
                'options'  => $options,
                'required' => true
            ]);

            $options = [];
            $table   = new CommitteeTable();
            $list    = $table->find(['legislative'=>true]);
            foreach ($list as $t) { $options[] = ['value'=>$t->getId(), 'label'=>parent::escape($t->getName())]; }
            echo $h->field([
                'name'     => 'committee_id',
                'id'       => 'committee_id',
                'label'    => $this->_('committee'),
                'type'     => 'select',
                'options'  => $options,
                'required' => true
            ]);

            echo $h->field([
                'name'     => 'number',
                'id'       => 'number',
                'label'    => $this->_('number'),
                'value'    => $number,
                'required' => true
            ]);
            echo $h->field([
                'name'     => 'title',
                'id'       => 'title',
                'label'    => $this->_('title'),
                'value'    => $title,
                'type'     => 'textarea',
                'required' => true
            ]);
            echo $h->field([
                'name'     => 'synopsis',
                'id'       => 'synopsis',
                'label'    => $this->_('synopsis'),
                'value'    => $synopsis,
                'type'     => 'textarea'
            ]);

            $h = $this->template->getHelper('saveAndCancelButtons');
            echo $h->saveAndCancelButtons($_SESSION['return_url']);
        ?>
        </fieldset>
    </form>
</section>
