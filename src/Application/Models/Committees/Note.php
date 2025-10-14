<?php
/**
 * @copyright 2025 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models\Committees;

use Web\Database;

class Note extends \Web\ActiveRecord
{
    protected $tablename = 'committee_notes';
    protected $committee;
    protected $person;

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
     * @param int|string|array $id
     */
    public function __construct($id=null)
    {
        if ($id) {
            if (is_array($id)) { $this->exchangeArray($id); }
            else {
                $db  = Database::getConnection();
                $sql = 'select * from committee_notes where id=?';
                $res = $db->createStatement($sql)->execute([$id]);
                if (count($res)) {
                    $this->exchangeArray($res->current());
                }
                else {
                    throw new \Exception('committees/unknownNote');
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
     *
     * @throws \Exception $e
     */
    public function validate()
    {
        if (!$this->getCommittee_id() || !$this->getPerson_id() || !$this->getNote()) {
            throw new \Exception('missingRequiredFields');
        }
    }

    public function   save() { parent::save(); }
    public function delete() { parent::delete(); }

    public function getId()           { return parent::get('id'); }
    public function getNote()         { return parent::get('note'); }
    public function getCommittee_id() { return parent::get('committee_id'); }
    public function getPerson_id()    { return parent::get('person_id'   ); }
    public function getCommittee()    { return parent::getForeignKeyObject('Application\Models\Committee', 'committee_id'); }
    public function getPerson()       { return parent::getForeignKeyObject('Application\Models\Person',    'person_id'   ); }
    public function getCreated ($f=null) { return parent::getDateData('created',  $f); }
    public function getModified($f=null) { return parent::getDateData('modified', $f); }

    public function setNote($s) { parent::set('note', $s); }
    public function setCommittee_id($i) { parent::setForeignKeyField ('Application\Models\Committee', 'committee_id', $i); }
    public function setCommittee   ($o) { parent::setForeignKeyObject('Application\Models\Committee', 'committee_id', $o); }
    public function setPerson_id   ($i) { parent::setForeignKeyField ('Application\Models\Person',    'person_id',    $i); }
    public function setPerson      ($o) { parent::setForeignKeyObject('Application\Models\Person',    'person_id',    $o); }

    public function __toString() { return parent::get('note'); }
}
