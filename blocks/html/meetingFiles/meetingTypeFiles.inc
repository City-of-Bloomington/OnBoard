<?php
/**
 * @copyright 2017-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param string    $this->type
 * @param Committee $this->committee
 * @param string    $this->eventId
 * @param DateTime  $this->date
 * @param array     $this->files      An array of MeetingFile objects
 */
use Application\Models\Person;
?>
<div>
    <h3><?= $this->type; ?></h3>
    <?php
        if ($this->files) {
            echo '<ul>';
            foreach ($this->files as $file) {
                $name = $file->getTitle()
                      ? parent::escape($file->getTitle())
                      : $file->getFilename();
                echo "<li><a href=\"{$file->getDownloadUri()}\">$name</a></li>";
            }
            echo '</ul>';
        }

        if (Person::isAllowed('meetingFiles', 'update')) {
            $this->_include('meetingFiles/addForm.inc');
        }
    ?>
</div>
