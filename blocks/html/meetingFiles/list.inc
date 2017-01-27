<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->files
 * @param Committee      $this->committee (optional)
 */
use Application\Models\Person;
?>
<section>
    <header>
        <h1><?= $this->_(['meetingFile', 'meetingFiles', count($this->files)]); ?></h1>
        <div class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');

            if (Person::isAllowed('meetingFiles', 'add')) {
                $uri = BASE_URI.'/meetingFiles/update';
                if ($this->committee) { $uri.='?committee_id='.$this->committee->getId(); }

                echo $helper->buttonLink($uri, $this->_('meetingFile_add'), 'add');
            }

        ?>
        </div>
    </header>
    <table>
    <?php
        $userCanEdit   = Person::isAllowed('meetingFiles', 'edit'  );
        $userCanDelete = Person::isAllowed('meetingFiles', 'delete');

        $editButton   = '';
        $deleteButton = '';
        foreach ($this->files as $f) {
            $committee = parent::escape($f->getCommittee()->getName());

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
                <td>$committee</td>
                <td>{$f->getMeetingDate(DATE_FORMAT)}</td>
                <td>{$f->getType()}</td>
                <td>$deleteButton</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>
