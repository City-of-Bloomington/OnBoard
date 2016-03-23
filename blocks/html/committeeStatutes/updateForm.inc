<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
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
    <h1><?= $committee; ?></h1>

    <form method="post" action="<?= BASE_URI; ?>/committeeStatutes/update">
        <fieldset><legend><?= $title; ?></legend>
            <input name="committeeStatute_id" type="hidden" value="<?= $id; ?>" />
            <input name="committee_id"        type="hidden" value="<?= $committee_id; ?>" />
            <?php
                foreach (['citation', 'url'] as $f) {
                    echo "
                    <dl class=\"fn1-input-field\">
                        <dt><label  for=\"$f\">{$this->_($f)}</label></dt>
                        <dd><input name=\"$f\" id=\"$f\" value=\"{$$f}\" /></dd>
                    </dl>
                    ";
                }

                $h = $this->template->getHelper('saveAndCancelButtons');
                echo $h->saveAndCancelButtons(BASE_URI.'/committees/info?committee_id='.$committee_id);
            ?>
        </fieldset>
    </form>
</section>
