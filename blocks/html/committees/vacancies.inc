<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->data
 */
?>
<section     class="fn1-uiBlock">
    <header  class="fn1-uiBlock-header"><h1><?= $this->_(['vacancy', 'vacancies', 2]); ?></h1>
        <div class="fn1-uiBlock-headerTools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            echo $helper->buttonLink(BASE_URI.'/committees/vacancies?format=csv', 'CSV', 'download');
        ?>
        </div>
    </header>
    <table class="fn1-table">
        <thead>
            <tr><th>Committee</th>
                <th>Seat</th>
                <th>Appointer</th>
                <th>Term Dates</th>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach ($this->data as $row) {
                foreach (array_keys($row) as $f) {
                    $$f = self::escape($row[$f]);
                }
                echo "
                <tr><td>$committee</td>
                    <td>$seat</td>
                    <td>$appointer</td>
                    <td>$termStart $termEnd</td>
                </tr>
                ";
            }
        ?>
        </tbody>
    </table>
</section>