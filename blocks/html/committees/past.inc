<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\ResultSet $this->data
 */
use Blossom\Classes\Url;
?>
<section>
    <header>
        <h1><?= $this->_('committees_past'); ?></h1>
        <div class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            $url = new Url(Url::current_url());
            unset($url->current);
            echo $helper->buttonLink($url, $this->_('committees_current'));
        ?>
        </div>
    </header>

    <table class="fn1-table">
    <?php
        foreach ($this->data as $c) {
            $uri     = BASE_URI.'/committees/info?committee_id='.$c['id'];
            $name    = self::escape($c['name']);
            $endDate = $c['endDate'];

            echo "
            <tr><td><a href=\"$uri\">$name</a></td>
                <td>$endDate</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>
