<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Legislation $this->legislation
 */
use Application\Models\Person;
use Application\Models\Legislation\Legislation;

$legislation_id = $this->legislation->getId();

$committee = parent::escape($this->legislation->getCommittee()->getName());
$type      = parent::escape($this->legislation->getType()     ->getName());
$number    = parent::escape($this->legislation->getNumber());
$title     = parent::escape($this->legislation->getTitle());
$synopsis  = parent::escape($this->legislation->getSynopsis());
?>
<section>
    <header>
        <h1><?= "$type $number"; ?></h1>
        <div class="tools">
        <?php
            $h = $this->template->getHelper('buttonLink');

            if (Person::isAllowed('legislation', 'update')) {
                echo $h->buttonLink(
                    BASE_URI.'/legislation/update?id='.$legislation_id,
                    $this->_('legislation_edit'),
                    'edit'
                );
            }
        ?>
        </div>
        <h2><?= $title; ?></h2>
    </header>
    <p><?= $synopsis; ?></p>
    <?php
        $userCanUploadFiles = Person::isAllowed('legislationFiles', 'update');
        $files = $this->legislation->getFiles();
        if (count($files)) {
            $downloadButton = '';
            $editButton     = '';
            echo '<ul>';
            foreach ($files as $f) {
                if ($userCanUploadFiles) {
                    $editButton = $h->buttonLink(
                        BASE_URI.'/legislationFiles/update?id='.$f->getId(),
                        $this->_('legislationFile_edit'),
                        'edit'
                    );
                }

                $uri = BASE_URI.'/legislationFiles/download?id='.$f->getId();
                $name = parent::escape($f->getFilename());
                $downloadButton = $h->buttonLink($uri, $this->_('download'), 'download');
                echo "<li>$downloadButton$editButton</li>";
            }
            echo '</ul>';
        }
        else {
             if ($userCanUploadFiles) {
                echo $h->buttonLink(
                    BASE_URI.'/legislationFiles/update?legislation_id='.$legislation_id,
                    $this->_('legislationFile_add'),
                    'upload'
                );
            }
        }
    ?>

    <table>
    <?php
        $userCanEditActions = Person::isAllowed('legislationActions', 'update');

        foreach (Legislation::actionTypes() as $t) {
            $name = parent::escape($t->getName());
            $action = $this->legislation->getAction($t);

            if ($action) {
                $outcome    = parent::escape($action->getOutcome());
                $actionDate = $action->getActionDate(DATE_FORMAT);

                if ($userCanEditActions) {
                    $button = $h->buttonLink(
                        BASE_URI.'/legislationActions/update?id='.$action->getId(),
                        $this->_('legislationAction_edit'),
                        'edit'
                    );
                }
            }
            else {
                $outcome    = '';
                $actionDate = '';
                if ($userCanEditActions) {
                    $params = http_build_query([
                        'legislation_id' => $legislation_id,
                        'type_id'        => $t->getId()
                    ]);

                    $button = $h->buttonLink(
                        BASE_URI."/legislationActions/update?$params",
                        $this->_('legislationAction_add'),
                        'add'
                    );
                }
            }

            echo "
            <tr><th>$name</th>
                <td>$outcome $actionDate$button</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>
