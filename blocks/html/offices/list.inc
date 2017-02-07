<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->offices
 */
?>
<section>
    <header>
        <h1><?= $this->_(['office', 'offices', count($this->offices)]); ?></h1>
    </header>
    <table>
    <?php
        foreach ($this->offices as $o) {
            $title = self::escape($o->getTitle());
            $name  = self::escape($o->getPerson()->getFullname());
            echo "
            <tr><td>$title</td>
                <td>$name</td>
                <td>{$o->getStartDate(DATE_FORMAT)} - {$o->getEndDate(DATE_FORMAT)}</td>
            </tr>
            ";
        }
    ?>
    <table>
</section>
