<?php
/**
 * @copyright 2017-2024 City of Bloomington, Indiana
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
    protected $meeting;

    public static $types = ['Agenda', 'Memorandum', 'Minutes', 'Packet'];

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
        if (!$this->getType() || !$this->getMeeting_id()) {
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
    public function getType()       { return parent::get('type'        ); }
    public function getTitle()      { return parent::get('title'       ); }
    public function getMeeting_id() { return parent::get('meeting_id'); }
    public function getMeeting()    { return parent::getForeignKeyObject(__namespace__.'\Meeting', 'meeting_id'); }

    public function setType      ($s) { parent::set('type',    $s); }
    public function setTitle     ($s) { parent::set('title',   $s); }
    public function setMeeting_id($i) { parent::setForeignKeyField (__namespace__.'\Meeting', 'meeting_id', $i); }
    public function setMeeting   ($o) { parent::setForeignKeyObject(__namespace__.'\Meeting', 'meeting_id', $o); }

    public function setIndexed(\DateTime $d)    { $this->data['indexed'   ] = $d->format(ActiveRecord::MYSQL_DATETIME_FORMAT); }
    public function setUpdated_by(int $id)      { $this->data['updated_by'] = $id; }
    public function setUpdatedPerson(Person $p) { $this->data['updated_by'] = (int)$p->getId(); }

    //----------------------------------------------------------------
    // Custom Functions
    //----------------------------------------------------------------
    public function getCommittee(): Committee
    {
        return $this->getMeeting()->getCommittee();
    }

    public function getDisplayFilename(): string
    {
        $m    = $this->getMeeting();
        $c    = $m->getCommittee();
        $name = $c->getCode() ? $c->getCode() : $c->getName();
        $name = parent::createValidFilename($name);
        return "$name-{$m->getStart('Ymd')}-{$this->getType()}.{$this->getExtension()}";
    }

    public function getData(): array
    {
        $m    = $this->getMeeting();
        $c    = $m->getCommittee();

        $data = $this->data;
        $data['url'      ] = $this->getDownloadUrl();
        $data['committee'] = $c->getName();
        return $data;
    }

    /**
     * Returns the partial path of the file, relative to /data/files
     *
     * Implementations of this class will usually override this function
     * with their own custom scheme for the directory structure.
     * This implementation should be a good enough default that most
     * of the time, we won't need to override it.
     */
    public function getDirectory(): string
    {
        return $this->getMeeting()->getStart('Y/m/d');
    }

    public function getDownloadUrl():string { return  View::generateUrl('meetingFiles.download', ['meetingFile_id'=>$this->getId()]); }
    public function getDownloadUri():string { return  View::generateUri('meetingFiles.download', ['meetingFile_id'=>$this->getId()]); }

    public function getSolrFields(): array
    {
        $m = $this->getMeeting();
        $c = $m->getCommittee();
        return [
            'id'        => $this->getId(),
            'type'      => $this->getType(),
            'title'     => $this->getTitle() ?: "{$c->getName()} {$m->getStart('Y-m-d')} {$this->getType()}",
            'url'       => $this->getDownloadUrl(),
            'text'      => $this->extractText(),
            'date'      => $this->getMeeting()->getStart(),
            'changed'   => $this->getUpdated(),
            'committee' => $c->getName()
        ];
    }
}
