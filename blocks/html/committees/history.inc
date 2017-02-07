<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->history An array of CommitteeHistory objects
 */
?>
<section>
    <header><h1><?= $this->_('committeeHistory'); ?></h1></header>

    <table>
    <?php
        foreach ($this->history as $h) {
            $person = parent::escape($h->getPerson()->getFullname());

            echo "
            <tr><td>{$h->getDate(DATE_FORMAT)}</td>
                <td>$person</td>
                <td>{$h->getAction()}</td>
                <td>{$h->getTablename()}</td>
            </tr>
            ";
        }
    ?>
    </table>

</section>
