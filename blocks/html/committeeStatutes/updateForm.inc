<?php
/**
 * @copyright 2016-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param CommitteeStatute $this->committeeStatute
 */
$fields = ['id', 'committee_id', 'citation', 'url'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = self::escape($this->committeeStatute->$get());
}

$committee = self::escape($this->committeeStatute->getCommittee()->getName());
$title     = $id ? $this->_('committeeStatute_edit') : $this->_('committeeStatute_add');
?>
<section>
    <header>
        <h1><?= $committee; ?></h1>
    </header>

    <form method="post">
        <fieldset><legend><?= $title; ?></legend>
            <input name="committeeStatute_id" type="hidden" value="<?= $id; ?>" />
            <input name="committee_id"        type="hidden" value="<?= $committee_id; ?>" />
            <?php
                $h = $this->template->getHelper('field');

                foreach (['citation', 'url'] as $f) {
                    echo $h->field([
                        'name'=>$f, 'id'=>$f, 'label'=>$this->_($f), 'value'=>$$f
                    ]);
                }

                $h = $this->template->getHelper('saveAndCancelButtons');
                echo $h->saveAndCancelButtons(parent::generateUri('committees.info')."?committee_id=$committee_id");
            ?>
        </fieldset>
    </form>
</section>
