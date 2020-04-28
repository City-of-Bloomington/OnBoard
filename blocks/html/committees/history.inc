<?php
/**
 * @copyright 2017-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param array $this->history An array of CommitteeHistory objects
 */
use Blossom\Classes\Block;

?>
<section>
    <header><h2><?= $this->_('committeeHistory'); ?></h2></header>
    <?php
        foreach ($this->history as $i=>$h) {
            $person  = parent::escape($h->getPerson()->getFullname());
            $action  = parent::escape($h->getAction());
            $table   = parent::escape($h->getTablename());

            echo "
            <details>
                <summary>
                    <h3>{$h->getDate(DATE_FORMAT)}: $person $table:$action</h3>
                </summary>
            ";
                foreach ($h->getChanges() as $c) {
                    $block = new Block("$table/changeLogEntry.inc", ['change'=>$c]);
                    echo $block->render('html', $this->template);
                }
            echo "
            </details>
            ";
        }
    ?>
</section>
