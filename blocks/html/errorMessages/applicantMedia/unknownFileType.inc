<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\ApplicantMedia;
?>
<p><?= sprintf($this->_('media/unknownFileType', 'errors'), implode(', ', ApplicantMedia::$mime_types)); ?></p>
