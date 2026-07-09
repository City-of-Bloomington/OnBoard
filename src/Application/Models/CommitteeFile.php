<?php
/**
 * @copyright 2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\Search\Indexable;

use Web\ActiveRecord;
use Web\View;

class CommitteeFile extends File implements Notifications\Model, Indexable
{
    public const TABLENAME = 'committeeFiles';
    protected $committee;

    public static $types = ['Bylaws'];

    /**
     * Whitelist of accepted file types
     */
    public static $mime_types = [
        'application/pdf' => 'pdf'
    ];

    /**
     * Check information not related to the file storage
     */
    public function validateDatabaseInformation()
    {
        if (!$this->getType() || !$this->getCommittee_id()) {
            throw new \Exception('missingRequiredFields');
        }

        if (!in_array($this->getType(), self::$types)) {
            throw new \Exception('meetingFiles/invalidType');
        }
    }

    /**
     * @throws \Exception
     */
    public function validate()
    {
        $this->validateDatabaseInformation();

        parent::validate();
    }

    public function getType()         { return parent::get('type'        ); }
    public function getTitle()        { return parent::get('title'       ); }
    public function getCommittee_id():int { return (int)parent::get('committee_id'); }
    public function getCommittee()    { return parent::getForeignKeyObject(__namespace__.'\Committee', 'committee_id'); }

    public function setType        ($s) { parent::set('type',    $s); }
    public function setTitle       ($s) { parent::set('title',   $s); }
    public function setCommittee_id($i) { parent::setForeignKeyField (__namespace__.'\Committee', 'committee_id', $i); }
    public function setCommittee   ($o) { parent::setForeignKeyObject(__namespace__.'\Committee', 'committee_id', $o); }


    public function getDisplayFilename(): string
    {
        $c    = $this->getCommittee();
        $name = $c->getCode() ? $c->getCode() : $c->getName();
        $name = parent::createValidFilename($name);
        return "$name-{$this->getType()}.{$this->getExtension()}";
    }

    public function getData(): array
    {
        $data = $this->data;
        if (empty($data['url'])) {
            $data['url'] = View::generateUrl('committees.files.download', ['file_id'=>$data['id']]);
        }
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
     */
    public function getDirectory(): string
    {
        return $this->getCreated('Y/m/d');
    }

    public function getSolrFields(): array
    {
        $c = $this->getCommittee();
        return [
            'id'        => $this->getId(),
            'type'      => $this->getType(),
            'title'     => $this->getTitle() ?: "{$c->getName()} {$this->getType()}",
            'url'       => $this->data['url'] ?? View::generateUrl('committees.files.download', ['file_id'=>$this->getId()]),
            'text'      => $this->extractText(),
            'date'      => $this->getCreated(),
            'changed'   => $this->getUpdated(),
            'committee' => $c->getName()
        ];
    }
}
