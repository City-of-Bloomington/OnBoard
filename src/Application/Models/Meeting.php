<?php
/**
 * @copyright 2024-2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;

class Meeting extends ActiveRecord
{
    protected $tablename = 'meetings';
    protected $committee;

    /**
     * Populates the object with data
     *
     * Passing in an associative array of data will populate this object without
     * hitting the database.
     *
     * Passing in a scalar will load the data from the database.
     * This will load all fields in the table as properties of this class.
     * You may want to replace this with, or add your own extra, custom loading
     *
     * @param int|string|array $id (ID, email, username)
     */
    public function __construct($id=null)
    {
        if ($id) {
            if (is_array($id)) {
                $this->exchangeArray($id);
            }
            else {
                $db     = Database::getConnection();
                $sql    = 'select * from meetings where id=?';
                $result = $db->createStatement($sql)->execute([$id]);
                if (count($result)) {
                    $this->exchangeArray($result->current());
                }
                else {
                    throw new \Exception('meetings/unknownMeeting');
                }
            }
        }
        else {
            // This is where the code goes to generate a new, empty instance.
            // Set any default values for properties that need it here
        }
    }

    /**
     * Throws an exception if anything's wrong
     * @throws Exception $e
     */
    public function validate()
    {
        if (!$this->getCommittee_id()) { throw new \Exception('missingCommittee'); }
        if (!$this->getStart())        { throw new \Exception('missingStart'); }
    }

    public function save() { parent::save(); }

    public function delete()
    {
        $files = $this->getMeetingFiles();
        foreach ($files as $f) { $f->delete(); }

        parent::delete();
    }

    //----------------------------------------------------------------
    // Generic Getters & Setters
    //----------------------------------------------------------------
    public function getId()              { return parent::get('id'             ); }
    public function getTitle()           { return parent::get('title'          ); }
    public function getEventId()         { return parent::get('eventId'        ); }
    public function getLocation()        { return parent::get('location'       ); }
    public function getHtmlLink()        { return parent::get('htmlLink'       ); }
    public function getAttendanceNotes() { return parent::get('attendanceNotes'); }
    public function getCommittee_id()    { return parent::get('committee_id'   ); }
    public function getCommittee()       { return parent::getForeignKeyObject(__namespace__.'\Committee', 'committee_id'); }
    public function getStart  ($f=null)  { return parent::getDateData('start',   $f); }
    public function getEnd    ($f=null)  { return parent::getDateData('end',     $f); }
    public function getCreated($f=null)  { return parent::getDateData('created', $f); }
    public function getUpdated($f=null)  { return parent::getDateData('updated', $f); }

    public function setTitle          ($s) { parent::set('title',           $s); }
    public function setEventId        ($s) { parent::set('eventId',         $s); }
    public function setLocation       ($s) { parent::set('location',        $s); }
    public function setHtmlLink       ($s) { parent::set('htmlLink',        $s); }
    public function setAttendanceNotes($s) { parent::set('attendanceNotes', $s); }
    public function setCommittee_id($i) { parent::setForeignKeyField (__namespace__.'\Committee', 'committee_id', $i); }
    public function setCommittee   ($o) { parent::setForeignKeyObject(__namespace__.'\Committee', 'committee_id', $o); }
    public function setStart(?string $dt=null, ?string $format='Y-m-d H:i:s') { parent::setDateData('start', $dt, $format); }
    public function setEnd  (?string $dt=null, ?string $format='Y-m-d H:i:s') { parent::setDateData('end',   $dt, $format); }

    //----------------------------------------------------------------
    // Custom Functions
    //----------------------------------------------------------------
    public function getMeetingFiles(): array
    {
        $t = new MeetingFilesTable();
        $r = $t->find(['meeting_id'=>$this->getId()]);
        return $r['rows'];
    }

    public function isSafeToDelete(): bool
    {
        $sql = 'select count(*) from meetingFiles where meeting_id=?';
        $db  = Database::getConnection();
        $res = $db->createStatement($sql)->execute([$this->getId()]);
        $files = $res->getResource()->fetchColumn();

        return !$files && !$this->getEventId();
    }

    public function hasAttendance(): bool
    {
        $sql = 'select count(*) from meeting_attendance where meeting_id=?';
        $db  = Database::getConnection();
        $res = $db->createStatement($sql)->execute([$this->getId()]);
        $c   = $res->getResource()->fetchColumn();
        return $c ? true : false;
    }

    public function getAttendance(): array
    {
        $sql = "select x.id     as meeting_id,
                       a.status as attendance,
                       m.id     as member_id,
                       m.committee_id,
                       m.seat_id,
                       m.term_id,
                       m.person_id,
                       m.startDate,
                       m.endDate,
                       p.firstname,
                       p.lastname
                from meetings x
                join members  m on x.committee_id=m.committee_id and m.startDate<x.start and (m.endDate is null or m.endDate>x.start)
                join people   p on p.id=m.person_id
                left join meeting_attendance a on a.meeting_id=x.id and a.member_id=m.id
                where x.id=?";
        $pdo = Database::getConnection()->getDriver()->getConnection()->getResource();
        $q   = $pdo->prepare($sql);
        $q->execute([$this->getId()]);
        return $q->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function saveAttendance(array $attendance, ?string $notes=null)
    {
        $pdo = Database::getConnection()->getDriver()->getConnection()->getResource();
        $sql = 'delete from meeting_attendance where meeting_id=?';
        $pdo->prepare($sql)->execute([$this->getId()]);

        $sql    = 'insert meeting_attendance values(:meeting_id, :member_id, :status)';
        $insert = $pdo->prepare($sql);
        foreach ($attendance as $row) { $insert->execute($row); }

        $sql    = 'update meetings set attendanceNotes=? where id=?';
        $update = $pdo->prepare($sql);
        $update->execute([$notes, $this->getId()]);
    }

    private static $LOCATION_MAP = [
        // Google Calendar Resource   => Human Readable Location
        '|^(.+)?Atrium(.+)?$|'                                      => "City Hall Showers\n401 N Morton ST\nAtrium",
        '|^(.+)?Council Chambers(.+)?$|'                            => "City Hall Showers\n401 N Morton ST\nCouncil Chambers Room 115",
        '|^(.+)?Kelly Conference Room(.+)?$|'                       => "City Hall Showers\n401 N Morton ST\nKelly Conference Room 155",
        '|^(.+)?Lemon Conference Room(.+)?$|'                       => "City Hall Showers\n401 N Morton ST\nLemon Conference Room 125",
        '|^(.+)?McCloskey Conference Room(.+)?$|'                   => "City Hall Showers\n401 N Morton ST\nMcCloskey Conference Room 135",
        '|^(.+)?Showers Plaza(.+)?$|'                               => "City Hall Showers\n401 N Morton ST",
        '|^(.+)?Allison Conference Room(.+)?$|'                     => "City Hall Showers\n401 N Morton ST\nAllison Conference Room 225",
        '|^.+Dunlap Conference Room.+$|'                            => "City Hall Showers\n401 N Morton ST\nDunlap Conference Room 235",
        '|^(.+)?Hooker(.+)?$|'                                      => "City Hall Showers\n401 N Morton ST\nHooker Conference Room 245",
        '|^(.+)?ITS Training Room(.+)?$|'                           => "City Hall Showers\n401 N Morton ST\nTraining Room",
        '|^(.+)?Banneker - Gymnasium(.+)?$|'                        => "Banneker Community Center\n930 W 7th ST\nGymnasium",
        '|^(.+)?Banneker - Kitchen(.+)?$|'                          => "Banneker Community Center\n930 W 7th ST\nKitchen",
        '|^(.+)?Family Resource Center(.+)?$|'                      => "Banneker Community Center\n930 W 7th ST\nFamily Resource Center (Third Floor)",
        '|^(.*)?Banneker Community Center(.+)?$|'                   => "Banneker Community Center\n930 W 7th ST",
        '|^(.+)?Blucher Poole Conference Room(.+)?$|'               => "Blucher Poole\n5555 N Bottom RD\nConference Room",
        '|^(.+)?Dillman Road Conference Room(.+)?$|'                => "Dillman Water Treatment Plant\n100 W Dillman RD\nConference Room",
        '|^(.+)?BPD Detective Library Conference Room(.+)?$|'       => "Police Headquarters\n220 E 3rd ST\nDetective Library Conference Room",
        '|^(.+)?BPD Headquarters Training Room(.+)?$|'              => "Police Headquarters\n220 E 3rd ST\nTraining Room",
        '|^(.+)?BPD Firing Range(.+)?$|'                            => "Public Safety Training Center\n3230 S Walnut ST\nFiring Range",
        '|^(.+)?Fire Department Training Tower(.+)?$|'              => "Public Safety Training Center\n3230 S Walnut ST\nFire Training Tower",
        '|^(.+)?BPD Firing Range Class Room(.+)?$|'                 => "Public Safety Training Center\n3230 S Walnut ST\nFiring Range Classroom",
        '|^(.+)?Police Substation Community Room(.+)?$|'            => "Police Substation\n245 W Grimes LN\nCommunity Room",
        '|^(.+)?Mill 1(.+)?$|'                                      => "The Mill\n642 N Madison ST\nRoom 1",
        '|^(.+)?Mill 2(.+)?$|'                                      => "The Mill\n642 N Madison ST\nRoom 2",
        '|^(.+)?Mill 3(.+)?$|'                                      => "The Mill\n642 N Madison ST\nRoom 3",
        '|^(.+)?Utilities Administration Conference Room(.+)?$|'    => "Utilities Service Center\n600 E Miller DR\nAdministration Conference Room",
        '|^(.+)?Utilities Board Room(.+)?$|'                        => "Utilities Service Center\n600 E Miller DR\nBoard Room",
        '|^(.+)?Utilities Engineering Large Conference Room(.+)?$|' => "Utilities Service Center\n600 E Miller DR\nEngineering Large Conference Room",
        '|^(.+)?Utilities Engineering Small Conference Room(.+)?$|' => "Utilities Service Center\n600 E Miller DR\nEngineering Small Conference Room",
        '|^.+?Animal Shelter.+Education.+?$|'                       => "Animal Shelter\n3410 S Walnut ST\nEducation Room",
        '|^.+?Animal Shelter.+Admin.+?$|'                           => "Animal Shelter\n3410 S Walnut ST\nShelter Admin",
        '|^.+?Animal Shelter.+?$|'                                  => "Animal Shelter\n3410 S Walnut ST",
        '|https://bloomington\.zoom\.us/./\d+\?pwd=\w+|'            => '',
        '|\, Bloomington\, IN.+$|'                                  => '',
        '|\,\s|'                                                    => "\n"
    ];

    public function translated_location(): string
    {
        return preg_replace(array_keys(self::$LOCATION_MAP), array_values(self::$LOCATION_MAP), $this->getLocation() ?? '');
    }
}
