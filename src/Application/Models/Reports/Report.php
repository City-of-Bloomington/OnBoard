<?php
/**
 * @copyright 2017-2025 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);

namespace Application\Models\Reports;

use Application\Models\Committee;
use Application\Models\File;
use Web\ActiveRecord;
use Web\View;

class Report extends File
{
    const VALIDATION_ALL  = 0b1111;
    const VALIDATION_DB   = 0b0001;
    const VALIDATION_FILE = 0b0010;
    public $validation = self::VALIDATION_ALL;

    protected $tablename = 'reports';
    protected $committee;

    public function __construct($id=null)
    {
        if (!$id) {
            $now = new \DateTime();
            $this->data['reportDate'] = $now->format(ActiveRecord::MYSQL_DATETIME_FORMAT);
        }
        parent::__construct($id);
    }

    public function validateDatabaseInformation()
    {
        if (!$this->getTitle() || !$this->getCommittee_id()) {
            throw new \Exception('missingRequiredFields');
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
    public function getTitle       () { return parent::get('title'       ); }
    public function getCommittee_id() { return parent::get('committee_id'); }
	public function getCommittee   () { return parent::getForeignKeyObject('\Application\Models\Committee', 'committee_id'); }
    public function getReportDate($format=null) { return parent::getDateData('reportDate', $format); }

    public function setTitle     ($s) { parent::set('title', $s ); }
    public function setReportDate(string $d, string $format) { parent::setDateData('reportDate', $d, $format); }
	public function setCommittee_id        ($i) { parent::setForeignKeyField ('\Application\Models\Committee', 'committee_id', $i); }
	public function setCommittee (Committee $o) { parent::setForeignKeyObject('\Application\Models\Committee', 'committee_id', $o); }

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	public function toArray()
	{
        return [
            'id'        => (int)$this->getId(),
            'committee' => $this->getCommittee()->getName(),
            'title'     => $this->getTitle(),
            'date'      => $this->getReportDate(),
            'url'       => View::generateUrl('reports.download', ['id'=>$this->getId()])
        ];
	}

	public function getSolrFields(): array
    {
        $c = $this->getCommittee();
        return [
            'id'        => $this->getId(),
            'type'      => 'Report',
            'title'     => $this->getTitle(),
            'url'       => View::generateUrl('reports.download', ['id'=>$this->getId()]),
            'text'      => $this->extractText(),
            'date'      => $this->getReportDate(),
            'changed'   => $this->getUpdated(),
            'committee' => $c->getName()
        ];
    }
}
