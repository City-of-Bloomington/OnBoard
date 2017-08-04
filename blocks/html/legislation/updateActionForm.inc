<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Action $this->action
 */
use Application\Models\Legislation\Action;

$fields = ['id', 'legislation_id', 'type_id', 'outcome', 'vote'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = parent::escape($this->action->$get());
}

$legislation = parent::escape($this->action->getLegislation()->getTitle());
$type        = parent::escape($this->action->getType()       ->getName());
?>
<section>
    <header>
        <h1><?= $legislation; ?></h1>
        <h2><?= $type; ?></h2>
    </header>
    <form method="post" action="<?= BASE_URI; ?>/legislationActions/update">
        <fieldset>
            <input name="id"             type="hidden" value="<?= $id; ?>" />
            <input name="legislation_id" type="hidden" value="<?= $legislation_id; ?>" />
            <input name="type_id"        type="hidden" value="<?= $type_id; ?>" />

            <?php
                $h = $this->template->getHelper('field');
                echo $h->field([
                    'name'    => 'actionDate',
                    'id'      => 'actionDate',
                    'label'   => $this->_('date'),
                    'type'    => 'date',
                    'value'   => $this->action->getActionDate('U'),
                    'required'=> true
                ]);

                echo $h->field([
                    'name'    => 'vote',
                    'id'      => 'vote',
                    'label'   => $this->_('vote'),
                    'value'   => $vote
                ]);

                $options = [['value'=>'']];
                foreach (Action::$outcomes as $c) { $options[] = ['value'=>$c]; }
                echo $h->field([
                    'name'    => 'outcome',
                    'id'      => 'outcome',
                    'label'   => $this->_('outcome'),
                    'value'   => $outcome,
                    'type'    => 'select',
                    'options' => $options
                ]);

                $h = $this->template->getHelper('saveAndCancelButtons');
                echo $h->saveAndCancelButtons(
                    BASE_URI.'/legislation/view?id='.$legislation_id
                );
            ?>
        </fieldset>
    </form>
</section>
