<?php
/**
 * @copyright 2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models\Legislation;

use Application\Models\File;

use Web\ActiveRecord;
use Web\Database;

class LegislationFile extends File
{
    protected $tablename = 'legislationFiles';
    protected $legislation;

    public function validate()
    {
        if (!$this->getLegislation_id()) { throw new \Exception('missingRequiredFields'); }
    }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
    public function getLegislation_id() { return parent::get('legislation_id'); }
    public function getLegislation()    { return parent::getForeignKeyObject(__namespace__.'\Legislation', 'legislation_id'); }

	public function setLegislation_id($i) { parent::setForeignKeyField (__namespace__.'\Legislation', 'legislation_id', $i); }
	public function setLegislation   ($o) { parent::setForeignKeyObject(__namespace__.'\Legislation', 'legislation_id', $o); }
}
