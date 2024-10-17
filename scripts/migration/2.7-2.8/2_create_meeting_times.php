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

$sql    = "update meetings
           set start=:start,end=:end,created=:created,updated=:updated,location=:location,htmlLink=:htmlLink
           where id=:id";
$update = $pdo->prepare($sql);

$sql    = "select m.id,
                  m.committee_id,
                  m.meetingDate,
                  m.eventId,
                  c.calendarId
           from meetings   m
           join committees c on c.id=m.committee_id
           where start is null
             and eventId is not null";
$result = $pdo->query($sql, \PDO::FETCH_ASSOC);
foreach ($result as $row) {
    echo "$row[id] $row[meetingDate] $row[eventId]\n";

    try {
        $event = GoogleGateway::getEvent($row['calendarId'], $row['eventId']);
        if (!$event->start->dateTime || !$event->end->dateTime) {
            throw new \Exception('missingDateTime');
        }
    }
    catch (\Exception $e) {
        fputcsv($log, $row);
        continue;
    }

    $start    = new \DateTime($event->start->dateTime);
    $end      = new \DateTime($event->end  ->dateTime);
    $created  = new \DateTime($event->created);
    $updated  = new \DateTime($event->updated);
    $location = !empty($event->location) ? substr($event->location, 0, 255) : null;
    try {
        $d = [
            'id'       => $row['id'],
            'start'    => $start  ->format('Y-m-d H:i:s'),
            'end'      => $end    ->format('Y-m-d H:i:s'),
            'created'  => $created->format('Y-m-d H:i:s'),
            'updated'  => $updated->format('Y-m-d H:i:s'),
            'location' => $location,
            'htmlLink' => $event->htmlLink
        ];
        $update->execute($d);
    }
    catch (\Exception $e) {
        print_r($e);
        print_r($event);
        exit();
    }
}


// Regular meeting times for committees
$schedule = [
     //1 => '',  City council meeting times have changed over the years
     3 => ['start'=>'18:00:00', 'end'=>'19:30:00'],
     5 => ['start'=>'11:00:00', 'end'=>'12:00:00'],
     9 => ['start'=>'17:00:00', 'end'=>'19:00:00'],
    10 => ['start'=>'16:00:00', 'end'=>'17:30:00'],
    13 => ['start'=>'12:00:00', 'end'=>'13:00:00'],
    14 => ['start'=>'18:00:00', 'end'=>'20:00:00'],
    16 => ['start'=>'16:00:00', 'end'=>'17:00:00'],
    22 => ['start'=>'16:00:00', 'end'=>'17:30:00'],
    25 => ['start'=>'17:30:00', 'end'=>'19:30:00'],
    27 => ['start'=>'17:30:00', 'end'=>'19:00:00'],
    28 => ['start'=>'17:00:00', 'end'=>'19:00:00'],
    29 => ['start'=>'18:00:00', 'end'=>'19:30:00'],
    31 => ['start'=>'16:30:00', 'end'=>'18:00:00'],
    32 => ['start'=>'10:00:00', 'end'=>'11:00:00'],
    33 => ['start'=>'12:00:00', 'end'=>'13:00:00'],
    35 => ['start'=>'17:00:00', 'end'=>'19:00:00'],
    36 => ['start'=>'16:30:00', 'end'=>'17:30:00'],
    40 => ['start'=>'17:30:00', 'end'=>'19:00:00'],
    45 => ['start'=>'18:30:00', 'end'=>'20:00:00'],
    46 => ['start'=>'13:30:00', 'end'=>'15:00:00'],
    47 => ['start'=>'10:00:00', 'end'=>'11:30:00'],
    49 => ['start'=>'00:00:00', 'end'=>'01:00:00'], // Sidewalk Committee meets at random times every meeting
    55 => ['start'=>'10:00:00', 'end'=>'11:00:00'],
    56 => ['start'=>'17:30:00', 'end'=>'19:00:00'],
    57 => ['start'=>'17:30:00', 'end'=>'19:30:00'],
    60 => ['start'=>'15:00:00', 'end'=>'16:00:00'],
    62 => ['start'=>'17:30:00', 'end'=>'18:30:00'],
    63 => ['start'=>'17:00:00', 'end'=>'18:00:00'],
    68 => ['start'=>'18:00:00', 'end'=>'19:00:00'],
    73 => ['start'=>'15:00:00', 'end'=>'17:30:00']
];

$sql    = "update meetings
           set start=concat_ws(' ', meetingDate, ?),
                 end=concat_ws(' ', meetingDate, ?)
           where start is null and committee_id=?";
$update = $pdo->prepare($sql);
foreach ($schedule as $committee_id=>$s) {
    echo "$committee_id: $s[start]-$s[end]\n";
    $update->execute([$s['start'], $s['end'], $committee_id]);
}

// City Council Meetings have been different standard times over the years
$sql    = "update meetings
           set start=concat_ws(' ', meetingDate, ?),
                 end=concat_ws(' ', meetingDate, ?)
           where start is null
             and meetingDate>?
             and committee_id=?";
$update = $pdo->prepare($sql);
$update->execute(['18:30:00', '21:30:00', '2017-01-01', 1]);
$update->execute(['19:30:00', '22:30:00', '1950-01-01', 1]);
