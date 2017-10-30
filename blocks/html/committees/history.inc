<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->history An array of CommitteeHistory objects
 */
use Blossom\Classes\Block;
?>
<section>
    <header><h1><?= $this->_('committeeHistory'); ?></h1></header>
    <?php
        foreach ($this->history as $h) {
            $person  = parent::escape($h->getPerson()->getFullname());
            $action  = parent::escape($h->getAction());
            $table   = parent::escape($h->getTablename());

            echo "
            <article>
                <header>
                    <h2>{$h->getDate(DATE_FORMAT)}: $person $table:$action</h2>
                </header>
                <div>
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
