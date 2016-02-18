<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Media;
?>
<p><?= sprintf($this->_('media/unknownFileType', 'errors'), implode(', ', array_keys(Media::$extensions))); ?></p>