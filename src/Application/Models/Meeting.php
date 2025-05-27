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
                $db = Database::getConnection();
                if (ActiveRecord::isId($id)) {
                    $sql = 'select * from meetings where id=?';
                }
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
    public function getStart  ($f=null)  { return parent::getDateData('start', $f); }
    public function getEnd    ($f=null)  { return parent::getDateData('end',   $f); }
    public function getCreated($f=null)  { return parent::getDateData('start', $f); }
    public function getUpdated($f=null)  { return parent::getDateData('end',   $f); }

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
    public function getMeetingFiles()
    {
        $table = new MeetingFilesTable();
        return $table->find(['meeting_id'=>$this->getId()]);
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

        $sql    = 'update meetings set attendanceNotes=?';
        $update = $pdo->prepare($sql);
        $update->execute([$notes]);
    }
}
