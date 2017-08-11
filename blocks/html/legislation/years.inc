<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array     $this->years
 * @param Committee $this->committee (optional)
 */
echo '<table>';

$params = $this->committee
        ? ['committee_id'=>$this->committee->getId()]
        : [];

foreach ($this->years as $year=>$count) {
    $params['year'] = $year;
    $uri = BASE_URI.'/legislation?'.http_build_query($params);
    echo "
    <tr><th><a href=\"$uri\">$year</a></th>
        <td>$count</td>
    </tr>";
}
echo '</table>';
