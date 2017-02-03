<?php
/**
 * @copyright 2009-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\ResultSet $this->data
 */
namespace Application\Models;

use Application\Templates\Helpers\ButtonLink;
use Blossom\Classes\Url;

$userCanEdit = Person::isAllowed('committees', 'edit');
?>
<section>
    <header>
        <h1><?= $this->_(['committee', 'committees', count($this->data)]); ?></h1>
        <div class="tools">
        <?php
            $helper = $this->template->getHelper('buttonLink');
            if ($userCanEdit) {
                echo $helper->buttonLink(
                    BASE_URI.'/committees/update',
                    $this->_('committee_add'),
                    'add'
                );
            }
        ?>
        </div>
    </header>
    <table>
        <thead>
            <tr><th></th>
                <th><?= $this->_(['seat', 'seats', 2]); ?></th>
                <th><?= $this->_(['vacancy', 'vacancies', 2]); ?></th>
                <?php
                    $userCanViewApplications = Person::isAllowed('committees', 'applications');
                    if ($userCanViewApplications) {
                        echo "<th>{$this->_(['application', 'applications', 2])}</th>";
                    }
                ?>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach ($this->data as $c) {
                $name = self::escape($c['name']);
                $url = BASE_URI.'/committees/info?committee_id='.$c['id'];

                if ($c['type'] === 'seated') {
                    $seatCount = (int)$c['seats'];
                    if ($seatCount === 0) { $seatCount = ''; }
                }
                else { $seatCount = $this->_('open'); }

                $vacancies = (int)$c['vacancies'];
                if ($vacancies === 0) { $vacancies = ''; }

                $apps = (int)$c['applications'];
                if ($apps === 0) { $apps = ''; }
                $applications = $userCanViewApplications
                    ? "<td><a href=\"".BASE_URI."/committees/applications?committee_id=$c[id]\">$apps</a></td>"
                    : '';

                echo "
                <tr><td><a href=\"$url\">$name</a></td>
                    <td>$seatCount</td>
                    <td>$vacancies</td>
                    $applications
                </tr>
                ";
            }
        ?>
        </tbody>
    </table>
    <?php
        $url = new Url(Url::current_url());
        $url->current = '0';
        echo $helper->buttonLink($url, $this->_('committees_past'));
    ?>
</section>
