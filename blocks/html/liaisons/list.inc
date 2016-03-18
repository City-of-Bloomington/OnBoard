<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Zend\Db\Result $this->data
 */
use Application\Models\Committee;
use Blossom\Classes\Url;

$title = $this->_(['liaison', 'liaisons', count($this->data)]);

$helper = $this->template->getHelper('buttonLink');
$uri = new Url(Url::current_url());
$uri->format = 'csv';
$csvButton = $helper->buttonLink($uri, 'csv', 'download');
echo "
<section>
    <header>
        <h1>$title</h1>
        <div class=\"tools\">$csvButton</div>
    </header>
    <table class=\"fn1-table\">
";
    foreach ($this->data['results'] as $row) {
        $committeeUri  = BASE_URI.'/committees/info?committee_id='.$row['committee_id'];
        $uri = BASE_URI.'/people/view?person_id='.$row['person_id'];

        foreach ($row as $f=>$v) {
            $$f = self::escape($v);
        }

        echo "
        <tr><td><a href=\"$committeeUri\">$committee</a></td>
            <td>$department</td>
            <td>$type</td>
            <td><a href=\"$uri\">$firstname $lastname</a></td>
            <td>$email</td>
            <td>$phone</td>
        </tr>
        ";
    }
echo "
    </table>
</section>
";
