<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Legislation $this->legislation
 */
use Application\Models\Person;

$helper = $this->template->getHelper('buttonLink');

$userCanUploadFiles = Person::isAllowed('legislationFiles', 'update');
$userCanDeleteFiles = Person::isAllowed('legislationFiles', 'delete');

$files = $this->legislation->getFiles();
if (count($files)) {
    $downloadButton = '';
    $editButton     = '';
    $deleteButton   = '';

    echo '<ul>';
    foreach ($files as $f) {
        $file_id = $f->getId();
        if ($userCanUploadFiles) {
            $editButton = $helper->buttonLink(
                parent::generateUri('legislationFiles.update')."?id=$file_id",
                $this->_('legislationFile_edit'),
                'edit'
            );
        }

        if ($userCanDeleteFiles) {
            $deleteButton = $helper->buttonLink(
                parent::generateUri('legislationFiles.delete')."?id=$file_id",
                $this->_('legislationFile_delete'),
                'delete'
            );
        }

        $uri            = parent::generateUri('legislationFiles.download')."?id=$file_id";
        $name           = parent::escape($f->getFilename());
        $downloadButton = $helper->buttonLink($uri, $this->_('download'), 'download');
        echo "
        <li>$downloadButton
            $editButton
            $deleteButton
        </li>
        ";
    }
    echo '</ul>';
}
else {
    if ($userCanUploadFiles) {
        echo $helper->buttonLink(
            parent::generateUri('legislationFiles.update').'?legislation_id='.$this->legislation->getId(),
            $this->_('legislationFile_add'),
            'upload'
        );
    }
}
