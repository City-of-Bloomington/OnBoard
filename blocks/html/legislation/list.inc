<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->legislation An array of Legislation objects
 */
use Application\Models\Person;
use Application\Models\Legislation\ActionTypesTable;

use Blossom\Classes\Url;
?>
<section>
    <header>
        <h1><?= $this->_(['legislation', 'legislations', count($this->legislation)]); ?></h1>
        <div class="tools">
        <?php
            $userCanEdit = Person::isAllowed('legislation', 'update');
            $h = $this->template->getHelper('buttonLink');
            if ($userCanEdit) {
                echo $h->buttonLink(
                    BASE_URI.'/legislation/update?return_url='.Url::current_url(BASE_HOST),
                    $this->_('legislation_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
    <div>
    <?php
        foreach ($this->legislation as $l) {
            $type      = parent::escape($l->getType()->getName());
            $number    = parent::escape($l->getNumber  ());
            $title     = parent::escape($l->getTitle   ());
            $synopsis  = parent::escape($l->getSynopsis());

            $uri = BASE_URI.'/legislation/view?id='.$l->getId();

            $actions = $l->getActions();
            $action  = end($actions);
            $status  = '';
            if ($action) {
                $t       = $action->getType()->getName();
                $outcome = $action->getOutcome();
                $date    = $action->getActionDate(DATE_FORMAT);
                $status  = parent::escape("($t $outcome $date)");
            }

            $downloadButton = '';
            $files = $l->getFiles();
            if (count($files)) {
                foreach ($files as $f) {
                    $u    = BASE_URI.'/legislationFiles/download?id='.$f->getId();
                    $name = parent::escape($f->getFilename());
                    $downloadButton.= $h->buttonLink($u, $this->_('download'), 'download');
                }
            }
            echo "
            <article>
                <header>
                    <h2><a href=\"$uri\">$type $number $status $downloadButton</a></h2>
                    <h3>$title</h3>
                </header>
                <p>$synopsis</p>
            </article>
            ";
        }
    ?>
    </div>
</section>
