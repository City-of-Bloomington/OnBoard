<?php
/**
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;
use Web\Database;

include '../../src/Web/bootstrap.php';
$db     = Database::getConnection();
$pdo    = $db->getDriver()->getConnection()->getResource();

$sql = "insert meetings
        set committee_id=:committee_id,start=:start,end=:end,created=:created,updated=:updated,location=:location,htmlLink=:htmlLink";
$insertMeeting = $pdo->prepare($sql);

$sql = "update meetings
        set start=:start,end=:end,created=:created,updated=:updated,location=:location,htmlLink=:htmlLink
        where id=:id";
$updateMeeting  = $pdo->prepare($sql);
$updateToken    = $pdo->prepare('update committees set syncToken=? where id=?');
$selectMeetings = $pdo->prepare('select * from meetings where eventId=?');

$sql = "select id, calendarId, syncToken
        from committees
        where (endDate is null or endDate>now())";
$committees = $pdo->query($sql);
foreach ($committees->fetchAll(\PDO::FETCH_ASSOC) as $committee) {
    $events        = GoogleGateway::sync($committee['calendarId'], $committee['syncToken']);
    $nextSyncToken = $events->nextSyncToken;
    echo "syncToken: $nextSyncToken\n";

    try {
        foreach ($events as $event) {
            // Calendar events without start and end times are either
            // all day events, or they have been cancelled.  In either
            // case, we should not change the information in the database.
            if (!$event->start->dateTime || !$event->end->dateTime) {
                continue;
            }

            $eventId  = $event->id;
            $start    = new \DateTime($event->start->dateTime);
            $end      = new \DateTime($event->end  ->dateTime);
            $created  = new \DateTime($event->created);
            $updated  = new \DateTime($event->updated);
            $location = !empty($event->location) ? substr($event->location, 0, 255) : null;
            echo "Event: $eventId\n";

            $selectMeetings->execute([$event->id]);
            $result = $selectMeetings->fetchAll(\PDO::FETCH_ASSOC);
            $count  = count($result);
            if (!count($result)) {
                $insertMeeting->execute([
                    'committee_id' => $committee['id'],
                    'start'        => $start  ->format('Y-m-d H:i:s'),
                    'end'          => $end    ->format('Y-m-d H:i:s'),
                    'created'      => $created->format('Y-m-d H:i:s'),
                    'updated'      => $updated->format('Y-m-d H:i:s'),
                    'location'     => $location,
                    'htmlLink'     => $event->htmlLink
                ]);
            }
            else {
                foreach ($result as $row) {
                    echo "Updating meeting\n";
                    $updateMeeting->execute([
                        'id'       => $row['id'],
                        'start'    => $start  ->format('Y-m-d H:i:s'),
                        'end'      => $end    ->format('Y-m-d H:i:s'),
                        'created'  => $created->format('Y-m-d H:i:s'),
                        'updated'  => $updated->format('Y-m-d H:i:s'),
                        'location' => $location,
                        'htmlLink' => $event->htmlLink
                    ]);
                }
            }
        }

        echo "Updating committee token: $nextSyncToken $committee[id]\n";
        $updateToken->execute([$nextSyncToken, $committee['id']]);
    }
    catch (\Exception $e) {
        print_r($e);
        print_r($event);
        exit();
    }
}
