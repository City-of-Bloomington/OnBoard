<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\CommitteeTable;
use Application\Models\Legislation\TypesTable;

$fields = ['year', 'type_id', 'committee_id'];
foreach ($fields as $f) {
    $$f = !empty($_GET[$f]) ? parent::escape($_GET[$f]) : '';
}
?>
<form method="get">
    <fieldset>
    <?php
        $h = $this->template->getHelper('field');

        $options = [['value'=>'']];
        $table   = new CommitteeTable();
        $list    = $table->find(['legislative'=>true]);
        foreach ($list as $t) { $options[] = ['value'=>$t->getId(), 'label'=>parent::escape($t->getName())]; }
        echo $h->field([
            'name'     => 'committee_id',
            'id'       => 'committee_id',
            'label'    => $this->_('committee'),
            'type'     => 'select',
            'value'    => $committee_id,
            'options'  => $options
        ]);

        echo $h->field([
            'name'     => 'year',
            'id'       => 'year',
            'label'    => $this->_('year'),
            'type'     => 'number',
            'value'    => $year
        ]);

        $options = [['value'=>'']];
        $table   = new TypesTable();
        $list    = $table->find(['subtype'=>false]);
        foreach ($list as $t) { $options[] = ['value'=>$t->getId(), 'label'=>parent::escape($t->getName())]; }
        echo $h->field([
            'name'     => 'type_id',
            'id'       => 'type_id',
            'label'    => $this->_('type'),
            'type'     => 'select',
            'value'    => $type_id,
            'options'  => $options
        ]);
    ?>
        <button type="submit" class="search"><?= $this->_('search'); ?></button>
    </fieldset>
</form>
