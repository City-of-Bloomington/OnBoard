<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->files
 * @param Committee      $this->committee (optional)
 */
use Application\Models\MeetingFile;
use Application\Models\Person;
?>
<section>
    <header>
        <h1><?= $this->_(['meetingFile', 'meetingFiles', count($this->files)]); ?></h1>
        <div class="tools">
        <?php
            if ($this->committee && Person::isAllowed('meetingFiles', 'add')) {
                $actions      = [];
                $committee_id = $this->committee->getId();
                $helper       = $this->template->getHelper('dropdown');
                foreach (MeetingFile::$types as $type) {
                    $actions[] = [
                        'label' => $type,
                        'url'   => BASE_URI."/meetingFiles/update?type=$type;committee_id=$committee_id"
                    ];
                }
                echo $helper->dropdown($actions, $this->_('add'), 'meetingFile_add');
            }
        ?>
        </div>
    </header>
    <table>
    <?php
        $userCanEdit   = Person::isAllowed('meetingFiles', 'edit'  );
        $userCanDelete = Person::isAllowed('meetingFiles', 'delete');
        $helper        = $this->template->getHelper('buttonLink');

        $editButton   = '';
        $deleteButton = '';
        foreach ($this->files as $f) {
            $filename = parent::escape($f->getFilename());

            if ($userCanEdit) {
                $editButton = $helper->buttonLink(
                    BASE_URI."/meetingFiles/update?meetingFile_id={$f->getId()}",
                    $this->_('meetingFile_edit'),
                    'edit'
                );
            }
            if ($userCanDelete) {
                $deleteButton = $helper->buttonLink(
                    BASE_URI."/meetingFiles/delete?meetingFile_id={$f->getId()}",
                    $this->_('meetingFile_delete'),
                    'delete'
                );
            }
            echo "
            <tr><td>$editButton</td>
                <td>$filename</td>
                <td>{$f->getMeetingDate(DATE_FORMAT)}</td>
                <td>{$f->getType()}</td>
                <td>$deleteButton</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>
