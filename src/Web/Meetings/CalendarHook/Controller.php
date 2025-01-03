<?php
/**
 * Handle push notifications for Google Calendar Events
 *
 * Google will POST to this route.  All the information is in the HTTP Header.
 *
 * [HTTP_X_GOOG_CHANNEL_ID]         => onboard-inghamn
 * [HTTP_X_GOOG_CHANNEL_EXPIRATION] => Thu, 02 Jan 2025 19:03:25 GMT
 * [HTTP_X_GOOG_RESOURCE_STATE]     => sync
 * [HTTP_X_GOOG_MESSAGE_NUMBER]     => 1
 * [HTTP_X_GOOG_RESOURCE_ID]        => zDkcEeu4J4fYNUkBtRqbAQ9aA6Y
 * [HTTP_X_GOOG_RESOURCE_URI]       => https://www.googleapis.com/calendar/v3/calendars/inghamn%40bloomington.in.gov/events?alt=json
 *
 * @copyright 2024 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @see https://developers.google.com/calendar/api/guides/push
 */
declare (strict_types=1);
namespace Web\Meetings\CalendarHook;

use Application\Models\CommitteeTable;
use Application\Models\GoogleGateway;
use Application\Models\Meeting;
use Application\Models\MeetingTable;

class Controller
{
    const DEBUG = SITE_HOME.'/debug.log';

    public function __invoke(array $params): \Web\View
    {
        $committees  = new CommitteeTable();
        $meetings    = new MeetingTable();
        $debug       = fopen(SITE_HOME.'/debug.log', 'a');

        #error_log(print_r($_SERVER, true));

        $event_id    = $_SERVER['HTTP_X_GOOG_RESOURCE_ID'];
        $calendar_id = self::parseCalendarId($_SERVER['HTTP_X_GOOG_RESOURCE_URI']);
        $event       = GoogleGateway::getEvent($calendar_id, $event_id);
        fwrite($debug, print_r($event, true)."\n");

        $list        = $committees->find(['calendarId'=>$calendar_id]);
        if ($list->count()) {
            $committee = $list->current();
            fwrite($debug, "Committee: ".$committee->getName()."\n");
            $committee->syncGoogleCalendar();
        }
        else {
            fwrite($debug, 'No Committee Found: '.$calendar_id."\n");
        }

        return new View();
    }

    private static function parseCalendarId(string $resource_uri): string
    {
        $matches = [];
        if (preg_match('|calendars/(.+)/|', $resource_uri, $matches)) {
            return urldecode($matches[1]);
        }
        return '';
    }
}


