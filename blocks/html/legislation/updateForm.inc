<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Legislation $this->legislation
 */
use Application\Models\TagsTable;
use Application\Models\Legislation\StatusesTable;
use Application\Models\Legislation\TypesTable;

use Blossom\Classes\Block;

$fields = [
    'id', 'committee_id', 'type_id', 'parent_id', 'status_id',
    'year', 'number', 'title', 'synopsis', 'notes', 'amendsCode'
];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = parent::escape($this->legislation->$get());
}

?>
<section>
    <header>
        <?php
            $type = $this->legislation->getType();
            $heading = $type
                ? ($id
                    ? sprintf($this->_('edit_something', 'messages'), $type->getName())
                    : sprintf($this->_( 'add_something', 'messages'), $type->getName()))
                : ($id
                    ? $this->_('legislation_edit')
                    : $this->_('legislation_add'));


            $parent  = $this->legislation->getParent();
            if ($parent) {
                $block = new Block('legislation/partials/heading.inc', ['legislation'=>$parent]);
                echo $block->render('html', $this->template);
                echo "<h3>$heading</h3>";
            }
            else {
                echo "<h1>$heading</h1>";
            }
        ?>
    </header>
    <form method="post" action="<?= BASE_URI; ?>/legislation/update">
        <fieldset>
            <input name="id"           type="hidden" value="<?= $id;           ?>" />
            <input name="committee_id" type="hidden" value="<?= $committee_id; ?>" />
            <input name="parent_id"    type="hidden" value="<?= $parent_id;    ?>" />
        <?php
            $helper = $this->template->getHelper('field');

            echo $helper->field([
                'name'     => 'year',
                'id'       => 'year',
                'label'    => $this->_('year'),
                'value'    => $year,
                'type'     => 'number',
                'required' => true
            ]);

            $options = [];
            $table   = new TypesTable();
            $list    = $table->find(['subtype'=>$parent_id ? true : false]);
            foreach ($list as $t) { $options[] = ['value'=>$t->getId(), 'label'=>parent::escape($t->getName())]; }
            echo $helper->field([
                'name'     => 'type_id',
                'id'       => 'type_id',
                'label'    => $this->_('type'),
                'value'    => $type_id,
                'type'     => 'select',
                'options'  => $options,
                'required' => true
            ]);

            echo $helper->field([
                'name'     => 'number',
                'id'       => 'number',
                'label'    => $this->_('number'),
                'value'    => $number,
                'required' => true
            ]);

            echo $helper->field([
                'name'     => 'title',
                'id'       => 'title',
                'label'    => $this->_('title'),
                'value'    => $title,
                'type'     => 'textarea',
                'required' => true
            ]);

            echo $helper->field([
                'name'    => 'amendsCode',
                'label'   => $this->_('amendsCode'),
                'value'   => $amendsCode,
                'type'    => 'radio',
                'options' => [
                    ['value'=>1, 'label'=>$this->_('yes')],
                    ['value'=>0, 'label'=>$this->_('no' )]
                ]
            ]);

            echo $helper->field([
                'name'     => 'synopsis',
                'id'       => 'synopsis',
                'label'    => $this->_('synopsis'),
                'value'    => $synopsis,
                'type'     => 'textarea'
            ]);

            $options = [['value'=>'']];
            $table   = new StatusesTable();
            $list    = $table->find();
            foreach ($list as $s) { $options[] = ['value'=>$s->getId(), 'label'=>parent::escape($s->getName())]; }
            echo $helper->field([
                'name'    => 'status_id',
                'id'      => 'status_id',
                'label'   => $this->_('legislationStatus'),
                'value'   => $status_id,
                'type'    => 'select',
                'options' => $options
            ]);

            $options = [];
            $table   = new TagsTable();
            $list    = $table->find();
            foreach ($list as $t) { $options[] = ['value'=>$t->getId(), 'label'=>parent::escape($t->getName())]; }
            echo $helper->field([
                'name'    => 'tags',
                'label'   => $this->_(['tag', 'tags', count($list)]),
                'type'    => 'checkbox',
                'value'   => array_keys($this->legislation->getTags()),
                'options' => $options
            ]);

            echo $helper->field([
                'name'  => 'notes',
                'id'    => 'notes',
                'label' => $this->_('notes'),
                'type'  =>'textarea',
                'value' => $notes
            ]);

            $helper = $this->template->getHelper('saveAndCancelButtons');
            echo $helper->saveAndCancelButtons($_SESSION['return_url']);
        ?>
        </fieldset>
    </form>
</section>
