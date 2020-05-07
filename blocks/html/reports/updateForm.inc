<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Report $this->report
 */
declare (strict_types=1);
use Application\Models\Reports\Report;
use Web\Block;

foreach (['id', 'title', 'committee_id', 'filename'] as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = parent::escape($this->report->$get());
}

$heading = $id ? $this->_('report_edit') : $this->_('report_add');
$helper  = $this->template->getHelper('field');
?>
<section>
    <header>
        <h2><?= $heading; ?></h2>
    </header>
    <form method="post" enctype="multipart/form-data">
        <fieldset>
            <input name="report_id"    type="hidden" value="<?= $id;           ?>" />
            <input name="committee_id" type="hidden" value="<?= $committee_id; ?>" />
            <?php
                echo $helper->field([
                    'name'     => 'title',
                    'id'       => 'title',
                    'label'    => $this->_('title'),
                    'value'    => $title,
                    'required' => true
                ]);

                echo $helper->field([
                    'name'     => 'reportDate',
                    'id'       => 'reportDate',
                    'label'    => $this->_('date'),
                    'type'     => 'date',
                    'value'    => new \DateTime($this->report->getReportDate()),
                    'required' => true
                ]);
            ?>
        </fieldset>
        <fieldset>
            <?php
                $downloadUrl = parent::generateUri('legislationFiles.download')."?legislationFile_id=$id";
                $currentFile = $filename ? "<a href=\"$downloadUrl\">$filename</a>" : '';

                $accept = [];
                foreach (Report::$mime_types as $mime=>$ext) { $accept[] = ".$ext"; }

                $help = new Block('files/partials/uploadHelp.inc');
                echo $helper->field([
                    'id'    => 'reportFile',
                    'name'  => 'reportFile',
                    'label' => $this->_('report'),
                    'type'  => 'file',
                    'value' => $currentFile,
                    'attr'  => ['accept' => implode(',', $accept)],
                    'help'  => $help->render('html', $this->template)
                ]);

                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(parent::generateUri('reports.index')."?committee_id=$committee_id");
            ?>
        </fieldset>
    </form>
</section>
