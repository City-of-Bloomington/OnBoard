<?php
/**
 * @copyright 2009-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE
 * @param Committee $this->committee (optional)
 */
use Application\Models\Person;
?>
<nav class="breadcrumbs">
    <ul><li><a href="<?= BASE_URI; ?>"><?= $this->_('home'); ?></a></li>
        <li><a href="<?= parent::generateUri('committees.index'); ?>"><?= $this->_(['committee','committees',2]); ?></a></li>
        <?php
            if (isset($this->committee)) {
                $url  = parent::generateUri('committees.info').'?committee_id='.$this->committee->getId();
                $name = $this->committee->getCode()
                      ? $this->committee->getCode()
                      : $this->committee->getName();
                $name = parent::escape($name);

                echo "<li><a href=\"$url\">$name</a></li>";
            }
        ?>
    </ul>
</nav>
