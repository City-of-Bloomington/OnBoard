<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\ApplicantFile;
?>
<p><?= sprintf($this->_('files/unknownFileType', 'errors'), implode(', ', ApplicantFile::$mime_types)); ?></p>
