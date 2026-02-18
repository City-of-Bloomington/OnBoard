<?php
/**
 * @copyright 2017-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models\Legislation;

use Application\Models\File;

use Web\ActiveRecord;
use Web\Database;

class LegislationFile extends File
{
    protected $tablename = 'legislationFiles';
    protected $legislation;

    /**
     * Check information not related to the file storage
     */
    public function validateDatabaseInformation()
    {
        if (!$this->getLegislation_id()) { throw new \Exception('missingRequiredFields'); }
    }

    public function validate()
    {
        $this->validateDatabaseInformation();

        parent::validate();
    }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
    public function getLegislation_id() { return parent::get('legislation_id'); }
    public function getLegislation()    { return parent::getForeignKeyObject(__namespace__.'\Legislation', 'legislation_id'); }

	public function setLegislation_id($i) { parent::setForeignKeyField (__namespace__.'\Legislation', 'legislation_id', $i); }
	public function setLegislation   ($o) { parent::setForeignKeyObject(__namespace__.'\Legislation', 'legislation_id', $o); }


	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------

	public function getSolrFields(): array
    {
        $l = $this->getLegislation();
        $c = $l->getCommittee();
        return [
            'id'        => $this->getId(),
            'type'      => $l->getType(),
            'title'     => $l->getTitle(),
            'url'       => \Web\View::generateUrl('legislationFiles.download', ['legislationFile_id'=>$this->getId()]),
            'text'      => $this->extractText(),
            'date'      => $this->getCreated(),
            'changed'   => $this->getUpdated(),
            'committee' => $c->getName()
        ];
    }

}
