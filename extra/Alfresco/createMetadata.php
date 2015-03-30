<?php
/**
 * @copyright 2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$type = 'cob:agenda';
$time = 'T17:00:00.000+05:00';

foreach (file('./files.csv') as $line) {
    list($filename, $date) = explode('|', $line);
    $filename = trim($filename);
    $date     = trim($date);

    if (file_exists($filename)) {
        $xml = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE properties SYSTEM "http://java.sun.com/dtd/properties.dtd">
<properties>
    <entry key="type">$type</entry>
    <entry key="cob:meetingDate">{$date}{$time}</entry>
</properties>
EOT;
        file_put_contents("$filename.metadata.properties.xml", $xml);
    }
}
