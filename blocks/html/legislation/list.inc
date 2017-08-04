<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array       $this->list   An array of Legislation objects
 * @param Legislation $this->parent The parent legislation for the list
 */
use Application\Models\Person;
use Application\Models\Legislation\ActionTypesTable;

use Blossom\Classes\Url;
?>
<section>
    <header>
        <div class="tools">
        <?php
            $userCanEdit = Person::isAllowed('legislation', 'update');
            $h = $this->template->getHelper('buttonLink');
            if ($userCanEdit && !$this->parent) {
                $params = [];
                if ($this->committee) { $params['committee_id'] = $this->committee->getId(); }
                $params['return_url'] = Url::current_url(BASE_HOST);

                echo $h->buttonLink(
                    BASE_URI.'/legislation/update?'.http_build_query($params),
                    $this->_('legislation_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
    <div>
    <?php
        foreach ($this->list as $l) {
            $type       = parent::escape($l->getType()->getName());
            $number     = parent::escape($l->getNumber  ());
            $title      = parent::escape($l->getTitle   ());
            $amendsCode = $l->amendsCode() ? $this->_('yes') : $this->_('no');

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
                    <h2><a href=\"$uri\">$type $number $status $amendsCode $downloadButton</a></h2>
                    <h3>$title</h3>
                </header>
            </article>
            ";
        }
    ?>
    </div>
</section>
