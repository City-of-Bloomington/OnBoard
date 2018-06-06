<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
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
        if ($userCanUploadFiles) {
            $editButton = $helper->buttonLink(
                BASE_URI.'/legislationFiles/update?id='.$f->getId(),
                $this->_('legislationFile_edit'),
                'edit'
            );
        }

        if ($userCanDeleteFiles) {
            $deleteButton = $helper->buttonLink(
                BASE_URI.'/legislationFiles/delete?id='.$f->getId(),
                $this->_('legislationFile_delete'),
                'delete'
            );
        }

        $uri = BASE_URI.'/legislationFiles/download?id='.$f->getId();
        $name = parent::escape($f->getFilename());
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
            BASE_URI.'/legislationFiles/update?legislation_id='.$this->legislation->getId(),
            $this->_('legislationFile_add'),
            'upload'
        );
    }
}
