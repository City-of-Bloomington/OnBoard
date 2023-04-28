<?php
/**
 * @copyright 2017-2023 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Web\ActiveRecord;
use Web\Database;
use Web\View;

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
        'application/rtf'                                                         => 'rtf',
               'text/rtf'                                                         => 'rtf'
    ];

    public function validateDatabaseInformation()
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
	public function setMeetingDate (?string $date=null, ?string $format='Y-m-d') { parent::setDateData('meetingDate', $date, $format); }

	public function setIndexed(\DateTime $d)    { $this->data['indexed'   ] = $d->format(ActiveRecord::MYSQL_DATETIME_FORMAT); }
	public function setUpdated_by(int $id)      { $this->data['updated_by'] = $id; }
	public function setUpdatedPerson(Person $p) { $this->data['updated_by'] = (int)$p->getId(); }

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
	public function getDownloadUrl() { return  View::generateUrl('meetingFiles.download').'?meetingFile_id='.$this->getId(); }
	public function getDownloadUri() { return  View::generateUri('meetingFiles.download').'?meetingFile_id='.$this->getId(); }

	/**
	 * Extracts plain text out of a PDF
	 */
	public function extractText(): string
	{
        return shell_exec("pdftotext -enc UTF-8 -nodiag -nopgbrk -eol unix {$this->getFullPath()} -") ?: '';
	}

	public function getSolrFields(): array
	{
        return [
            'id'        => $this->getId(),
            'type'      => $this->getType(),
            'title'     => $this->getTitle() ?: "{$this->getCommittee()->getName()} {$this->getMeetingDate()} {$this->getType()}",
            'url'       => $this->getDownloadUrl(),
            'text'      => $this->extractText(),
            'date'      => $this->getMeetingDate(),
            'changed'   => $this->getUpdated(),
            'committee' => $this->getCommittee()->getName()
        ];
	}
}
