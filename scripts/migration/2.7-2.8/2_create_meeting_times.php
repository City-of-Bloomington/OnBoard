<?php
/**
 * Populates meeting times in the new datetime field
 *
 * Sets meeting times from Google Events if they exist
 * For meetings without Google Events, we use the standard meeting time for the committee
 *
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
include '../../../src/Web/bootstrap.php';

use Application\Models\GoogleGateway;
use Web\Database;

$db     = Database::getConnection();
$pdo    = $db->getDriver()->getConnection()->getResource();
$log    = fopen('./errors.csv', 'w');

$sql    = 'update meetings set datetime=? where id=?';
$update = $pdo->prepare($sql);

$sql    = "select m.id,
                  m.committee_id,
                  m.meetingDate,
                  m.eventId,
                  c.calendarId
           from meetings   m
           join committees c on c.id=m.committee_id
           where datetime is null
             and eventId is not null";
$result = $pdo->query($sql, \PDO::FETCH_ASSOC);
foreach ($result as $row) {
    echo "$row[id] $row[meetingDate] $row[eventId]\n";

    try {
        $e = GoogleGateway::getEvent($row['calendarId'], $row['eventId']);
        $update->execute([$e->start->dateTime, $row['id']]);
    }
    catch (\Exception $e) { fputcsv($log, $row); }
}


// Regular meeting times for committees
$schedule = [
     //1 => '',  City council meeting times have changed over the years
     3 => '18:00:00',
     5 => '11:00:00',
     9 => '17:00:00',
    10 => '16:00:00',
    13 => '12:00:00',
    14 => '18:00:00',
    16 => '16:00:00',
    22 => '16:00:00',
    25 => '17:30:00',
    27 => '17:30:00',
    28 => '17:00:00',
    29 => '18:00:00',
    31 => '16:30:00',
    32 => '10:00:00',
    33 => '12:00:00',
    35 => '17:00:00',
    36 => '16:30:00',
    40 => '17:30:00',
    46 => '13:30:00',
    47 => '10:00:00',
    49 => '00:00:00', // Sidewalk Committee meets at random times every meeting
    55 => '10:00:00',
    56 => '17:30:00',
    57 => '17:30:00',
    60 => '15:00:00',
    62 => '17:30:00',
    63 => '17:00:00',
    68 => '18:00:00',
    73 => '15:00:00',
];

$sql    = "update meetings set datetime=concat_ws(' ', meetingDate, ?)
           where datetime is null and committee_id=?";
$update = $pdo->prepare($sql);
foreach ($schedule as $committee_id=>$time) {
    echo "$committee_id: $time\n";
    $update->execute([$time, $committee_id]);
}

// City Council Meetings have been different standard times over the years
$sql    = "update meetings set datetime=concat_ws(' ', meetingDate, ?)
           where datetime is null
           and meetingDate>?
           and committee_id=?";
$update = $pdo->prepare($sql);
$update->execute(['18:30:00', '2017-01-01', 1]);
$update->execute(['19:30:00', '1950-01-01', 1]);
