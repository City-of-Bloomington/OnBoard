<?php
/**
 * Displays one year of documents from a CMIS query
 *
 * $documents The raw results from the CMIS query
 * $years     The available years of documents that we have in Alfresco
 * $types     The list of CMIS types of documents that are being listed
 * $year      The year for the documents being listed
 * $node      The node object for the board or commission page
 *
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
global $base_url;
$dates = [];

foreach ($documents as $row) {
    $d = &$row->succinctProperties;
    // CMIS returns timestamps in milliseconds.  However,
    // PHP only reads timestamps in seconds.
    $date = date('Y-m-d', $d->{'cob:meetingDate'}/1000);
    $dates[$date][$d->{'cmis:objectTypeId'}][] = [
        'id'       => $d->{'cmis:versionSeriesId'},
        'filename' => $d->{'cmis:name'},
        'mimeType' => $d->{'cmis:contentStreamMimeType'}
    ];
}
echo "
<div class=\"civiclegislation-container\">
    <h3>$year Meetings</h3>
    <nav id=\"years\">
";
    foreach (array_reverse(array_keys(get_object_vars($years))) as $y) {
        $y = substr($y, 0, 4);
        echo l($y, "node/{$node->nid}/meetings/$y", ['class'=>['current']]);
    }
echo "
    </nav>
    <div class=\"civiclegislation-listing\">
        <table id=\"documents\">
            <thead>
                <tr><th>Date</th>
";
        foreach ($types as $type) {
            echo "<th>$type</th>";
        }
echo "
                </tr>
            </thead>
            <tbody>
";
    foreach ($dates as $date=>$docs) {
        echo "<tr><td>$date</td>";
        foreach ($types as $type) {
            echo "<td>";
            if (!empty($docs[$type])) {
                echo "<ul>";
                foreach ($docs[$type] as $d) {
                    $url = "{$base_url}/{$node->nid}/meetings/download/$d[id]";
                    echo "<li><a href=\"$url\" class=\"$d[mimeType]\">$d[filename]</a></li>";
                }
                echo "</ul>";
            }
            echo "</td>";
        }
        echo "</tr>";
    }
echo "
            </tbody>
        </table>
    </div>
</div>
";
