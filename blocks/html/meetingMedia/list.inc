<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->media
 * @param Committee      $this->committee (optional)
 */
use Application\Models\Person;
?>
<section>
    <header>
        <h1><?= $this->_(['meetingMedia', 'meetingMedias', count($this->media)]); ?></h1>
        <div class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');

            if (Person::isAllowed('meetingMedia', 'add')) {
                $uri = BASE_URI.'/meetingMedia/update';
                if ($this->committee) { $uri.='?committee_id='.$this->committee->getId(); }

                echo $helper->buttonLink($uri, $this->_('meetingMedia_add'), 'add');
            }

        ?>
        </div>
    </header>
    <table>
    <?php
        $userCanEdit   = Person::isAllowed('meetingMedia', 'edit'  );
        $userCanDelete = Person::isAllowed('meetingMedia', 'delete');

        $editButton   = '';
        $deleteButton = '';
        foreach ($this->media as $m) {
            $committee = parent::escape($m->getCommittee()->getName());

            if ($userCanEdit) {
                $editButton = $helper->buttonLink(
                    BASE_URI."/meetingMedia/update?meetingMedia_id={$m->getId()}",
                    $this->_('meetingMedia_edit'),
                    'edit'
                );
            }
            if ($userCanDelete) {
                $deleteButton = $helper->buttonLink(
                    BASE_URI."/meetingMedia/delete?meetingMedia_id={$m->getId()}",
                    $this->_('meetingMedia_delete'),
                    'delete'
                );
            }
            echo "
            <tr><td>$editButton</td>
                <td>$committee</td>
                <td>{$m->getMeetingDate(DATE_FORMAT)}</td>
                <td>{$m->getType()}</td>
                <td>$deleteButton</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>
