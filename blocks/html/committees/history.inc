<?php
/**
 * @copyright 2017-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->history An array of CommitteeHistory objects
 */
use Blossom\Classes\Block;

$this->template->addToAsset('scripts', BASE_URI.'/js/changeLog.js');
?>
<section>
    <header><h2><?= $this->_('committeeHistory'); ?></h2></header>
    <?php
        foreach ($this->history as $i=>$h) {
            $person  = parent::escape($h->getPerson()->getFullname());
            $action  = parent::escape($h->getAction());
            $table   = parent::escape($h->getTablename());

            echo "
            <article class=\"changeLogEntry\">
                <header>
                    <h3>{$h->getDate(DATE_FORMAT)}: $person $table:$action</h3>
                    <div class=\"tools\">
                        <button        id=\"changeLogEntry-control-$i\"
                            aria-controls=\"changeLogEntry-content-$i\" aria-expanded=\"true\">
                            {$this->_('expand')}
                        </button>
                    </div>
                </header>
                <div id=\"changeLogEntry-content-$i\" aria-hidden=\"false\">
            ";
                foreach ($h->getChanges() as $c) {
                    $block = new Block("$table/changeLogEntry.inc", ['change'=>$c]);
                    echo $block->render('html', $this->template);
                }
            echo "
                </div>
            </article>
            ";
        }
    ?>
</section>
