<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->committees
 */
?>
<section>
    <h1><?= $this->_('committees_past'); ?></h1>
    <table class="fn1-table">
    <?php
        foreach ($this->committees as $c) {
            $uri     = BASE_URI.'/committees/info?committee_id='.$c->getId();
            $name    = self::escape($c->getName());
            $endDate = $c->getEndDate(DATE_FORMAT);

            echo "
            <tr><td><a href=\"$uri\">$name</a></td>
                <td>$endDate</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>
