<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class MeetingFile extends ActiveRecord
{
    use File;

	protected $tablename = 'meetingFiles';
	protected $committee;

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

	public function validate()
	{
        if ($this->getType() || !$this->getCommittee_id() || !$this->getMeetingDate()) {
            throw new \Exception('missingRequiredFields');
        }

        if (!in_array($this->getType(), self::$types)) {
            throw new \Exception('meetingFiles/invalidType');
        }

		if (!$this->getFilename())  { throw new \Exception('files/missingFilename'); }
		if (!$this->getMime_type()) { throw new \Exception('files/missingMimeType'); }
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getType()         { return parent::get('type'        ); }
	public function getEventId()      { return parent::get('eventId'     ); }
    public function getCommittee_id() { return parent::get('committee_id'); }
	public function getCommittee()    { return parent::getForeignKeyObject(__namespace__.'\Committee', 'committee_id'); }
	public function getMeetingDate($f=null) { return parent::getDateData('meetingDate', $f); }

	public function setType        ($s) { parent::set('type',    $s); }
	public function setEventId     ($s) { parent::set('eventId', $s); }
	public function setCommittee_id($i) { parent::setForeignKeyField (__namespace__.'\Committee', 'committee_id', $i); }
	public function setCommittee   ($o) { parent::setForeignKeyObject(__namespace__.'\Committee', 'committee_id', $o); }
	public function setMeetingDate ($d) { parent::setDateData('meetingDate', $d); }

	public function handleUpdate(array $post)
	{
        $this->setType        ($post['type'        ]);
        $this->setEventId     ($post['eventId'     ]);
        $this->setCommittee_id($post['committee_id']);
        $this->setMeetingDate ($post['meetingDate' ]);
	}
	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function getDirectory()
	{
        return "{$this->getCommittee_id()}/{$this->getCreated('Y/m/d')}/{$this->getType()}";
	}
}
