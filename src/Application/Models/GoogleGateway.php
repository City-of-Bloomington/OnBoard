<?php
/**
 * @copyright 2017-2024 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Channel;
use Google\Service\Calendar\Events;

class GoogleGateway
{
    private static function getClient()
    {
        static $client = null;

        if (!$client) {
            $client = new Client();
            $client->setAuthConfig(GOOGLE_CREDENTIALS_FILE);
            $client->setScopes([Calendar::CALENDAR]);
            $client->setSubject(GOOGLE_USER_EMAIL);
        }
        return $client;
    }

    /**
     * @see https://developers.google.com/google-apps/calendar/v3/reference/events/list
     * @param  string   $calendarId
     * @param  DateTime $start
     * @param  DateTime $end
     * @param  boolean  $singleEvents
     * @param  int      $maxResults
     * @return EventList
     */
    public static function events($calendarId, \DateTime $start=null, \DateTime $end=null, $singleEvents=true, $maxResults=null)
    {
        $FIELDS = 'description,end,endTimeUnspecified,htmlLink,id,location,'
                . 'originalStartTime,recurrence,recurringEventId,sequence,'
                . 'start,summary,attendees,organizer';

        $opts = [
            'fields'       => "items($FIELDS)",
            'singleEvents' => $singleEvents,
            'maxResults'   => $maxResults
        ];
        if ($singleEvents) { $opts['orderBy'] = 'startTime'; }

        if ($start) { $opts['timeMin'] = $start->format(\DateTime::RFC3339); }
        if ($end  ) { $opts['timeMax'] = $end  ->format(\DateTime::RFC3339); }

        $service = new Calendar(self::getClient());
        $events  = $service->events->listEvents($calendarId, $opts);
        return $events;
    }

    /**
     * @param string $calendarId
     * @param string $eventId
     * @return Event
     */
    public static function getEvent($calendarId, $eventId)
    {
        $service = new Calendar(self::getClient());
        return $service->events->get($calendarId, $eventId);
    }

    public static function sync(string $calendarId, ?string $nextSyncToken=null): Events
    {
        $opts = [
            'singleEvents' => true,
            'syncToken'    => $nextSyncToken
        ];

        $service = new Calendar(self::getClient());
        $events  = $service->events->listEvents($calendarId, $opts);
        return $events;
    }

    public static function watch(string $calendarId, string $watch_id): Channel
    {
        $opts  = [
            'id'      => $watch_id,
            'type'    => 'web_hook',
            'address' => BASE_URL.'/notifications',
            'expiration' => strtotime('+10 minute').'000',
            'eventTypes' => 'default'
        ];
        print_r($opts);
        $watch = new Channel($opts);
        print_r($watch);
        $service = new Calendar(self::getClient());
        return $service->events->watch($calendarId, $watch);
    }
}
