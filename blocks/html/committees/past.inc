<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Laminas\Db\ResultSet $this->data
 */
use Web\Url;
?>
<section>
    <header>
        <h1><?= $this->_('committees_past'); ?></h1>
        <div class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            $url = new Url(Url::current_url(BASE_HOST));
            unset($url->current);
            echo $helper->buttonLink($url, $this->_('committees_current'));
        ?>
        </div>
    </header>

    <table>
    <?php
        foreach ($this->data as $c) {
            $uri     = BASE_URI.'/committees/info?committee_id='.$c['id'];
            $name    = self::escape($c['name']);
            $endDate = new \DateTime($c['endDate']);

            echo "
            <tr><td><a href=\"$uri\">$name</a></td>
                <td>{$endDate->format(DATE_FORMAT)}</td>
            </tr>
            ";
        }
    ?>
    </table>
</section>
