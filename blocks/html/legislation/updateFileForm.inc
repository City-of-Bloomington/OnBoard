<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param LegislationFile $this->legislationFile
 */
use Application\Models\Legislation\LegislationFile;
use Web\Block;

$fields = ['id', 'legislation_id', 'filename'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = parent::escape($this->legislationFile->$get());
}

$heading = $id ? $this->_('legislationFile_edit') : $this->_('legislationFile_add');
?>
<section>
    <header>
        <h1><?= $heading; ?></h1>
    </header>
    <form method="post" enctype="multipart/form-data">
        <fieldset>
            <input type="hidden" name="legislationFile_id" value="<?= $id ?>" />
            <input type="hidden" name="legislation_id"     value="<?= $legislation_id; ?>" />
            <?php
                $h = $this->template->getHelper('field');

                $downloadUrl = parent::generateUri('legislationFiles.download')."?legislationFile_id=$id";
                $currentFile = $filename ? "<a href=\"$downloadUrl\">$filename</a>" : '';

                $accept = [];
                foreach (LegislationFile::$mime_types as $mime=>$ext) { $accept[] = ".$ext"; }

                $help = new Block('files/partials/uploadHelp.inc');
                $h    = $this->template->getHelper('field');
                echo $h->field([
                    'id'    => 'legislationFile',
                    'name'  => 'legislationFile',
                    'label' => $this->_('legislationFile'),
                    'type'  => 'file',
                    'value' => $currentFile,
                    'attr'  => ['accept' => implode(',', $accept)],
                    'help'  => $help->render('html', $this->template)
                ]);

                $h = $this->template->getHelper('saveAndCancelButtons');
                echo $h->saveAndCancelButtons($_SESSION['return_url']);
            ?>
        </fieldset>
    </form>
</section>
