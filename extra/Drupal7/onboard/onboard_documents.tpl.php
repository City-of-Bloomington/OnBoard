<?php
/**
 * Displays one year of documents from a CMIS query
 *
 * $documents The raw results from the CMIS query
 * $years     The available years of documents that we have in Alfresco
 * $types     The list of CMIS types of documents that are being listed
 * $year      The year for the documents being listed
 * $node      The node object for the board or commission page
 * $namespace The short namespace that is prepended to the types
 *
 * @copyright 2015-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$dates = [];
foreach ($documents as $row) {
    $d = &$row->succinctProperties;
    // CMIS returns timestamps in milliseconds.  However,
    // PHP only reads timestamps in seconds.
    $date = date('Y-m-d', $d->{'cob:meetingDate'}/1000);
    $dates[$date][$d->{'cmis:objectTypeId'}][] = [
        'id'    => $d->{'cmis:versionSeriesId'},
        'title' => $d->{'cmis:name'},
        'type'  => $d->{'cmis:contentStreamMimeType'}
    ];
}
echo "
<section class=\"onboard container\">
    <h1>$year Meetings</h1>
    <nav>
";
    foreach (array_reverse(array_keys(get_object_vars($years))) as $y) {
        $y = substr($y, 0, 4);
        $attr = $y == $year ? ['attributes'=>['class'=>['current']]] : [];
        echo l($y, "onboard/{$node->nid}/field_onboard_links/meetings/$y", $attr);
    }
echo "
    </nav>
    <div class=\"listing\">
        <dl>
";
    foreach ($dates as $date=>$docs) {
        $dateObj = new DateTime($date);
        echo "
            <dt><time datetime=\"{$dateObj->format('Y-m-d')}\">
                <span class=\"month\">{$dateObj->format('F')}</span>
                <span class=\"dayOfMonth\">{$dateObj->format('j')}</span>
            </dt>
        ";
        foreach ($types as $type) {
            // Strip the namespace off the front of the type
            $class    = substr($type, strlen($namespace));
            $typeName = ucfirst($class);

            echo "
            <dd class=\"$class\">
                <dl><dt>$typeName</dt>
            ";
            if (!empty  ($docs[$type])) {
                foreach ($docs[$type] as $d) {
                    $a = theme('cmisro_item', ['object'=>$d]);
                    echo "<dd>$a</dd>";
                }
            }
            else {
                echo '<dd>None</dd>';
            }
            echo "
                </dl>
            </dd>
            ";
        }
    }
echo "
        </dl>
    </div>
</section>
";