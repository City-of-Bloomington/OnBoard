<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;

class MeetingFile extends File
{
    const VALIDATION_ALL  = 0b1111;
    const VALIDATION_DB   = 0b0001;
    const VALIDATION_FILE = 0b0010;
    public $validation = self::VALIDATION_ALL;

	protected $tablename = 'meetingFiles';
	protected $committee;
	protected $event;

	public static $types = ['Agenda', 'Minutes', 'Packet'];

	/**
	 * Whitelist of accepted file types
	 */
    public static $mime_types = [
        'application/msword'                                                      => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.oasis.opendocument'                                      => 'odf',
        'application/vnd.oasis.opendocument.text'                                 => 'odt',
        'application/pdf'                                                         => 'pdf',
        'application/rtf'                                                         => 'rtf'
    ];

    private function validateDatabaseInformation()
    {
        $committee = $this->getCommittee();
        if ($committee->getCalendarId()) {
            $event = $this->getEvent();
            if ($event) {
                // Make sure the meetingDate matches the event date
                $startDate = $event->start->dateTime
                    ? new \DateTime($event->start->dateTime)
                    : new \DateTime($event->start->date);
                if ($this->getMeetingDate()) {
                    if ($this->getMeetingDate('Y-m-d') != $startDate->format('Y-m-d')) {
                        throw new \Exception('meetingFiles/dateMismatch');
                    }
                }
                else {
                    $this->setMeetingDate($startDate->format('Y-m-d'));
                }
            }
        }

        if (!$this->getType() || !$this->getCommittee_id() || !$this->getMeetingDate()) {
            throw new \Exception('missingRequiredFields');
        }

        if (!in_array($this->getType(), self::$types)) {
            throw new \Exception('meetingFiles/invalidType');
        }

    }

	public function validate()
	{
        if ($this->validation & self::VALIDATION_DB) {
            $this->validateDatabaseInformation();
        }

		if ($this->validation & self::VALIDATION_FILE) {
            if (!$this->getFilename())  { throw new \Exception('files/missingFilename'); }
            if (!$this->getMime_type()) { throw new \Exception('files/missingMimeType'); }
        }
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getType()         { return parent::get('type'        ); }
	public function getTitle()        { return parent::get('title'       ); }
	public function getEventId()      { return parent::get('eventId'     ); }
    public function getCommittee_id() { return parent::get('committee_id'); }
	public function getCommittee()    { return parent::getForeignKeyObject(__namespace__.'\Committee', 'committee_id'); }
	public function getMeetingDate($f=null) { return parent::getDateData('meetingDate', $f); }

	public function setType        ($s) { parent::set('type',    $s); }
	public function setTitle       ($s) { parent::set('title',   $s); }
	public function setEventId     ($s) { parent::set('eventId', $s); }
	public function setCommittee_id($i) { parent::setForeignKeyField (__namespace__.'\Committee', 'committee_id', $i); }
	public function setCommittee   ($o) { parent::setForeignKeyObject(__namespace__.'\Committee', 'committee_id', $o); }
	public function setMeetingDate ($d) { parent::setDateData('meetingDate', $d); }

	public function handleUpdate(array $post, array $file=null)
	{
        $fields = ['type', 'title', 'eventId', 'committee_id', 'meetingDate'];
        foreach ($fields as $f) {
            if (isset($post[$f])) {
                $set = 'set'.ucfirst($f);
                $this->$set($post[$f]);
            }
        }

        // Before we save the file, make sure all the database information is correct
        $this->validateDatabaseInformation();

        // If they are editing an existing document, they do not need to upload a new file
        if ($file) {
            $this->setFile($file);
        }
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * @override
	 * @return string
	 */
	public function getDisplayFilename()
	{
        $committee = $this->getCommittee();
        $name = $committee->getCode()
                ? $committee->getCode()
                : $committee->getName();
        $name = parent::createValidFilename($name);
        return "$name-{$this->getMeetingDate('Ymd')}-{$this->getType()}.{$this->getExtension()}";
	}

	/**
	 * @return array
	 */
	public function getData() {
        $data = $this->data;
        $data['url'      ] = $this->getDownloadUrl();
        $data['committee'] = $this->getCommittee()->getName();
        return $data;
    }

	/**
	 * Returns the partial path of the file, relative to /data/files
	 *
	 * Implementations of this class will usually override this function
	 * with their own custom scheme for the directory structure.
	 * This implementation should be a good enough default that most
	 * of the time, we won't need to override it.
	 *
	 * @return string
	 */
	public function getDirectory()
	{
		return $this->getMeetingDate('Y/m/d');
	}

	public function getEvent()
	{
        if (!$this->event && $this->getEventId()) {
            $this->event = GoogleGateway::getEvent(
                $this->getCommittee()->getCalendarId(),
                $this->getEventId()
            );
        }
        return $this->event;
	}

	/**
	 * @return string
	 */
	public function getDownloadUrl() { return BASE_URL.'/meetingFiles/download?meetingFile_id='.$this->getId(); }
	public function getDownloadUri() { return BASE_URI.'/meetingFiles/download?meetingFile_id='.$this->getId(); }
}
